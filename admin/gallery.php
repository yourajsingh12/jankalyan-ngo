<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function gallery_admin_redirect(): never
{
    header('Location: gallery.php');
    exit;
}

function remove_unused_gallery_image(string $imagePath): void
{
    if ((int) val('SELECT COUNT(*) FROM gallery WHERE image_path = ?', [$imagePath], 0) === 0) {
        delete_upload($imagePath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That gallery image could not be found.', 'error');
            gallery_admin_redirect();
        }

        $image = row('SELECT id, image_path FROM gallery WHERE id = ?', [$id]);
        if ($image === null) {
            flash('That gallery image could not be found.', 'error');
            gallery_admin_redirect();
        }

        q('DELETE FROM gallery WHERE id = ?', [$id]);
        remove_unused_gallery_image((string) $image['image_path']);
        flash('Gallery image deleted successfully.');
        gallery_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        gallery_admin_redirect();
    }

    $existing = $id === null
        ? null
        : row('SELECT id, image_path FROM gallery WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That gallery image could not be found.', 'error');
        gallery_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $altText = trim((string) ($_POST['alt_text'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');

    if (
        mb_strlen($title) > 255
        || mb_strlen($altText) > 255
        || mb_strlen($category) > 80
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['0', '1'], true)
    ) {
        flash('Please enter valid gallery details, display order and status.', 'error');
        gallery_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'gallery');
        if ($newImage !== null) {
            validate_uploaded_image_dimensions($newImage, 500, 600, 'Gallery images');
        }
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        gallery_admin_redirect();
    }

    if ($existing === null && $newImage === null) {
        flash('Please choose an image to add to the gallery.', 'error');
        gallery_admin_redirect();
    }

    $imagePath = $newImage ?? (string) $existing['image_path'];

    try {
        if ($existing === null) {
            q(
                'INSERT INTO gallery (image_path, title, alt_text, category, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    $imagePath,
                    $title !== '' ? $title : null,
                    $altText !== '' ? $altText : null,
                    $category !== '' ? $category : null,
                    $sortOrder,
                    (int) $status,
                ]
            );
            flash('Gallery image added successfully.');
        } else {
            q(
                'UPDATE gallery
                    SET image_path = ?, title = ?, alt_text = ?, category = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [
                    $imagePath,
                    $title !== '' ? $title : null,
                    $altText !== '' ? $altText : null,
                    $category !== '' ? $category : null,
                    $sortOrder,
                    (int) $status,
                    $id,
                ]
            );
            if ($newImage !== null) {
                remove_unused_gallery_image((string) $existing['image_path']);
            }
            flash('Gallery image updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Gallery save failed: ' . $exception->getMessage());
        flash('The gallery image could not be saved. Please try again.', 'error');
    }

    gallery_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM gallery WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That gallery image could not be found.', 'error');
        gallery_admin_redirect();
    }
}

$images = rows(
    'SELECT id, image_path, title, alt_text, category, sort_order, status
       FROM gallery
      ORDER BY sort_order ASC, id DESC'
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
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Manage Gallery</h3>
                <div style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ecfeff; border-radius:12px; padding:8px 12px;">
                    Add, edit and organize website photos
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0"><?= $selected === null ? 'Add Gallery Image' : 'Edit Gallery Image' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="gallery.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="gallery-image" class="form-label">Image <?= $selected === null ? '*' : '(choose to replace current image)' ?></label>
                                <input id="gallery-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" <?= $selected === null ? 'required' : '' ?>>
                                <small class="text-muted">JPG, PNG, WEBP, or GIF; maximum 5 MB; exactly 500 × 600 px.</small>
                            </div>
                            <div class="col-lg-6">
                                <?php if ($selected !== null): ?>
                                    <label class="form-label">Current image</label><br>
                                    <a href="<?= e(img_url((string) $selected['image_path'])) ?>" target="_blank" rel="noopener">
                                        <img src="<?= e(img_url((string) $selected['image_path'])) ?>" alt="<?= e($selected['alt_text'] ?: $selected['title'] ?: 'Gallery image') ?>" style="max-width:100%; max-height:150px; border-radius:10px;">
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="gallery-title" class="form-label">Title</label>
                                <input id="gallery-title" class="form-control" name="title" maxlength="255" value="<?= e($selected['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="gallery-alt" class="form-label">Alt text</label>
                                <input id="gallery-alt" class="form-control" name="alt_text" maxlength="255" value="<?= e($selected['alt_text'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="gallery-category" class="form-label">Category</label>
                                <input id="gallery-category" class="form-control" name="category" maxlength="80" value="<?= e($selected['category'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="gallery-order" class="form-label">Display order</label>
                                <input id="gallery-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($images) + 1) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="gallery-status" class="form-label">Status</label>
                                <select id="gallery-status" class="form-select" name="status">
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Visible on website</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Image' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4">Gallery Images</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:800px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Image</th><th>Title</th><th>Category</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($images === []): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">No gallery images yet. Add one using the form above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($images as $image): ?>
                                    <tr>
                                        <td><img src="<?= e(img_url((string) $image['image_path'])) ?>" alt="<?= e($image['alt_text'] ?: $image['title'] ?: 'Gallery image') ?>" style="width:100px; height:65px; object-fit:cover; border-radius:8px;"></td>
                                        <td><?= e($image['title'] ?: '—') ?></td>
                                        <td><?= e($image['category'] ?: '—') ?></td>
                                        <td><?= e($image['sort_order']) ?></td>
                                        <td><?= (int) $image['status'] === 1 ? 'Visible' : 'Hidden' ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary" href="gallery.php?edit=<?= e($image['id']) ?>" aria-label="Edit gallery image"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this gallery image?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($image['id']) ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" aria-label="Delete gallery image"><i class="fas fa-trash"></i></button>
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
