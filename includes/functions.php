<?php
/**
 * Shared helper layer.
 *
 * Every public page and every admin screen loads this file. It is the only
 * place that talks to PDO directly, so a change to how queries are run or how
 * output is escaped happens once rather than in forty templates.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

/* ==========================================================================
   QUERY HELPERS
   --------------------------------------------------------------------------
   All three take a parameter array and prepare the statement. There is no
   variant that interpolates a value into SQL, deliberately — the absence of
   one is what keeps injection out of the codebase.
   ========================================================================== */

/**
 * Runs a statement and returns it. Use for INSERT / UPDATE / DELETE.
 */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}

/**
 * Every matching row, as an array of associative arrays.
 */
function rows(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

/**
 * The first matching row, or null when there is none.
 */
function row(string $sql, array $params = []): ?array
{
    $result = q($sql, $params)->fetch();

    return $result === false ? null : $result;
}

/**
 * The first column of the first row — for COUNT(*) and similar.
 */
function val(string $sql, array $params = [], mixed $default = null): mixed
{
    $result = q($sql, $params)->fetchColumn();

    return $result === false ? $default : $result;
}

/**
 * The id MySQL assigned to the row just inserted.
 */
function last_id(): int
{
    return (int) db()->lastInsertId();
}

/**
 * The available groups used to organise team members.
 */
function team_sections(): array
{
    return ['Administrative Body', 'Head Members', 'General Body', 'Volunteer Team'];
}

/**
 * Creates the participant table when the database schema has not been
 * installed yet.
 */
function ensure_participants_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS participants (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            full_name       VARCHAR(255) NOT NULL,
            mobile          VARCHAR(10) NOT NULL,
            email           VARCHAR(190) NOT NULL,
            password_hash   VARCHAR(255) NULL,
            dob             DATE NOT NULL,
            gender          VARCHAR(20) NOT NULL,
            marital_status  VARCHAR(20) NOT NULL,
            guardian_name   VARCHAR(255) NOT NULL,
            aadhar          VARCHAR(12) NOT NULL,
            category        VARCHAR(40) NOT NULL,
            blood_group     VARCHAR(3) NULL,
            occupation      VARCHAR(80) NOT NULL,
            address         TEXT NOT NULL,
            pincode         VARCHAR(6) NOT NULL,
            state           VARCHAR(80) NOT NULL,
            district        VARCHAR(80) NOT NULL,
            designation     VARCHAR(80) NOT NULL,
            profile_photo   VARCHAR(255) NULL,
            status          TINYINT(1) NOT NULL DEFAULT 0,
            submitted_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_participants_status (status, submitted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

/* ==========================================================================
   OUTPUT
   ========================================================================== */

/**
 * Escapes a value for HTML. Every piece of database content goes through this
 * on the way to the page — the content is editable by an admin, and an admin
 * account is exactly what an attacker aims for.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Renders stored rich text. Only for fields edited through the WYSIWYG, where
 * markup is the point; never for a plain field.
 */
function raw(mixed $value): string
{
    return (string) ($value ?? '');
}

/**
 * Truncates on a word boundary, for card excerpts.
 */
function excerpt(?string $text, int $length = 140): string
{
    $text = trim(strip_tags((string) $text));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    $cut = mb_substr($text, 0, $length);
    $gap = mb_strrpos($cut, ' ');

    return rtrim($gap === false ? $cut : mb_substr($cut, 0, $gap), " ,.;:-") . '…';
}

/**
 * Formats a date for display, tolerating nulls and unparseable values.
 */
function fmt_date(?string $date, string $format = 'd M Y'): string
{
    if (empty($date) || $date === '0000-00-00' || str_starts_with($date, '0000-00-00')) {
        return '';
    }

    $ts = strtotime($date);

    return $ts === false ? '' : date($format, $ts);
}

/* ==========================================================================
   URLS AND ASSETS
   --------------------------------------------------------------------------
   The site can sit at the domain root or in a subfolder, and admin screens
   live one directory deeper than public ones. Working out the web root once,
   from the document root, means neither of those needs a relative `../` that
   breaks the moment a file moves.
   ========================================================================== */

/**
 * A root-relative URL for a path inside the site.
 */
function base_url(string $path = ''): string
{
    static $base = null;

    if ($base === null) {
        $docRoot = str_replace('\\', '/', rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/'));
        $root    = str_replace('\\', '/', BASE_PATH);

        $base = ($docRoot !== '' && str_starts_with($root, $docRoot))
            ? rtrim(substr($root, strlen($docRoot)), '/')
            : '';
    }

    return $base . '/' . ltrim($path, '/');
}

/**
 * A URL for a stored image, falling back to a placeholder when the row has no
 * image or the file has gone missing. A broken <img> on a live NGO site looks
 * like neglect; a placeholder looks like a gap waiting to be filled.
 */
function img_url(?string $path, string $fallback = 'assets/img/logo/study.png'): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return base_url($fallback);
    }

    // Already absolute — an externally hosted image.
    if (preg_match('#^(https?:)?//#i', $path)) {
        return $path;
    }

    $relative = ltrim($path, '/');

    if (!is_file(BASE_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative))) {
        return base_url($fallback);
    }

    return base_url($relative);
}

/**
 * The current script's filename, used to mark the active nav item.
 */
function current_page(): string
{
    return basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/* ==========================================================================
   SITE SETTINGS
   --------------------------------------------------------------------------
   One key/value table holds the organisation's name, logo, contact details
   and social profiles. It is read on nearly every request, so the whole table
   is fetched once per request and served from memory after that.
   ========================================================================== */

function settings(): array
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $cache = [];

    try {
        foreach (rows('SELECT setting_key, setting_value FROM site_settings') as $r) {
            $cache[$r['setting_key']] = $r['setting_value'];
        }
    } catch (PDOException $e) {
        // Before the schema is installed this table does not exist yet. The
        // page should still render with its defaults rather than white-screen.
        error_log('site_settings unavailable: ' . $e->getMessage());
    }

    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings();

    return isset($all[$key]) && $all[$key] !== '' ? (string) $all[$key] : $default;
}

/**
 * Splits the contact phone setting into non-empty phone numbers.
 */
function contact_phone_numbers(string $value): array
{
    $numbers = preg_split('/\R/', $value);

    return array_values(array_filter(
        array_map('trim', $numbers === false ? [] : $numbers),
        static fn(string $number): bool => $number !== ''
    ));
}

/**
 * Writes a setting, creating the key if it does not exist yet.
 */
function set_setting(string $key, string $value): void
{
    q(
        'INSERT INTO site_settings (setting_key, setting_value)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
        [$key, $value]
    );
}

/* ==========================================================================
   PAGE SECTIONS
   --------------------------------------------------------------------------
   The headings, taglines and intro paragraphs that sit above a list of cards.
   They are not entities in their own right, but they are still copy an admin
   needs to change, so they live in one generic table keyed by page + slot
   rather than in fifteen near-identical ones.
   ========================================================================== */

function sections(string $page): array
{
    static $cache = [];

    if (isset($cache[$page])) {
        return $cache[$page];
    }

    $out = [];

    try {
        $found = rows(
            'SELECT section_key, tagline, heading, subheading, body, image, extra
               FROM page_sections
              WHERE page = ? AND status = 1',
            [$page]
        );

        foreach ($found as $r) {
            $out[$r['section_key']] = $r;
        }
    } catch (PDOException $e) {
        error_log('page_sections unavailable: ' . $e->getMessage());
    }

    return $cache[$page] = $out;
}

/**
 * One field from one section, with a fallback so a template never renders a
 * blank heading just because the row has not been created yet.
 */
function section(string $page, string $key, string $field = 'heading', string $default = ''): string
{
    $all = sections($page);

    return isset($all[$key][$field]) && $all[$key][$field] !== ''
        ? (string) $all[$key][$field]
        : $default;
}

/* ==========================================================================
   FORM SECURITY
   ========================================================================== */

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function csrf_token(): string
{
    start_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * True when the submitted token matches the one in the session. hash_equals
 * rather than === so the comparison does not leak the token through timing.
 */
function csrf_valid(): bool
{
    start_session();

    $sent = (string) ($_POST['csrf_token'] ?? '');

    return $sent !== ''
        && !empty($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $sent);
}

/**
 * Stops a POST that has no valid token. Called at the top of every handler
 * that writes to the database.
 */
function require_csrf(): void
{
    if (!csrf_valid()) {
        http_response_code(419);
        exit('This form has expired. Please go back, reload the page and try again.');
    }
}

/* ==========================================================================
   FLASH MESSAGES
   ========================================================================== */

function flash(string $message, string $type = 'success'): void
{
    start_session();
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function take_flash(): array
{
    start_session();

    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

/* ==========================================================================
   IMAGE UPLOADS
   ========================================================================== */

/**
 * Validates and stores one uploaded image, returning the path to save in the
 * database (relative to the site root), or null when no file was sent.
 *
 * The type is taken from the file's actual contents via finfo, never from the
 * browser-supplied `type` or from the extension — both are attacker-chosen.
 * The stored name is generated rather than derived from the original, so a
 * file called `shell.php.jpg` cannot survive the trip.
 *
 * @param array|null $file  One entry from $_FILES.
 * @param string     $group Subfolder to file it under, e.g. 'team'.
 *
 * @throws RuntimeException when a file was sent but cannot be accepted.
 */
function upload_image(?array $file, string $group = 'general'): ?string
{
    if ($file === null || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_message((int) $file['error']));
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('The upload could not be verified. Please try again.');
    }

    if ($file['size'] > UPLOAD_MAX_BYTES) {
        throw new RuntimeException(sprintf(
            'That image is %s. The limit is %s.',
            format_bytes((int) $file['size']),
            format_bytes(UPLOAD_MAX_BYTES)
        ));
    }

    // SVG is deliberately absent: it is a document format that can carry
    // script, and it would be served from the site's own origin.
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WEBP and GIF images can be uploaded.');
    }

    // A file can pass the MIME sniff and still not be a usable image.
    if (@getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('That file is not a readable image.');
    }

    $group  = preg_replace('/[^a-z0-9_-]/', '', strtolower($group)) ?: 'general';
    $dir    = UPLOAD_PATH . DIRECTORY_SEPARATOR . $group;

    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('The upload folder could not be created.');
    }

    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];

    if (!move_uploaded_file($file['tmp_name'], $dir . DIRECTORY_SEPARATOR . $name)) {
        throw new RuntimeException('The image could not be saved to disk.');
    }

    return UPLOAD_DIR . '/' . $group . '/' . $name;
}

