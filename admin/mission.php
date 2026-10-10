<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function mission_admin_redirect(): never
{
    header('Location: mission.php');
    exit;
}

function valid_mission_icon(string $icon): bool
{
    return preg_match('#^assets/img/icon/[A-Za-z0-9_.-]+\.svg$#', $icon) === 1;
}

$sectionDefaults = [
    'intro' => [
        'tagline' => 'Our Purpose',
        'heading' => 'Creating a future built on equality and opportunity',
        'body' => 'At JAN KALYAN SANGH FOUNDATION, we believe lasting change begins when communities have the resources, knowledge and confidence to shape their own future.',
        'extra' => '',
    ],
    'mission' => [
        'tagline' => 'Our Mission',
        'heading' => 'Driving social change through sustainable development',
        'body' => 'We are dedicated to improving education, healthcare, environmental conservation and women\'s empowerment by providing communities with the resources they need to build brighter futures.',
        'extra' => 'Our ultimate goal is to create self-reliant, resilient communities that thrive in harmony with their environment.',
        'image' => 'assets/img/icon/accurate-guidance.svg',
    ],
    'vision' => [
        'tagline' => 'Our Vision',
        'heading' => 'A world where every person can reach their potential',
        'body' => 'We envision a future where equality, education and environmental sustainability form the bedrock of thriving communities.',
        'extra' => 'We strive for a society where collaboration drives innovation and communities work together to build lasting solutions for generations to come.',
        'image' => 'assets/img/icon/country.svg',
    ],
    'focus' => [
        'tagline' => 'Our Focus',
        'heading' => 'Turning purpose into action',
        'body' => 'Our work is focused on practical support that helps people build secure, healthy and independent lives.',
        'extra' => '',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_sections') {
        $submitted = $_POST['sections'] ?? [];
        if (!is_array($submitted)) {
            flash('Please submit valid mission page sections.', 'error');
            mission_admin_redirect();
        }

        $values = [];
        $invalid = false;
        foreach ($sectionDefaults as $key => $defaults) {
            $section = $submitted[$key] ?? null;
            if (!is_array($section)) {
                $invalid = true;
                break;
            }

            $values[$key] = [
                'tagline' => trim((string) ($section['tagline'] ?? '')),
                'heading' => trim((string) ($section['heading'] ?? '')),
                'body' => trim((string) ($section['body'] ?? '')),
                'extra' => trim((string) ($section['extra'] ?? '')),
                'image' => trim((string) ($section['image'] ?? $defaults['image'] ?? '')),
                'status' => isset($section['status']) ? 1 : 0,
            ];

            if (
                $values[$key]['tagline'] === ''
                || $values[$key]['heading'] === ''
                || $values[$key]['body'] === ''
                || mb_strlen($values[$key]['tagline']) > 160
                || mb_strlen($values[$key]['heading']) > 255
                || mb_strlen($values[$key]['body']) > 10000
                || mb_strlen($values[$key]['extra']) > 10000
                || (in_array($key, ['mission', 'vision'], true) && !valid_mission_icon($values[$key]['image']))
            ) {
                $invalid = true;
                break;
            }
        }

        if ($invalid) {
            flash('Please check the section copy and make sure required fields are filled in.', 'error');
            mission_admin_redirect();
        }

        try {
            db()->beginTransaction();
            foreach ($values as $key => $section) {
                q(
                    'INSERT INTO page_sections (page, section_key, tagline, heading, body, extra, image, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading),
                         body = VALUES(body), extra = VALUES(extra), image = VALUES(image), status = VALUES(status)',
                    ['mission', $key, $section['tagline'], $section['heading'], $section['body'], $section['extra'], $section['image'] !== '' ? $section['image'] : null, $section['status']]
                );
            }
            db()->commit();
            flash('Mission page sections saved successfully.');
        } catch (PDOException $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            error_log('Mission sections save failed: ' . $exception->getMessage());
            flash('The mission page sections could not be saved. Please try again.', 'error');
        }

        mission_admin_redirect();
    }

    if ($action === 'delete_focus') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            flash('That focus area could not be found.', 'error');
            mission_admin_redirect();
        }

        $deleted = q('DELETE FROM mission_focus_areas WHERE id = ?', [$id])->rowCount();
        flash($deleted > 0 ? 'Focus area deleted successfully.' : 'That focus area could not be found.', $deleted > 0 ? 'success' : 'error');
        mission_admin_redirect();
    }

    if ($action === 'save_focus') {
        $idInput = trim((string) ($_POST['id'] ?? ''));
        $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $icon = trim((string) ($_POST['icon'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '', FILTER_VALIDATE_INT);
        $status = isset($_POST['status']) ? '1' : '0';

        if (
            ($id !== null && ($id === false || $id < 1))
            || $title === ''
            || mb_strlen($title) > 160
            || mb_strlen($description) > 10000
            || !valid_mission_icon($icon)
            || $sortOrder === false
            || $sortOrder < 0
            || $sortOrder > 999999
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Please enter a valid focus area title, description, icon, order and status.', 'error');
            mission_admin_redirect();
        }

        if ($id !== null && row('SELECT id FROM mission_focus_areas WHERE id = ?', [$id]) === null) {
            flash('That focus area could not be found.', 'error');
            mission_admin_redirect();
        }

        try {
            if ($id === null) {
                q(
                    'INSERT INTO mission_focus_areas (title, description, icon, sort_order, status) VALUES (?, ?, ?, ?, ?)',
                    [$title, $description, $icon, $sortOrder, (int) $status]
                );
                flash('Focus area added successfully.');
            } else {
                q(
                    'UPDATE mission_focus_areas SET title = ?, description = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?',
                    [$title, $description, $icon, $sortOrder, (int) $status, $id]
                );
                flash('Focus area updated successfully.');
            }
        } catch (PDOException $exception) {
            error_log('Mission focus area save failed: ' . $exception->getMessage());
            flash('The focus area could not be saved. Please try again.', 'error');
        }

        mission_admin_redirect();
    }

    flash('The requested action is not valid.', 'error');
    mission_admin_redirect();
}

$storedSections = rows(
    'SELECT section_key, tagline, heading, body, extra, image, status
       FROM page_sections
      WHERE page = ?',
    ['mission']
);
$sections = [];
foreach ($storedSections as $section) {
    $sections[$section['section_key']] = $section;
}
foreach ($sectionDefaults as $key => $defaults) {
    $sections[$key] = array_merge($defaults + ['status' => 1], $sections[$key] ?? []);
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM mission_focus_areas WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That focus area could not be found.', 'error');
        mission_admin_redirect();
    }
}

