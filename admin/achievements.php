<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function achievement_admin_redirect(): never
{
    header('Location: achievements.php');
    exit;
}

function ensure_achievement_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS achievements (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            title       VARCHAR(255) NOT NULL,
            description TEXT NULL,
            category    VARCHAR(80) NULL,
            year        VARCHAR(10) NULL,
            highlight   VARCHAR(160) NULL,
            image       VARCHAR(255) NULL,
            icon        VARCHAR(255) NULL,
            sort_order  INT NOT NULL DEFAULT 0,
            status      TINYINT(1) NOT NULL DEFAULT 1,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_order (status, sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    foreach ([
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS category VARCHAR(80) NULL AFTER description',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS year VARCHAR(10) NULL AFTER category',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS highlight VARCHAR(160) NULL AFTER year',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS image VARCHAR(255) NULL AFTER highlight',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS icon VARCHAR(255) NULL AFTER image',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER icon',
        'ALTER TABLE achievements ADD COLUMN IF NOT EXISTS status TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order',
    ] as $statement) {
        try {
            db()->exec($statement);
        } catch (PDOException $exception) {
            error_log('Achievement migration failed: ' . $exception->getMessage());
        }
    }
}

function remove_unused_achievement_asset(string $assetPath): void
{
    if ((int) val('SELECT COUNT(*) FROM achievements WHERE image = ? OR icon = ?', [$assetPath, $assetPath], 0) === 0) {
        delete_upload($assetPath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    ensure_achievement_table();

    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save_page_content') {
        $tagline = trim((string) ($_POST['page_tagline'] ?? ''));
        $heading = trim((string) ($_POST['page_heading'] ?? ''));
        $body = trim((string) ($_POST['page_body'] ?? ''));
        $statValues = is_array($_POST['stat_value'] ?? null) ? $_POST['stat_value'] : [];
        $statLabels = is_array($_POST['stat_label'] ?? null) ? $_POST['stat_label'] : [];
        $statDescriptions = is_array($_POST['stat_description'] ?? null) ? $_POST['stat_description'] : [];
        $statKeys = ['lives', 'volunteers', 'communities', 'service'];
        $invalid = $tagline === ''
            || mb_strlen($tagline) > 160
            || $heading === ''
            || mb_strlen($heading) > 255
            || $body === ''
            || mb_strlen($body) > 10000;
        $statsToSave = [];

        foreach ($statKeys as $statKey) {
            $value = trim((string) ($statValues[$statKey] ?? ''));
            $label = trim((string) ($statLabels[$statKey] ?? ''));
            $description = trim((string) ($statDescriptions[$statKey] ?? ''));
            if (
                $value === ''
                || mb_strlen($value) > 40
                || $label === ''
                || mb_strlen($label) > 120
                || $description === ''
                || mb_strlen($description) > 500
            ) {
                $invalid = true;
                break;
            }
            $statsToSave[$statKey] = [$value, $label, $description];
        }

        if ($invalid) {
            flash('Please check the achievement introduction and all four impact statistics.', 'error');
            achievement_admin_redirect();
        }

        try {
            db()->beginTransaction();
            q(
                'INSERT INTO page_sections (page, section_key, tagline, heading, body, status)
                 VALUES (?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), body = VALUES(body), status = 1',
                ['achievements', 'intro', $tagline, $heading, $body]
            );
            foreach ($statsToSave as $statKey => [$value, $label, $description]) {
                q(
                    'INSERT INTO page_sections (page, section_key, heading, subheading, body, status)
                     VALUES (?, ?, ?, ?, ?, 1)
                     ON DUPLICATE KEY UPDATE heading = VALUES(heading), subheading = VALUES(subheading), body = VALUES(body), status = 1',
                    ['achievements', 'stat_' . $statKey, $value, $label, $description]
                );
            }
            db()->commit();
            flash('Achievement page content updated successfully.');
        } catch (PDOException $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            error_log('Achievement page content save failed: ' . $exception->getMessage());
            flash('Achievement page content could not be saved. Please try again.', 'error');
        }

        achievement_admin_redirect();
    }

    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That achievement could not be found.', 'error');
            achievement_admin_redirect();
        }

        $achievement = row('SELECT id, image, icon FROM achievements WHERE id = ?', [$id]);
        if ($achievement === null) {
            flash('That achievement could not be found.', 'error');
            achievement_admin_redirect();
        }

        q('DELETE FROM achievements WHERE id = ?', [$id]);
        if (!empty($achievement['image'])) {
            remove_unused_achievement_asset((string) $achievement['image']);
        }
        if (!empty($achievement['icon'])) {
            remove_unused_achievement_asset((string) $achievement['icon']);
        }

        flash('Achievement deleted successfully.');
        achievement_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        achievement_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image, icon FROM achievements WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That achievement could not be found.', 'error');
        achievement_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $year = trim((string) ($_POST['year'] ?? ''));
    $highlight = trim((string) ($_POST['highlight'] ?? ''));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '1');

    if ($title === '' || mb_strlen($title) > 255 || mb_strlen($category) > 80 || mb_strlen($year) > 10 || mb_strlen($highlight) > 160 || $sortOrder === false || $sortOrder < 0 || $sortOrder > 999999 || !in_array($status, ['0', '1'], true)) {
        flash('Please enter a valid title, category, year, highlight, order and status.', 'error');
        achievement_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'achievements');
        $newIcon = upload_image($_FILES['icon'] ?? null, 'achievements');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        achievement_admin_redirect();
    }

    $image = $newImage ?? (string) ($existing['image'] ?? '');
    $icon = $newIcon ?? (string) ($existing['icon'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO achievements (title, description, category, year, highlight, image, icon, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $description, $category !== '' ? $category : null, $year !== '' ? $year : null, $highlight !== '' ? $highlight : null, $image !== '' ? $image : null, $icon !== '' ? $icon : null, $sortOrder, (int) $status]
            );
            flash('Achievement added successfully.');
        } else {
            q(
                'UPDATE achievements
                   SET title = ?, description = ?, category = ?, year = ?, highlight = ?, image = ?, icon = ?, sort_order = ?, status = ?
                 WHERE id = ?',
                [$title, $description, $category !== '' ? $category : null, $year !== '' ? $year : null, $highlight !== '' ? $highlight : null, $image !== '' ? $image : null, $icon !== '' ? $icon : null, $sortOrder, (int) $status, $id]
            );
            if ($newImage !== null && !empty($existing['image'])) {
                remove_unused_achievement_asset((string) $existing['image']);
            }
            if ($newIcon !== null && !empty($existing['icon'])) {
                remove_unused_achievement_asset((string) $existing['icon']);
            }
            flash('Achievement updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        if ($newIcon !== null) {
            delete_upload($newIcon);
        }
        error_log('Achievement save failed: ' . $exception->getMessage());
        flash('The achievement could not be saved. Please try again.', 'error');
    }

    achievement_admin_redirect();
}

