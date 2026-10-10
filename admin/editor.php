<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function editor_redirect(): never
{
    redirect('editor.php');
}

function valid_editor_path(string $path): bool
{
    if ($path === '' || preg_match('/[\x00-\x20]/', $path)) {
        return false;
    }

    if (preg_match('#^https?://#i', $path)) {
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }

    return !str_starts_with($path, '//')
        && !preg_match('/^[a-z][a-z0-9+.-]*:/i', $path)
        && !str_contains($path, '\\');
}

function editor_value(string $key, int $maxLength): ?string
{
    $value = trim((string) ($_POST[$key] ?? ''));

    return $value === '' || mb_strlen($value) > $maxLength ? null : $value;
}

function remove_unused_home_feature_image(string $image): void
{
    if ((int) val('SELECT COUNT(*) FROM home_features WHERE image = ?', [$image], 0) === 0) {
        delete_upload($image);
    }
}

function remove_unused_focus_area_image(string $image): void
{
    if ((int) val('SELECT COUNT(*) FROM focus_areas WHERE image = ?', [$image], 0) === 0) {
        delete_upload($image);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'feature_save' || $action === 'step_save') {
        $isFeature = $action === 'feature_save';
        $table = $isFeature ? 'home_features' : 'process_steps';
        $idInput = trim((string) ($_POST['id'] ?? ''));
        $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);
        $title = editor_value('title', 160);
        $description = trim((string) ($_POST['description'] ?? ''));
        $detail = trim((string) ($_POST['detail'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '1');

        if (
            ($id !== null && ($id === false || $id < 1))
            || $title === null
            || mb_strlen($description) > 5000
            || mb_strlen($detail) > 20000
            || $sortOrder === false
            || $sortOrder < 0
            || $sortOrder > 999999
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Enter a title, description, display order, and status with valid values.', 'error');
            editor_redirect();
        }

        $existing = $id === null
            ? null
            : row("SELECT id, image FROM {$table} WHERE id = ?", [$id]);
        if ($id !== null && $existing === null) {
            flash('That homepage item could not be found.', 'error');
            editor_redirect();
        }

        try {
            if ($id === null) {
                if ($isFeature) {
                    q(
                        'INSERT INTO home_features (title, description, detail, image, icon, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
                        [$title, $description, $detail !== '' ? $detail : null, null, null, $sortOrder, (int) $status]
                    );
                } else {
                    q(
                        'INSERT INTO process_steps (title, description, icon, sort_order, status) VALUES (?, ?, ?, ?, ?)',
                        [$title, $description, null, $sortOrder, (int) $status]
                    );
                }
                flash($isFeature ? 'Homepage feature added successfully.' : 'Get Involved item added successfully.');
            } else {
                if ($isFeature) {
                    q(
                        'UPDATE home_features SET title = ?, description = ?, detail = ?, sort_order = ?, status = ? WHERE id = ?',
                        [$title, $description, $detail !== '' ? $detail : null, $sortOrder, (int) $status, $id]
                    );
                } else {
                    q(
                        'UPDATE process_steps SET title = ?, description = ?, sort_order = ?, status = ? WHERE id = ?',
                        [$title, $description, $sortOrder, (int) $status, $id]
                    );
                }
                flash($isFeature ? 'Homepage feature updated successfully.' : 'Get Involved item updated successfully.');
            }
        } catch (PDOException $exception) {
            error_log('Homepage content save failed: ' . $exception->getMessage());
            flash('The homepage item could not be saved. Please try again.', 'error');
        }

        editor_redirect();
    }

    if ($action === 'feature_delete' || $action === 'step_delete') {
        $isFeature = $action === 'feature_delete';
        $table = $isFeature ? 'home_features' : 'process_steps';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        $existing = $id === false || $id < 1 ? null : row("SELECT id, image FROM {$table} WHERE id = ?", [$id]);
        if ($existing === null) {
            flash('That homepage item could not be found.', 'error');
        } else {
            try {
                q("DELETE FROM {$table} WHERE id = ?", [$id]);
                if ($isFeature && !empty($existing['image'])) {
                    remove_unused_home_feature_image((string) $existing['image']);
                }
                flash($isFeature ? 'Homepage feature deleted successfully.' : 'Get Involved item deleted successfully.');
            } catch (PDOException $exception) {
                error_log('Homepage content delete failed: ' . $exception->getMessage());
                flash('The homepage item could not be deleted. Please try again.', 'error');
            }
        }

        editor_redirect();
    }

    if ($action === 'focus_card_save') {
        $idInput = trim((string) ($_POST['id'] ?? ''));
        $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);
        $title = editor_value('title', 160);
        $description = trim((string) ($_POST['description'] ?? ''));
        $detail = trim((string) ($_POST['detail'] ?? ''));
        $imageInput = trim((string) ($_POST['image_path'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '1');

        if (
            ($id !== null && ($id === false || $id < 1))
            || $title === null
            || mb_strlen($description) > 5000
            || mb_strlen($detail) > 20000
            || mb_strlen($imageInput) > 255
            || ($imageInput !== '' && !valid_editor_path($imageInput))
            || $sortOrder === false
            || $sortOrder < 0
            || $sortOrder > 999999
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Enter a title, description, full details, valid image, display order, and status.', 'error');
            editor_redirect();
        }

        $existing = $id === null
            ? null
            : row('SELECT id, image FROM focus_areas WHERE id = ?', [$id]);
        if ($id !== null && $existing === null) {
            flash('That Focus Area could not be found.', 'error');
            editor_redirect();
        }

        try {
            $newImage = upload_image($_FILES['image_upload'] ?? null, 'focus-areas');
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
            editor_redirect();
        }

        $image = $newImage ?? ($imageInput !== '' ? $imageInput : ($existing['image'] ?? null));

        try {
            if ($id === null) {
                q(
                    'INSERT INTO focus_areas (title, description, detail, image, sort_order, status)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$title, $description !== '' ? $description : null, $detail !== '' ? $detail : null, $image, $sortOrder, (int) $status]
                );
                flash('Focus Area added successfully.');
            } else {
                q(
                    'UPDATE focus_areas
                        SET title = ?, description = ?, detail = ?, image = ?, sort_order = ?, status = ?
                      WHERE id = ?',
                    [$title, $description !== '' ? $description : null, $detail !== '' ? $detail : null, $image, $sortOrder, (int) $status, $id]
                );
                if (!empty($existing['image']) && $image !== $existing['image']) {
                    remove_unused_focus_area_image((string) $existing['image']);
                }
                flash('Focus Area updated successfully.');
            }
        } catch (PDOException $exception) {
            if ($newImage !== null) {
                delete_upload($newImage);
            }
            error_log('Homepage Focus Area save failed: ' . $exception->getMessage());
            flash('The Focus Area could not be saved. Please try again.', 'error');
        }

        editor_redirect();
    }

    if ($action === 'focus_card_delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $focusArea = $id === false || $id < 1
            ? null
            : row('SELECT id, image FROM focus_areas WHERE id = ?', [$id]);

        if ($focusArea === null) {
            flash('That Focus Area could not be found.', 'error');
        } else {
            try {
                q('DELETE FROM focus_areas WHERE id = ?', [$id]);
                if (!empty($focusArea['image'])) {
                    remove_unused_focus_area_image((string) $focusArea['image']);
                }
                flash('Focus Area deleted successfully.');
            } catch (PDOException $exception) {
                error_log('Homepage Focus Area delete failed: ' . $exception->getMessage());
                flash('The Focus Area could not be deleted. Please try again.', 'error');
            }
        }

        editor_redirect();
    }

    if ($action === 'focus_save') {
        $focusTagline = editor_value('focus_tagline', 160);
        $focusHeading = editor_value('focus_heading', 255);
        if ($focusTagline === null || $focusHeading === null) {
            flash('Enter a valid Focus Areas tagline and heading.', 'error');
            editor_redirect();
        }

        try {
            q(
                "INSERT INTO page_sections (page, section_key, tagline, heading, status)
                 VALUES ('home', 'focus', ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), status = 1",
                [$focusTagline, $focusHeading]
            );
            flash('Focus Areas heading updated successfully.');
        } catch (PDOException $exception) {
            error_log('Homepage focus heading save failed: ' . $exception->getMessage());
            flash('The Focus Areas heading could not be saved. Please try again.', 'error');
        }

        editor_redirect();
    }

    if ($action === 'involve_save') {
        $involveTagline = editor_value('involve_tagline', 160);
        $involveHeading = editor_value('involve_heading', 255);
        if ($involveTagline === null || $involveHeading === null) {
            flash('Enter a valid Get Involved tagline and heading.', 'error');
            editor_redirect();
        }

        try {
            q(
                "INSERT INTO page_sections (page, section_key, tagline, heading, status)
                 VALUES ('home', 'involve', ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), status = 1",
                [$involveTagline, $involveHeading]
            );
            flash('Get Involved heading updated successfully.');
        } catch (PDOException $exception) {
            error_log('Homepage involvement heading save failed: ' . $exception->getMessage());
            flash('The Get Involved heading could not be saved. Please try again.', 'error');
        }

        editor_redirect();
    }

    if ($action === 'sections_save') {
        $ctaHeading = editor_value('cta_heading', 255);
        $ctaBody = trim((string) ($_POST['cta_body'] ?? ''));
        $ctaImageInput = trim((string) ($_POST['cta_image'] ?? ''));
        $ctaButtonLabel = editor_value('cta_button_label', 60);
        if (
            $ctaHeading === null
            || $ctaBody === ''
            || mb_strlen($ctaBody) > 10000
            || mb_strlen($ctaImageInput) > 255
            || ($ctaImageInput !== '' && !valid_editor_path($ctaImageInput))
            || $ctaButtonLabel === null
        ) {
            flash('Please complete the homepage headings and CTA content with a valid image path.', 'error');
            editor_redirect();
        }

        $existingCta = row("SELECT image, extra FROM page_sections WHERE page = 'home' AND section_key = 'cta'");
        $existingCtaButton = explode('|', (string) ($existingCta['extra'] ?? 'Donate Now|donate.php'), 2);
        $ctaButtonUrl = trim((string) ($existingCtaButton[1] ?? '')) ?: 'donate.php';
        $newImage = null;
        try {
            $newImage = upload_image($_FILES['cta_image_upload'] ?? null, 'homepage');
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
            editor_redirect();
        }

        $ctaImage = $newImage ?? ($ctaImageInput !== '' ? $ctaImageInput : null);
        if ($ctaImage === null && $existingCta !== null) {
            $ctaImage = (string) ($existingCta['image'] ?? '');
            $ctaImage = $ctaImage !== '' ? $ctaImage : null;
        }
        $ctaExtra = $ctaButtonLabel . '|' . $ctaButtonUrl;

        try {
            db()->beginTransaction();
            q(
                "INSERT INTO page_sections (page, section_key, tagline, heading, body, image, extra, status)
                 VALUES ('home', 'cta', NULL, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE heading = VALUES(heading), body = VALUES(body), image = VALUES(image), extra = VALUES(extra), status = 1",
                [$ctaHeading, $ctaBody, $ctaImage, $ctaExtra]
            );
            db()->commit();
            if ($newImage !== null && !empty($existingCta['image']) && $newImage !== $existingCta['image']) {
                delete_upload((string) $existingCta['image']);
            }
            flash('Homepage section content updated successfully.');
        } catch (PDOException $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            if ($newImage !== null) {
                delete_upload($newImage);
            }
            error_log('Homepage section save failed: ' . $exception->getMessage());
            flash('Homepage section content could not be saved. Please try again.', 'error');
        }

        editor_redirect();
    }

    flash('The requested action is not valid.', 'error');
    editor_redirect();
}

$features = rows('SELECT id, title, description, detail, image, icon, sort_order, status FROM home_features ORDER BY sort_order ASC, id ASC');
$focusAreas = rows('SELECT id, title, description, detail, image, sort_order, status FROM focus_areas ORDER BY sort_order ASC, id ASC');
$steps = rows('SELECT id, title, description, icon, sort_order, status FROM process_steps ORDER BY sort_order ASC, id ASC');
$homeSections = sections('home');
$cta = $homeSections['cta'] ?? [];
$focus = $homeSections['focus'] ?? [];
$involve = $homeSections['involve'] ?? [];
$ctaButton = explode('|', (string) ($cta['extra'] ?? 'Donate Now|donate.php'), 2);
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
                style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px;">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Homepage Content</h3>
                <div class="d-flex gap-2">
                    <a href="page_content.php" class="btn btn-outline-light"><i class="fas fa-arrow-left"></i> Content Pages</a>
                    <a href="../index.php" target="_blank" rel="noopener" class="btn btn-light"><i class="fas fa-external-link-alt"></i> View Homepage</a>
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="homepage-editor-sections" style="display:flex; flex-direction:column;">
            <div class="white_card mb_30" id="features" style="order:1; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-1">Homepage highlight cards</h4>
                    <p class="text-muted mb-4">Edit the cards shown directly below the homepage banner.</p>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1050px;">
                            <thead><tr><th>Title</th><th>Short description</th><th>Full details</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($features === []): ?><tr><td colspan="6" class="text-center text-muted">No homepage highlight cards yet.</td></tr><?php endif; ?>
                                <?php foreach ($features as $feature): $formId = 'feature-' . (int) $feature['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="feature_save"><input type="hidden" name="id" value="<?= e($feature['id']) ?>"></form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="title" maxlength="160" value="<?= e($feature['title']) ?>" required>
                                        </td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="description" rows="2" maxlength="5000"><?= e($feature['description']) ?></textarea></td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="detail" rows="4" maxlength="20000" placeholder="Full detail for More Details page"><?= e($feature['detail']) ?></textarea></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="number" name="sort_order" min="0" value="<?= e($feature['sort_order']) ?>" required></td>
                                        <td><select class="form-select" form="<?= e($formId) ?>" name="status"><option value="1" <?= (int) $feature['status'] === 1 ? 'selected' : '' ?>>Show</option><option value="0" <?= (int) $feature['status'] === 0 ? 'selected' : '' ?>>Hide</option></select></td>
                                        <td class="d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" form="<?= e($formId) ?>" type="submit">Save</button>
                                            <form method="post" onsubmit="return confirm('Delete this feature card?')"><?= csrf_field() ?><input type="hidden" name="action" value="feature_delete"><input type="hidden" name="id" value="<?= e($feature['id']) ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="white_card mb_30" id="focus-heading" style="order:2; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <h4 class="mb-0">Focus Areas</h4>
                        <a href="#focus-cards" class="btn btn-outline-success"><i class="fas fa-arrow-down"></i> Manage Focus Area Cards</a>
                    </div>
                    <form method="post" class="row g-3 align-items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="focus_save">
                        <div class="col-md-5">
                            <label class="form-label" for="focus-tagline">Tagline</label>
                            <input class="form-control" id="focus-tagline" name="focus_tagline" maxlength="160" value="<?= e($focus['tagline'] ?? 'Our Focus Areas') ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="focus-heading-input">Heading</label>
                            <input class="form-control" id="focus-heading-input" name="focus_heading" maxlength="255" value="<?= e(strip_tags((string) ($focus['heading'] ?? 'Building a stronger and healthier society'))) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100" type="submit"><i class="fas fa-save"></i> Save Focus</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" id="focus-cards" style="order:3; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-1">Focus Area cards</h4>
                    <p class="text-muted mb-4">These cards populate the homepage Focus Areas carousel. Their existing icons and links remain unchanged.</p>
                    <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="focus_card_save">
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" maxlength="160" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" maxlength="5000"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full details</label>
                            <textarea class="form-control" name="detail" rows="4" maxlength="20000" placeholder="Add the full information shown on the Focus Area detail page"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image path</label>
                            <input class="form-control" id="new-focus-image-path" name="image_path" maxlength="255" placeholder="assets/img/visa/seva1.png" data-image-path-for="new-focus-image-preview" data-site-root="<?= e(base_url()) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Or upload image (JPG, PNG, WEBP, GIF)</label>
                            <input class="form-control" type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" data-image-preview-for="new-focus-image-preview">
                            <img id="new-focus-image-preview" src="<?= e(img_url('', 'assets/img/visa/seva1.png')) ?>" data-original-src="<?= e(img_url('', 'assets/img/visa/seva1.png')) ?>" alt="New Focus Area image preview" style="display:block; width:180px; height:120px; margin-top:10px; border-radius:10px; object-fit:cover;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order</label>
                            <input class="form-control" type="number" name="sort_order" min="0" value="<?= count($focusAreas) + 1 ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status"><option value="1">Show</option><option value="0">Hide</option></select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100" type="submit">Add Focus Area</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1450px;">
                            <thead><tr><th>Title</th><th>Description</th><th>Full details</th><th>Image path</th><th>Upload replacement</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($focusAreas === []): ?><tr><td colspan="8" class="text-center text-muted">No Focus Area cards yet.</td></tr><?php endif; ?>
                                <?php foreach ($focusAreas as $focusArea): $formId = 'focus-card-' . (int) $focusArea['id']; ?>
                                    <?php $focusCardImage = img_url((string) ($focusArea['image'] ?? ''), 'assets/img/visa/seva1.png'); ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="focus_card_save"><input type="hidden" name="id" value="<?= e($focusArea['id']) ?>"></form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="title" maxlength="160" value="<?= e($focusArea['title']) ?>" required>
                                        </td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="description" rows="2" maxlength="5000"><?= e($focusArea['description']) ?></textarea></td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="detail" rows="4" maxlength="20000" placeholder="Full details for the Focus Area page"><?= e($focusArea['detail'] ?? '') ?></textarea></td>
                                        <td>
                                            <input class="form-control mb-2" form="<?= e($formId) ?>" name="image_path" maxlength="255" value="<?= e($focusArea['image']) ?>" data-image-path-for="focus-card-preview-<?= (int) $focusArea['id'] ?>" data-site-root="<?= e(base_url()) ?>">
                                            <img id="focus-card-preview-<?= (int) $focusArea['id'] ?>" src="<?= e($focusCardImage) ?>" data-original-src="<?= e($focusCardImage) ?>" alt="<?= e($focusArea['title']) ?> preview" style="max-width:140px;max-height:80px;object-fit:cover;border-radius:8px;">
                                        </td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" data-image-preview-for="focus-card-preview-<?= (int) $focusArea['id'] ?>"></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="number" name="sort_order" min="0" value="<?= e($focusArea['sort_order']) ?>" required></td>
                                        <td><select class="form-select" form="<?= e($formId) ?>" name="status"><option value="1" <?= (int) $focusArea['status'] === 1 ? 'selected' : '' ?>>Show</option><option value="0" <?= (int) $focusArea['status'] === 0 ? 'selected' : '' ?>>Hide</option></select></td>
                                        <td class="d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" form="<?= e($formId) ?>" type="submit">Save</button>
                                            <form method="post" onsubmit="return confirm('Delete this Focus Area card?')"><?= csrf_field() ?><input type="hidden" name="action" value="focus_card_delete"><input type="hidden" name="id" value="<?= e($focusArea['id']) ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="white_card mb_30" id="sections" style="order:4; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Homepage call to action</h4>
                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="sections_save">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="cta-heading">CTA heading</label>
                                <input class="form-control" id="cta-heading" name="cta_heading" maxlength="255" value="<?= e(strip_tags((string) ($cta['heading'] ?? 'We work for a healthier, kinder, and stronger tomorrow'))) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cta-button-label">CTA button label</label>
                                <input class="form-control" id="cta-button-label" name="cta_button_label" maxlength="60" value="<?= e($ctaButton[0] ?? 'Donate Now') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="cta-body">CTA description</label>
                                <textarea class="form-control" id="cta-body" name="cta_body" rows="4" maxlength="10000" required><?= e($cta['body'] ?? 'Jan Kalyan Sangh Foundation is committed to empowering underserved communities through education, healthcare, social awareness, and community development.') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cta-image">CTA image path</label>
                                <input class="form-control" id="cta-image" name="cta_image" maxlength="255" value="<?= e($cta['image'] ?? 'assets/img/cta/flood.png') ?>" data-image-path-for="cta-image-preview" data-site-root="<?= e(base_url()) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cta-image-upload">Or upload a replacement image</label>
                                <input class="form-control" id="cta-image-upload" type="file" name="cta_image_upload" accept="image/jpeg,image/png,image/gif,image/webp" data-image-preview-for="cta-image-preview">
                                <img id="cta-image-preview" src="<?= e(img_url((string) ($cta['image'] ?? ''), 'assets/img/cta/flood.png')) ?>" data-original-src="<?= e(img_url((string) ($cta['image'] ?? ''), 'assets/img/cta/flood.png')) ?>" alt="Homepage call to action image preview" style="display:block; width:180px; height:120px; margin-top:10px; border-radius:10px; object-fit:cover;">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save CTA Content</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" id="involve-heading" style="order:5; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Get Involved section heading</h4>
                    <form method="post" class="row g-3 align-items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="involve_save">
                        <div class="col-md-5">
                            <label class="form-label" for="involve-tagline">Tagline</label>
                            <input class="form-control" id="involve-tagline" name="involve_tagline" maxlength="160" value="<?= e($involve['tagline'] ?? 'Get Involved') ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="involve-heading-input">Heading</label>
                            <input class="form-control" id="involve-heading-input" name="involve_heading" maxlength="255" value="<?= e(strip_tags((string) ($involve['heading'] ?? 'Small actions create lasting change'))) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100" type="submit"><i class="fas fa-save"></i> Save Heading</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" id="involvement" style="order:6; background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Get Involved items</h4>
                    <form method="post" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="step_save">
                        <div class="col-md-3"><label class="form-label">Title</label><input class="form-control" name="title" maxlength="160" required></div>
                        <div class="col-md-4"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2" maxlength="5000"></textarea></div>
                        <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" type="number" name="sort_order" min="0" value="<?= count($steps) + 1 ?>" required></div>
                        <div class="col-md-1"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1">Show</option><option value="0">Hide</option></select></div>
                        <div class="col-md-1"><button class="btn btn-success w-100" type="submit">Add</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:950px;">
                            <thead><tr><th>Title</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($steps === []): ?><tr><td colspan="5" class="text-center text-muted">No Get Involved items yet.</td></tr><?php endif; ?>
                                <?php foreach ($steps as $step): $formId = 'step-' . (int) $step['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="step_save"><input type="hidden" name="id" value="<?= e($step['id']) ?>"></form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="title" maxlength="160" value="<?= e($step['title']) ?>" required>
                                        </td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="description" rows="2" maxlength="5000"><?= e($step['description']) ?></textarea></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="number" name="sort_order" min="0" value="<?= e($step['sort_order']) ?>" required></td>
                                        <td><select class="form-select" form="<?= e($formId) ?>" name="status"><option value="1" <?= (int) $step['status'] === 1 ? 'selected' : '' ?>>Show</option><option value="0" <?= (int) $step['status'] === 0 ? 'selected' : '' ?>>Hide</option></select></td>
                                        <td class="d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" form="<?= e($formId) ?>" type="submit">Save</button>
                                            <form method="post" onsubmit="return confirm('Delete this Get Involved item?')"><?= csrf_field() ?><input type="hidden" name="action" value="step_delete"><input type="hidden" name="id" value="<?= e($step['id']) ?>"><button class="btn btn-danger btn-sm" type="submit">Delete</button></form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
    <script>
        document.querySelectorAll('[data-image-preview-for]').forEach((input) => {
            input.addEventListener('change', () => {
                const preview = document.getElementById(input.dataset.imagePreviewFor);
                const file = input.files && input.files[0];
                if (!preview) return;
                if (!file) {
                    preview.src = preview.dataset.originalSrc;
                    return;
                }
                if (!file.type.startsWith('image/')) return;

                const previewUrl = URL.createObjectURL(file);
                preview.onload = () => URL.revokeObjectURL(previewUrl);
                preview.src = previewUrl;
            });
        });

        document.querySelectorAll('[data-image-path-for]').forEach((input) => {
            input.addEventListener('input', () => {
                const preview = document.getElementById(input.dataset.imagePathFor);
                const path = input.value.trim();
                if (!preview) return;
                if (path === '') {
                    preview.src = preview.dataset.originalSrc;
                    return;
                }
                preview.src = /^https?:\/\//i.test(path)
                    ? path
                    : `${input.dataset.siteRoot}/${path.replace(/^\/+/, '')}`;
            });
        });
    </script>
</body>
</html>