/**
 * Removes a previously uploaded file. Scoped to the uploads folder so a bad
 * or crafted path in the database cannot delete anything else.
 */
function delete_upload(?string $path): void
{
    $path = ltrim((string) $path, '/');

    if ($path === '' || !str_starts_with($path, UPLOAD_DIR . '/')) {
        return;
    }

    $full = BASE_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    $real = realpath($full);
    $base = realpath(UPLOAD_PATH);

    if ($real !== false && $base !== false && str_starts_with($real, $base) && is_file($real)) {
        @unlink($real);
    }
}

/**
 * Ensures an uploaded image has the required pixel dimensions.
 *
 * @throws RuntimeException when the uploaded image dimensions do not match.
 */
function validate_uploaded_image_dimensions(string $uploadedImage, int $expectedWidth, int $expectedHeight, string $label): void
{
    $absolutePath = BASE_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadedImage);
    $dimensions = @getimagesize($absolutePath);

    if ($dimensions === false || $dimensions[0] !== $expectedWidth || $dimensions[1] !== $expectedHeight) {
        delete_upload($uploadedImage);
        throw new RuntimeException($label . ' must be exactly ' . $expectedWidth . ' × ' . $expectedHeight . ' pixels.');
    }
}

function upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That image is larger than the server allows.',
        UPLOAD_ERR_PARTIAL                        => 'The upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_TMP_DIR                     => 'The server has no temporary folder for uploads.',
        UPLOAD_ERR_CANT_WRITE                     => 'The server could not write the file to disk.',
        UPLOAD_ERR_EXTENSION                      => 'A server extension blocked the upload.',
        default                                   => 'The image could not be uploaded.',
    };
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }

    return max(1, (int) round($bytes / 1024)) . ' KB';
}

/* ==========================================================================
   MISC
   ========================================================================== */

/**
 * A URL-safe slug, used for post and project addresses.
 */
function slugify(string $text): string
{
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? '';
    $text = trim($text, '-');
    $text = mb_strtolower($text);

    return $text !== '' ? $text : 'item';
}

/**
 * Reads a POST field as trimmed text.
 */
function post(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function post_int(string $key, int $default = 0): int
{
    return isset($_POST[$key]) && $_POST[$key] !== '' ? (int) $_POST[$key] : $default;
}
