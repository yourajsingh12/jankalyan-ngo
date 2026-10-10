<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function testimonials_admin_redirect(): never
{
    header('Location: testimonials.php');
    exit;
}

function remove_unused_testimonial_image(string $image): void
{
    if ((int) val('SELECT COUNT(*) FROM testimonials WHERE image = ?', [$image], 0) === 0) {
        delete_upload($image);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That testimonial could not be found.', 'error');
            testimonials_admin_redirect();
        }

        $testimonial = row('SELECT id, image FROM testimonials WHERE id = ?', [$id]);
        if ($testimonial === null) {
            flash('That testimonial could not be found.', 'error');
            testimonials_admin_redirect();
        }

        q('DELETE FROM testimonials WHERE id = ?', [$id]);
        if (!empty($testimonial['image'])) {
            remove_unused_testimonial_image((string) $testimonial['image']);
        }

        flash('Testimonial deleted successfully.');
        testimonials_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        testimonials_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image FROM testimonials WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That testimonial could not be found.', 'error');
        testimonials_admin_redirect();
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $designation = trim((string) ($_POST['designation'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $rating = filter_var($_POST['rating'] ?? '5', FILTER_VALIDATE_INT);
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');

    if (
        $name === ''
        || mb_strlen($name) > 120
        || mb_strlen($designation) > 160
        || $message === ''
        || mb_strlen($message) > 5000
        || $rating === false
        || $rating < 1
        || $rating > 5
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['0', '1'], true)
    ) {
        flash('Enter a valid name, designation, message, rating, display order, and status.', 'error');
        testimonials_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'testimonials');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        testimonials_admin_redirect();
    }

    $image = $newImage ?? (string) ($existing['image'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO testimonials (name, designation, message, image, rating, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $name,
                    $designation !== '' ? $designation : null,
                    $message,
                    $image !== '' ? $image : null,
                    $rating,
                    $sortOrder,
                    (int) $status,
                ]
            );
            flash('Testimonial added successfully.');
        } else {
            q(
                'UPDATE testimonials
                    SET name = ?, designation = ?, message = ?, image = ?, rating = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [
                    $name,
                    $designation !== '' ? $designation : null,
                    $message,
                    $image !== '' ? $image : null,
                    $rating,
                    $sortOrder,
                    (int) $status,
                    $id,
                ]
            );
            if ($newImage !== null && !empty($existing['image'])) {
                remove_unused_testimonial_image((string) $existing['image']);
            }
            flash('Testimonial updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Testimonial save failed: ' . $exception->getMessage());
        flash('The testimonial could not be saved. Please try again.', 'error');
    }

    testimonials_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM testimonials WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That testimonial could not be found.', 'error');
        testimonials_admin_redirect();
    }
}

$testimonials = rows(
    'SELECT id, name, designation, message, image, rating, sort_order, status
       FROM testimonials
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
                style="background:linear-gradient(135deg,#0f172a,#7c3aed); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(124,58,237,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Community Voices</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ede9fe; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-comment-alt"></i> Testimonials
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" id="testimonial-form" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Testimonial' : 'Edit Testimonial' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="testimonials.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="testimonial-name" class="form-label">Name *</label>
                                <input id="testimonial-name" class="form-control" name="name" maxlength="120" value="<?= e($selected['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="testimonial-designation" class="form-label">Role / designation</label>
                                <input id="testimonial-designation" class="form-control" name="designation" maxlength="160" value="<?= e($selected['designation'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label for="testimonial-message" class="form-label">Testimonial *</label>
                                <textarea id="testimonial-message" class="form-control" name="message" rows="4" maxlength="5000" required><?= e($selected['message'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="testimonial-image" class="form-label">Photo</label>
                                <input id="testimonial-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <?php if (!empty($selected['image'])): ?>
                                    <small class="text-muted">Current: <?= e(basename((string) $selected['image'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <label for="testimonial-rating" class="form-label">Rating</label>
                                <select id="testimonial-rating" class="form-select" name="rating">
                                    <?php for ($rating = 1; $rating <= 5; $rating++): ?>
                                        <option value="<?= $rating ?>" <?= (int) ($selected['rating'] ?? 5) === $rating ? 'selected' : '' ?>><?= $rating ?> star<?= $rating === 1 ? '' : 's' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="testimonial-status" class="form-label">Status</label>
                                <select id="testimonial-status" class="form-select" name="status">
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="testimonial-order" class="form-label">Display order</label>
                                <input id="testimonial-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($testimonials) + 1) ?>" required>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Testimonial' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-3" style="font-size:1.2rem; font-weight:700; color:#0f172a;">Testimonials</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:900px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Name</th><th>Testimonial</th><th>Rating</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($testimonials === []): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">No testimonials yet. Add the first one above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($testimonials as $testimonial): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($testimonial['name']) ?></strong>
                                            <?php if (!empty($testimonial['designation'])): ?><br><small class="text-muted"><?= e($testimonial['designation']) ?></small><?php endif; ?>
                                        </td>
                                        <td><?= e(excerpt((string) $testimonial['message'], 180)) ?></td>
                                        <td><?= e($testimonial['rating']) ?>/5</td>
                                        <td><?= e($testimonial['sort_order']) ?></td>
                                        <td><?= (int) $testimonial['status'] === 1 ? 'Active' : 'Hidden' ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-primary" href="testimonials.php?edit=<?= e($testimonial['id']) ?>#testimonial-form" aria-label="Edit testimonial"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this testimonial?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($testimonial['id']) ?>">
                                                    <button class="btn btn-danger" type="submit" aria-label="Delete testimonial"><i class="fas fa-trash"></i></button>
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
