<?php
/**
 * Application configuration.
 *
 * Environment-specific database settings can be kept in config/local.php,
 * which is excluded from version control. Without it, local XAMPP defaults
 * are used.
 *
 * Do not commit real production credentials to version control.
 */

declare(strict_types=1);

$localConfigPath = __DIR__ . '/local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];

if (!is_array($localConfig)) {
    throw new UnexpectedValueException('config/local.php must return an array.');
}

/* ---------------------------------------------------------------- database */
define('DB_HOST', (string) ($localConfig['DB_HOST'] ?? '127.0.0.1'));
define('DB_PORT', (int) ($localConfig['DB_PORT'] ?? 3307));
define('DB_NAME', (string) ($localConfig['DB_NAME'] ?? 'ngo'));
define('DB_USER', (string) ($localConfig['DB_USER'] ?? 'root'));
define('DB_PASS', (string) ($localConfig['DB_PASS'] ?? ''));
define('DB_CHARSET', 'utf8mb4');

/* ------------------------------------------------------------------- paths */
/** Filesystem root of the public site (no trailing slash). */
define('BASE_PATH', dirname(__DIR__));

/** Where admin-uploaded images are written, relative to BASE_PATH. */
define('UPLOAD_DIR', 'assets/img/uploads');
define('UPLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads');

/** Largest image the admin will accept, in bytes. */
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024);

/* --------------------------------------------------------------- behaviour */
/**
 * Show PHP errors on screen. Turn this off on the live site — a stack trace
 * from a failed query names the database and the table layout.
 */
define('DEBUG_MODE', (bool) ($localConfig['DEBUG_MODE'] ?? true));

date_default_timezone_set('Asia/Kolkata');

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