ensure_achievement_table();

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM achievements WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That achievement could not be found.', 'error');
        achievement_admin_redirect();
    }
}

$achievements = rows(
    'SELECT id, title, description, category, year, highlight, image, icon, sort_order, status
       FROM achievements
      ORDER BY sort_order ASC, id ASC'
);
$achievementSections = sections('achievements');
$achievementIntro = $achievementSections['intro'] ?? [];
$impactStats = [
    'lives' => ['title' => 'Lives Touched', 'value' => '6,500+', 'description' => 'Families reached through community programs.'],
    'volunteers' => ['title' => 'Active Volunteers', 'value' => '48+', 'description' => 'People giving their time and skills.'],
    'communities' => ['title' => 'Communities', 'value' => '25+', 'description' => 'Local areas supported with care.'],
    'service' => ['title' => 'Years Of Service', 'value' => '12+', 'description' => 'A continuing journey of public service.'],
];
foreach ($impactStats as $statKey => &$stat) {
    $savedStat = $achievementSections['stat_' . $statKey] ?? [];
    $stat['value'] = $savedStat['heading'] ?? $stat['value'];
    $stat['title'] = $savedStat['subheading'] ?? $stat['title'];
    $stat['description'] = $savedStat['body'] ?? $stat['description'];
}
unset($stat);
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#f59e0b); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(245,158,11,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Achievements</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff7ed; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-trophy"></i> Achievement Panel
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div id="page-content" class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-2" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Progress Section Content</h4>
                    <p class="text-muted mb-4">Edit the introduction and the four impact statistics shown above the achievement timeline.</p>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_page_content">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="achievement-page-tagline" class="form-label">Tagline</label>
                                <input id="achievement-page-tagline" class="form-control" name="page_tagline" maxlength="160" value="<?= e($achievementIntro['tagline'] ?? 'Our Journey') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="achievement-page-heading" class="form-label">Heading</label>
                                <input id="achievement-page-heading" class="form-control" name="page_heading" maxlength="255" value="<?= e($achievementIntro['heading'] ?? 'Progress powered by people') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="achievement-page-body" class="form-label">Introduction</label>
                                <textarea id="achievement-page-body" class="form-control" name="page_body" rows="3" maxlength="10000" required><?= e($achievementIntro['body'] ?? 'Every milestone represents the trust of our supporters, the dedication of our volunteers and the strength of the communities we serve.') ?></textarea>
                            </div>
                            <?php foreach ($impactStats as $statKey => $stat): ?>
                                <div class="col-12"><hr class="my-2"><h5 class="mb-0"><?= e($stat['title']) ?></h5></div>
                                <div class="col-md-4">
                                    <label for="achievement-stat-value-<?= e($statKey) ?>" class="form-label">Number / value</label>
                                    <input id="achievement-stat-value-<?= e($statKey) ?>" class="form-control" name="stat_value[<?= e($statKey) ?>]" maxlength="40" value="<?= e($stat['value']) ?>" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="achievement-stat-label-<?= e($statKey) ?>" class="form-label">Label</label>
                                    <input id="achievement-stat-label-<?= e($statKey) ?>" class="form-control" name="stat_label[<?= e($statKey) ?>]" maxlength="120" value="<?= e($stat['title']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="achievement-stat-description-<?= e($statKey) ?>" class="form-label">Description</label>
                                    <input id="achievement-stat-description-<?= e($statKey) ?>" class="form-control" name="stat_description[<?= e($statKey) ?>]" maxlength="500" value="<?= e($stat['description']) ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Progress Section</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Achievement' : 'Edit Achievement' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="achievements.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="achievement-title" class="form-label">Title *</label>
                                <input id="achievement-title" class="form-control" name="title" maxlength="255" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-3">
                                <label for="achievement-category" class="form-label">Category</label>
                                <input id="achievement-category" class="form-control" name="category" maxlength="80" value="<?= e($selected['category'] ?? '') ?>">
                            </div>
                            <div class="col-lg-3">
                                <label for="achievement-year" class="form-label">Year</label>
                                <input id="achievement-year" class="form-control" name="year" maxlength="10" value="<?= e($selected['year'] ?? '') ?>">
                            </div>
                            <div class="col-lg-6">
                                <label for="achievement-highlight" class="form-label">Highlight</label>
                                <input id="achievement-highlight" class="form-control" name="highlight" maxlength="160" value="<?= e($selected['highlight'] ?? '') ?>">
                            </div>
                            <div class="col-lg-3">
                                <label for="achievement-order" class="form-label">Display order</label>
                                <input id="achievement-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($achievements) + 1) ?>" required>
                            </div>
                            <div class="col-lg-3">
                                <label for="achievement-status" class="form-label">Status</label>
                                <select id="achievement-status" class="form-select" name="status">
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Visible on website</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="achievement-description" class="form-label">Description</label>
                                <textarea id="achievement-description" class="form-control" name="description" rows="4"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="achievement-image" class="form-label">Achievement image <?= $selected === null ? '' : '(optional replacement)' ?></label>
                                <input id="achievement-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <div class="col-md-6">
                                <label for="achievement-icon" class="form-label">Achievement icon <?= $selected === null ? '' : '(optional replacement)' ?></label>
                                <input id="achievement-icon" class="form-control" type="file" name="icon" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <?php if ($selected !== null && (!empty($selected['image']) || !empty($selected['icon']))): ?>
                                <div class="col-12">
                                    <?php if (!empty($selected['image'])): ?>
                                        <div class="d-inline-block me-3">
                                            <label class="form-label">Current image</label><br>
                                            <img src="<?= e(img_url($selected['image'])) ?>" alt="<?= e($selected['title']) ?>" style="max-width:180px; max-height:100px; border-radius:10px; object-fit:cover;">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($selected['icon'])): ?>
                                        <div class="d-inline-block">
                                            <label class="form-label">Current icon</label><br>
                                            <img src="<?= e(img_url($selected['icon'])) ?>" alt="<?= e($selected['title']) ?> icon" style="max-width:80px; max-height:80px; border-radius:10px; object-fit:contain;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Achievement' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Achievement List</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1100px;">
                            <thead style="background:#f1f5f9; color:#0f172a;">
                                <tr>
                                    <th style="padding:12px 10px;">ID</th>
                                    <th style="padding:12px 10px;">Title</th>
                                    <th style="padding:12px 10px;">Category</th>
                                    <th style="padding:12px 10px;">Year</th>
                                    <th style="padding:12px 10px;">Highlight</th>
                                    <th style="padding:12px 10px;">Status</th>
                                    <th style="padding:12px 10px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achievements as $achievement): ?>
                                    <tr style="border-bottom:1px solid #e2e8f0;">
                                        <td style="padding:12px 10px; font-weight:600; color:#334155;">
                                            <?= e($achievement['id']) ?>
                                        </td>
                                        <td style="padding:12px 10px; font-weight:600; color:#0f172a;">
                                            <?= e($achievement['title']) ?>
                                        </td>
                                        <td style="padding:12px 10px; color:#475569;">
                                            <?= e($achievement['category'] ?? '') ?>
                                        </td>
                                        <td style="padding:12px 10px; color:#475569;">
                                            <?= e($achievement['year'] ?? '') ?>
                                        </td>
                                        <td style="padding:12px 10px; color:#475569;">
                                            <?= e($achievement['highlight'] ?? '') ?>
                                        </td>
                                        <td style="padding:12px 10px;">
                                            <span style="padding:6px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; background: <?= (int) ($achievement['status'] ?? 1) === 1 ? '#dcfce7' : '#fef3c7' ?>; color: <?= (int) ($achievement['status'] ?? 1) === 1 ? '#166534' : '#b45309' ?>;">
                                                <?= (int) ($achievement['status'] ?? 1) === 1 ? 'Published' : 'Draft' ?>
                                            </span>
                                        </td>
                                        <td style="padding:12px 10px;">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="?edit=<?= e((string) $achievement['id']) ?>" class="btn btn-sm btn-primary" title="Edit" style="background:#0f766e; border:none; border-radius:8px; color:#fff;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this achievement?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e((string) $achievement['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" style="background:#dc2626; border:none; border-radius:8px; color:#fff;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