$focusAreas = rows('SELECT * FROM mission_focus_areas ORDER BY sort_order ASC, id ASC');
$messages = take_flash();
$icons = [
    'assets/img/icon/accurate-guidance.svg' => 'Mission',
    'assets/img/icon/student-visa.svg' => 'Education',
    'assets/img/icon/quality-service.svg' => 'Healthcare',
    'assets/img/icon/country.svg' => 'Environment',
    'assets/img/icon/team.svg' => 'Women\'s Empowerment',
];
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#10b981); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(16,185,129,.18);">
                <div>
                    <h3 style="margin:0; font-size:1.8rem; color:#fff;">Mission &amp; Vision</h3>
                    <p class="mb-0 mt-2" style="color:#d1fae5;">Manage the public mission page and its focus areas.</p>
                </div>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#d1fae5; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-compass"></i> Mission Panel
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:24px;">
                    <h4 class="mb-1" style="font-weight:700; color:#0f172a;">Page copy</h4>
                    <p class="text-muted mb-4">Edit headings and descriptions. Uncheck a section to hide it from the public page.</p>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_sections">
                        <div class="row g-4">
                            <?php
                            $sectionLabels = [
                                'intro' => 'Purpose introduction',
                                'mission' => 'Our Mission',
                                'vision' => 'Our Vision',
                                'focus' => 'Our Focus',
                            ];
                            foreach ($sectionLabels as $key => $label):
                                $section = $sections[$key];
                            ?>
                                <div class="col-12">
                                    <div class="p-3" style="border:1px solid #e2e8f0; border-radius:12px;">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                            <h5 class="mb-0"><?= e($label) ?></h5>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sections[<?= e($key) ?>][status]" value="1" id="section-<?= e($key) ?>" <?= (int) $section['status'] === 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="section-<?= e($key) ?>">Show on page</label>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label" for="<?= e($key) ?>-tagline">Tagline</label>
                                                <input class="form-control" id="<?= e($key) ?>-tagline" name="sections[<?= e($key) ?>][tagline]" maxlength="160" value="<?= e($section['tagline']) ?>" required>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label" for="<?= e($key) ?>-heading">Heading</label>
                                                <input class="form-control" id="<?= e($key) ?>-heading" name="sections[<?= e($key) ?>][heading]" maxlength="255" value="<?= e($section['heading']) ?>" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="<?= e($key) ?>-body">Description</label>
                                                <textarea class="form-control" id="<?= e($key) ?>-body" name="sections[<?= e($key) ?>][body]" rows="3" maxlength="10000" required><?= e($section['body']) ?></textarea>
                                            </div>
                                            <?php if (in_array($key, ['mission', 'vision'], true)): ?>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="<?= e($key) ?>-image">Icon</label>
                                                    <select class="form-select" id="<?= e($key) ?>-image" name="sections[<?= e($key) ?>][image]" required>
                                                        <?php foreach ($icons as $path => $name): ?>
                                                            <option value="<?= e($path) ?>" <?= ($section['image'] ?? '') === $path ? 'selected' : '' ?>><?= e($name) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" for="<?= e($key) ?>-extra">Additional paragraph</label>
                                                    <textarea class="form-control" id="<?= e($key) ?>-extra" name="sections[<?= e($key) ?>][extra]" rows="2" maxlength="10000"><?= e($section['extra']) ?></textarea>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Page Copy</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:24px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <div>
                            <h4 class="mb-1" style="font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Focus Area' : 'Edit Focus Area' ?></h4>
                            <p class="text-muted mb-0">Manage the cards displayed under “Our Focus”.</p>
                        </div>
                        <?php if ($selected !== null): ?>
                            <a href="mission.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_focus">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label" for="focus-title">Title *</label>
                                <input class="form-control" id="focus-title" name="title" maxlength="160" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="focus-icon">Icon *</label>
                                <select class="form-select" id="focus-icon" name="icon" required>
                                    <?php foreach ($icons as $path => $name): ?>
                                        <option value="<?= e($path) ?>" <?= ($selected['icon'] ?? 'assets/img/icon/student-visa.svg') === $path ? 'selected' : '' ?>><?= e($name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="focus-order">Display order</label>
                                <input class="form-control" type="number" id="focus-order" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($focusAreas) + 1) ?>" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="focus-status" name="status" value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="focus-status">Show</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="focus-description">Description</label>
                                <textarea class="form-control" id="focus-description" name="description" rows="3" maxlength="10000"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Focus Area' : 'Save Focus Area' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:24px;">
                    <h4 class="mb-4" style="font-weight:700; color:#0f172a;">Focus Areas</h4>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr><th>Order</th><th>Title</th><th>Description</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($focusAreas as $area): ?>
                                    <tr>
                                        <td><?= e($area['sort_order']) ?></td>
                                        <td><?= e($area['title']) ?></td>
                                        <td><?= e($area['description']) ?></td>
                                        <td><?= (int) $area['status'] === 1 ? 'Active' : 'Hidden' ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?edit=<?= e($area['id']) ?>" class="btn btn-sm btn-primary" title="Edit" aria-label="Edit <?= e($area['title']) ?>"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this focus area?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_focus">
                                                    <input type="hidden" name="id" value="<?= e($area['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete <?= e($area['title']) ?>"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if ($focusAreas === []): ?>
                                    <tr><td colspan="5" class="text-center text-muted">No focus areas yet. Add one above.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
