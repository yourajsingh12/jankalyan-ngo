<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function focus_areas_admin_redirect(): never
{
    header('Location: focus_areas.php');
    exit;
}

function remove_unused_focus_area_asset(string $assetPath): void
{
    if ((int) val('SELECT COUNT(*) FROM focus_areas WHERE image = ?', [$assetPath], 0) === 0) {
        delete_upload($assetPath);
    }
}

function valid_focus_area_url(string $url): bool
{
    if ($url === '' || preg_match('/[\x00-\x20]/', $url)) {
        return false;
    }

    if (preg_match('#^https?://#i', $url)) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    return !str_starts_with($url, '//')
        && !preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)
        && !str_contains($url, '\\');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That focus area could not be found.', 'error');
            focus_areas_admin_redirect();
        }

        $focusArea = row('SELECT id, image FROM focus_areas WHERE id = ?', [$id]);
        if ($focusArea === null) {
            flash('That focus area could not be found.', 'error');
            focus_areas_admin_redirect();
        }

        q('DELETE FROM focus_areas WHERE id = ?', [$id]);
        if (!empty($focusArea['image'])) {
            remove_unused_focus_area_asset((string) $focusArea['image']);
        }

        flash('Focus area deleted successfully.');
        focus_areas_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        focus_areas_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image FROM focus_areas WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That focus area could not be found.', 'error');
        focus_areas_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $icon = trim((string) ($_POST['icon'] ?? ''));
    $imageInput = trim((string) ($_POST['image_path'] ?? ''));
    $linkUrl = trim((string) ($_POST['link_url'] ?? 'donate.php'));
    $linkLabel = trim((string) ($_POST['link_label'] ?? 'Support Now'));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');

    if (
        $title === ''
        || mb_strlen($title) > 160
        || mb_strlen($description) > 5000
        || mb_strlen($icon) > 255
        || mb_strlen($imageInput) > 255
        || mb_strlen($linkLabel) > 60
        || !valid_focus_area_url($linkUrl)
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['0', '1'], true)
        || ($icon !== '' && !valid_focus_area_url($icon))
        || ($imageInput !== '' && !valid_focus_area_url($imageInput))
    ) {
        flash('Please enter a title, valid image/icon paths, link, display order and status.', 'error');
        focus_areas_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image_upload'] ?? null, 'focus-areas');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        focus_areas_admin_redirect();
    }

    $image = $newImage ?? ($imageInput !== '' ? $imageInput : null);
    if ($newImage === null && $imageInput === '' && $existing !== null && str_starts_with((string) $existing['image'], UPLOAD_DIR . '/')) {
        $image = (string) $existing['image'];
    }

    try {
        if ($existing === null) {
            q(
                'INSERT INTO focus_areas (title, description, icon, image, link_url, link_label, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $title,
                    $description !== '' ? $description : null,
                    $icon !== '' ? $icon : null,
                    $image,
                    $linkUrl,
                    $linkLabel !== '' ? $linkLabel : 'Support Now',
                    $sortOrder,
                    (int) $status,
                ]
            );
            flash('Focus area added successfully.');
        } else {
            q(
                'UPDATE focus_areas
                    SET title = ?, description = ?, icon = ?, image = ?, link_url = ?, link_label = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [
                    $title,
                    $description !== '' ? $description : null,
                    $icon !== '' ? $icon : null,
                    $image,
                    $linkUrl,
                    $linkLabel !== '' ? $linkLabel : 'Support Now',
                    $sortOrder,
                    (int) $status,
                    $id,
                ]
            );
            if (!empty($existing['image']) && $image !== $existing['image']) {
                remove_unused_focus_area_asset((string) $existing['image']);
            }
            flash('Focus area updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Focus area save failed: ' . $exception->getMessage());
        flash('The focus area could not be saved. Please try again.', 'error');
    }

    focus_areas_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false && $editId > 0) {
    $selected = row('SELECT * FROM focus_areas WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That focus area could not be found.', 'error');
        focus_areas_admin_redirect();
    }
}

$focusAreas = rows(
    'SELECT id, title, image, sort_order, status
       FROM focus_areas
      ORDER BY sort_order ASC, id ASC'
);
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
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Our Focus Areas</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-bullseye"></i> Focus Area Panel
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Focus Area' : 'Edit Focus Area' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="focus_areas.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="focus-title" class="form-label">Title *</label>
                                <input id="focus-title" class="form-control" name="title" maxlength="160" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="focus-order" class="form-label">Display order</label>
                                <input id="focus-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($focusAreas) + 1) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="focus-status" class="form-label">Status</label>
                                <select id="focus-status" class="form-select" name="status" required>
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Published</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="focus-description" class="form-label">Description</label>
                                <textarea id="focus-description" class="form-control" name="description" rows="5"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="focus-icon" class="form-label">Icon path or HTTPS URL</label>
                                <input id="focus-icon" class="form-control" name="icon" maxlength="255" value="<?= e($selected['icon'] ?? '') ?>" placeholder="assets/img/icon/team.svg">
                            </div>
                            <div class="col-md-6">
                                <label for="focus-image-path" class="form-label">Image path or HTTPS URL</label>
                                <input id="focus-image-path" class="form-control" name="image_path" maxlength="255" value="<?= e($selected['image'] ?? '') ?>" placeholder="assets/img/visa/seva1.png">
                            </div>
                            <div class="col-md-6">
                                <label for="focus-image-upload" class="form-label">Or upload an image (JPG, PNG, WEBP, GIF)</label>
                                <input id="focus-image-upload" class="form-control" type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <div class="col-md-6">
                                <label for="focus-link" class="form-label">Detail-page button URL</label>
                                <input id="focus-link" class="form-control" name="link_url" maxlength="255" value="<?= e($selected['link_url'] ?? 'donate.php') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="focus-link-label" class="form-label">Detail-page button label</label>
                                <input id="focus-link-label" class="form-control" name="link_label" maxlength="60" value="<?= e($selected['link_label'] ?? 'Support Now') ?>">
                            </div>
                            <?php if ($selected !== null && !empty($selected['image'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Current image</label><br>
                                    <img src="<?= e(img_url((string) $selected['image'])) ?>" alt="<?= e($selected['title']) ?>" style="max-width:180px; max-height:110px; border-radius:10px; object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Focus Area' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Focus Area Records</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:650px;">
                            <thead style="background:#f1f5f9; color:#0f172a;">
                                <tr><th>ID</th><th>Focus Area</th><th>Order</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($focusAreas === []): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No focus areas have been added yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($focusAreas as $focusArea): ?>
                                    <tr>
                                        <td><?= e($focusArea['id']) ?></td>
                                        <td><strong><?= e($focusArea['title']) ?></strong></td>
                                        <td><?= e($focusArea['sort_order']) ?></td>
                                        <td><span class="badge bg-<?= (int) $focusArea['status'] === 1 ? 'success' : 'secondary' ?>"><?= (int) $focusArea['status'] === 1 ? 'Published' : 'Hidden' ?></span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?edit=<?= e((string) $focusArea['id']) ?>" class="btn btn-sm btn-primary" title="Edit" aria-label="Edit focus area" style="background:#0f766e; border:none; border-radius:8px; color:#fff;"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this focus area?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e((string) $focusArea['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete focus area" style="background:#dc2626; border:none; border-radius:8px; color:#fff;"><i class="fas fa-trash"></i></button>
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
