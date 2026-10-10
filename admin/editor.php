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
        $imageInput = trim((string) ($_POST['image_path'] ?? ''));
        $icon = trim((string) ($_POST['icon'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '1');

        if (
            ($id !== null && ($id === false || $id < 1))
            || $title === null
            || mb_strlen($description) > 5000
            || mb_strlen($detail) > 20000
            || mb_strlen($imageInput) > 255
            || ($isFeature && $imageInput !== '' && !valid_editor_path($imageInput))
            || mb_strlen($icon) > 255
            || ($icon !== '' && !valid_editor_path($icon))
            || $sortOrder === false
            || $sortOrder < 0
            || $sortOrder > 999999
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Enter a title, description, icon path, display order, and status with valid values.', 'error');
            editor_redirect();
        }

        $existing = $id === null
            ? null
            : row("SELECT id, image FROM {$table} WHERE id = ?", [$id]);
        if ($id !== null && $existing === null) {
            flash('That homepage item could not be found.', 'error');
            editor_redirect();
        }

        $newImage = null;
        if ($isFeature) {
            try {
                $newImage = upload_image($_FILES['image_upload'] ?? null, 'homepage-features');
            } catch (RuntimeException $exception) {
                flash($exception->getMessage(), 'error');
                editor_redirect();
            }
        }
        $image = $newImage ?? ($imageInput !== '' ? $imageInput : null);
        if ($isFeature && $image === null && $existing !== null) {
            $image = (string) ($existing['image'] ?? '');
            $image = $image !== '' ? $image : null;
        }

        try {
            if ($id === null) {
                if ($isFeature) {
                    q(
                        'INSERT INTO home_features (title, description, detail, image, icon, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
                        [$title, $description, $detail !== '' ? $detail : null, $image, $icon !== '' ? $icon : null, $sortOrder, (int) $status]
                    );
                } else {
                    q(
                        'INSERT INTO process_steps (title, description, icon, sort_order, status) VALUES (?, ?, ?, ?, ?)',
                        [$title, $description, $icon !== '' ? $icon : null, $sortOrder, (int) $status]
                    );
                }
                flash($isFeature ? 'Homepage feature added successfully.' : 'Get Involved item added successfully.');
            } else {
                if ($isFeature) {
                    q(
                        'UPDATE home_features SET title = ?, description = ?, detail = ?, image = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?',
                        [$title, $description, $detail !== '' ? $detail : null, $image, $icon !== '' ? $icon : null, $sortOrder, (int) $status, $id]
                    );
                } else {
                    q(
                        'UPDATE process_steps SET title = ?, description = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?',
                        [$title, $description, $icon !== '' ? $icon : null, $sortOrder, (int) $status, $id]
                    );
                }
                if ($isFeature && !empty($existing['image']) && $image !== $existing['image']) {
                    remove_unused_home_feature_image((string) $existing['image']);
                }
                flash($isFeature ? 'Homepage feature updated successfully.' : 'Get Involved item updated successfully.');
            }
        } catch (PDOException $exception) {
            if ($newImage !== null) {
                delete_upload($newImage);
            }
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

    if ($action === 'sections_save') {
        $ctaHeading = editor_value('cta_heading', 255);
        $ctaBody = trim((string) ($_POST['cta_body'] ?? ''));
        $ctaImageInput = trim((string) ($_POST['cta_image'] ?? ''));
        $ctaButtonLabel = editor_value('cta_button_label', 60);
        $ctaButtonUrl = trim((string) ($_POST['cta_button_url'] ?? ''));
        $focusTagline = editor_value('focus_tagline', 160);
        $focusHeading = editor_value('focus_heading', 255);
        $involveTagline = editor_value('involve_tagline', 160);
        $involveHeading = editor_value('involve_heading', 255);

        if (
            $ctaHeading === null
            || $ctaBody === ''
            || mb_strlen($ctaBody) > 10000
            || mb_strlen($ctaImageInput) > 255
            || ($ctaImageInput !== '' && !valid_editor_path($ctaImageInput))
            || $ctaButtonLabel === null
            || $ctaButtonUrl === ''
            || !valid_editor_path($ctaButtonUrl)
            || $focusTagline === null
            || $focusHeading === null
            || $involveTagline === null
            || $involveHeading === null
        ) {
            flash('Please complete the homepage headings, CTA copy, button, and a valid image/link path.', 'error');
            editor_redirect();
        }

        $existingCta = row("SELECT image FROM page_sections WHERE page = 'home' AND section_key = 'cta'");
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
            q(
                "INSERT INTO page_sections (page, section_key, tagline, heading, status)
                 VALUES ('home', 'focus', ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), status = 1",
                [$focusTagline, $focusHeading]
            );
            q(
                "INSERT INTO page_sections (page, section_key, tagline, heading, status)
                 VALUES ('home', 'involve', ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), status = 1",
                [$involveTagline, $involveHeading]
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

            <div class="white_card mb_30" id="sections" style="background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Homepage headings and main call to action</h4>
                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="sections_save">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="focus-tagline">Focus Areas tagline</label>
                                <input class="form-control" id="focus-tagline" name="focus_tagline" maxlength="160" value="<?= e($focus['tagline'] ?? 'Our Focus Areas') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="focus-heading">Focus Areas heading</label>
                                <input class="form-control" id="focus-heading" name="focus_heading" maxlength="255" value="<?= e(strip_tags((string) ($focus['heading'] ?? 'Building a stronger and healthier society'))) ?>" required>
                            </div>
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
                                <label class="form-label" for="cta-button-url">CTA button link</label>
                                <input class="form-control" id="cta-button-url" name="cta_button_url" maxlength="255" value="<?= e($ctaButton[1] ?? 'donate.php') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cta-image">CTA image path</label>
                                <input class="form-control" id="cta-image" name="cta_image" maxlength="255" value="<?= e($cta['image'] ?? 'assets/img/cta/flood.png') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cta-image-upload">Or upload a replacement image</label>
                                <input class="form-control" id="cta-image-upload" type="file" name="cta_image_upload" accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="involve-tagline">Get Involved tagline</label>
                                <input class="form-control" id="involve-tagline" name="involve_tagline" maxlength="160" value="<?= e($involve['tagline'] ?? 'Get Involved') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="involve-heading">Get Involved heading</label>
                                <input class="form-control" id="involve-heading" name="involve_heading" maxlength="255" value="<?= e(strip_tags((string) ($involve['heading'] ?? 'Small actions create lasting change'))) ?>" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Homepage Sections</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" id="features" style="background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Homepage feature cards</h4>
                    <form method="post" enctype="multipart/form-data" id="new-feature-form" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="feature_save">
                        <div class="col-md-3"><label class="form-label">Title</label><input class="form-control" name="title" maxlength="160" required></div>
                        <div class="col-md-4"><label class="form-label">Short description</label><textarea class="form-control" name="description" rows="2" maxlength="5000"></textarea></div>
                        <div class="col-md-5"><label class="form-label">Full details (shown on More Details page)</label><textarea class="form-control" name="detail" rows="3" maxlength="20000" placeholder="Write the full information about this feature"></textarea></div>
                        <div class="col-md-4"><label class="form-label">Detail image path</label><input class="form-control" name="image_path" maxlength="255" placeholder="assets/img/visa/seva1.png"></div>
                        <div class="col-md-4"><label class="form-label">Or upload detail image (JPG, PNG, WEBP, GIF)</label><input class="form-control" type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"></div>
                        <div class="col-md-4"><label class="form-label">Icon path</label><input class="form-control" name="icon" maxlength="255" placeholder="assets/img/icon/team.svg"></div>
                        <div class="col-md-3"><label class="form-label">Order</label><input class="form-control" type="number" name="sort_order" min="0" value="<?= count($features) + 1 ?>" required></div>
                        <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1">Show</option><option value="0">Hide</option></select></div>
                        <div class="col-md-3"><button class="btn btn-success w-100" type="submit">Add Feature</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1450px;">
                            <thead><tr><th>Title</th><th>Short description</th><th>Full details</th><th>Detail image</th><th>Upload replacement</th><th>Icon path</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($features === []): ?><tr><td colspan="9" class="text-center text-muted">No homepage feature cards yet.</td></tr><?php endif; ?>
                                <?php foreach ($features as $feature): $formId = 'feature-' . (int) $feature['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="feature_save"><input type="hidden" name="id" value="<?= e($feature['id']) ?>"></form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="title" maxlength="160" value="<?= e($feature['title']) ?>" required>
                                        </td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="description" rows="2" maxlength="5000"><?= e($feature['description']) ?></textarea></td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="detail" rows="4" maxlength="20000" placeholder="Full detail for More Details page"><?= e($feature['detail']) ?></textarea></td>
                                        <td><input class="form-control mb-2" form="<?= e($formId) ?>" name="image_path" maxlength="255" value="<?= e($feature['image']) ?>" placeholder="assets/img/visa/seva1.png"><?php if (!empty($feature['image'])): ?><img src="<?= e(img_url((string) $feature['image'])) ?>" alt="<?= e($feature['title']) ?>" style="max-width:140px;max-height:80px;object-fit:cover;border-radius:8px;"><?php endif; ?></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" name="icon" maxlength="255" value="<?= e($feature['icon']) ?>"></td>
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

            <div class="white_card mb_30" id="involvement" style="background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-3">Get Involved items</h4>
                    <form method="post" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="step_save">
                        <div class="col-md-3"><label class="form-label">Title</label><input class="form-control" name="title" maxlength="160" required></div>
                        <div class="col-md-4"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2" maxlength="5000"></textarea></div>
                        <div class="col-md-2"><label class="form-label">Icon path</label><input class="form-control" name="icon" maxlength="255" placeholder="assets/img/icon/team.svg"></div>
                        <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" type="number" name="sort_order" min="0" value="<?= count($steps) + 1 ?>" required></div>
                        <div class="col-md-1"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1">Show</option><option value="0">Hide</option></select></div>
                        <div class="col-md-1"><button class="btn btn-success w-100" type="submit">Add</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:950px;">
                            <thead><tr><th>Title</th><th>Description</th><th>Icon path</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($steps === []): ?><tr><td colspan="6" class="text-center text-muted">No Get Involved items yet.</td></tr><?php endif; ?>
                                <?php foreach ($steps as $step): $formId = 'step-' . (int) $step['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="step_save"><input type="hidden" name="id" value="<?= e($step['id']) ?>"></form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="title" maxlength="160" value="<?= e($step['title']) ?>" required>
                                        </td>
                                        <td><textarea class="form-control" form="<?= e($formId) ?>" name="description" rows="2" maxlength="5000"><?= e($step['description']) ?></textarea></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" name="icon" maxlength="255" value="<?= e($step['icon']) ?>"></td>
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
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
