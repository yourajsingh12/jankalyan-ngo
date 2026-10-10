<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function certificate_admin_redirect(): never
{
    header('Location: certificates.php');
    exit;
}

function remove_unused_certificate_image(string $imagePath): void
{
    if ((int) val('SELECT COUNT(*) FROM certificate_showcase WHERE image = ?', [$imagePath], 0) === 0) {
        delete_upload($imagePath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save_page_content') {
        $tagline = trim((string) ($_POST['page_tagline'] ?? ''));
        $heading = trim((string) ($_POST['page_heading'] ?? ''));
        $body = trim((string) ($_POST['page_body'] ?? ''));

        if (
            $tagline === ''
            || mb_strlen($tagline) > 160
            || $heading === ''
            || mb_strlen($heading) > 255
            || $body === ''
            || mb_strlen($body) > 10000
        ) {
            flash('Please check the certificate page tagline, heading, and description.', 'error');
            certificate_admin_redirect();
        }

        try {
            q(
                'INSERT INTO page_sections (page, section_key, tagline, heading, body, status)
                 VALUES (?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), body = VALUES(body), status = 1',
                ['certificate', 'intro', $tagline, $heading, $body]
            );
            flash('Certificate page content updated successfully.');
        } catch (PDOException $exception) {
            error_log('Certificate page content save failed: ' . $exception->getMessage());
            flash('Certificate page content could not be saved. Please try again.', 'error');
        }

        certificate_admin_redirect();
    }

    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That certificate could not be found.', 'error');
            certificate_admin_redirect();
        }

        $certificate = row('SELECT id, image FROM certificate_showcase WHERE id = ?', [$id]);
        if ($certificate === null) {
            flash('That certificate could not be found.', 'error');
            certificate_admin_redirect();
        }

        q('DELETE FROM certificate_showcase WHERE id = ?', [$id]);
        remove_unused_certificate_image((string) $certificate['image']);
        flash('Certificate deleted successfully.');
        certificate_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        certificate_admin_redirect();
    }

    $existing = $id === null
        ? null
        : row('SELECT id, image FROM certificate_showcase WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That certificate could not be found.', 'error');
        certificate_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $issuedBy = trim((string) ($_POST['issued_by'] ?? ''));
    $issuedOn = trim((string) ($_POST['issued_on'] ?? ''));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');
    $dateParts = explode('-', $issuedOn);
    $validDate = $issuedOn === ''
        || (
            count($dateParts) === 3
            &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $issuedOn) === 1
            && checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0])
        );

    if (
        $title === ''
        || mb_strlen($title) > 255
        || mb_strlen($issuedBy) > 160
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['0', '1'], true)
        || !$validDate
    ) {
        flash('Enter a title, valid date, display order, and status for the certificate.', 'error');
        certificate_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'certificates');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        certificate_admin_redirect();
    }

    if ($existing === null && $newImage === null) {
        flash('Please choose a certificate image to upload.', 'error');
        certificate_admin_redirect();
    }

    $image = $newImage ?? (string) $existing['image'];

    try {
        if ($existing === null) {
            q(
                'INSERT INTO certificate_showcase (title, description, image, issued_by, issued_on, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $description, $image, $issuedBy, $issuedOn !== '' ? $issuedOn : null, $sortOrder, (int) $status]
            );
            flash('Certificate added successfully.');
        } else {
            q(
                'UPDATE certificate_showcase
                    SET title = ?, description = ?, image = ?, issued_by = ?, issued_on = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [$title, $description, $image, $issuedBy, $issuedOn !== '' ? $issuedOn : null, $sortOrder, (int) $status, $id]
            );
            if ($newImage !== null) {
                remove_unused_certificate_image((string) $existing['image']);
            }
            flash('Certificate updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Certificate save failed: ' . $exception->getMessage());
        flash('The certificate could not be saved. Please try again.', 'error');
    }

    certificate_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM certificate_showcase WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That certificate could not be found.', 'error');
        certificate_admin_redirect();
    }
}

$certificates = rows(
    'SELECT id, title, image, issued_by, issued_on, sort_order, status
       FROM certificate_showcase
      ORDER BY sort_order ASC, id ASC'
);
$certificateIntro = sections('certificate')['intro'] ?? [];
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
                style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(15,118,110,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Website Certificates</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ecfeff; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-certificate"></i> Certificate Gallery
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div id="page-content" class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-2" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Certificate Page Introduction</h4>
                    <p class="text-muted mb-4">Edit the recognition tagline, page heading, and introduction displayed above the certificate gallery.</p>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_page_content">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="certificate-page-tagline" class="form-label">Tagline</label>
                                <input id="certificate-page-tagline" class="form-control" name="page_tagline" maxlength="160" value="<?= e($certificateIntro['tagline'] ?? 'Our Recognition') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="certificate-page-heading" class="form-label">Heading</label>
                                <input id="certificate-page-heading" class="form-control" name="page_heading" maxlength="255" value="<?= e($certificateIntro['heading'] ?? 'Certificates & Achievements') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="certificate-page-body" class="form-label">Introduction</label>
                                <textarea id="certificate-page-body" class="form-control" name="page_body" rows="3" maxlength="10000" required><?= e($certificateIntro['body'] ?? 'Explore the certificates and recognitions that reflect our commitment to serving communities with care, transparency and purpose.') ?></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Page Introduction</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Certificate' : 'Edit Certificate' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="certificates.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="certificate-title" class="form-label">Certificate title *</label>
                                <input id="certificate-title" class="form-control" name="title" maxlength="255" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="certificate-image" class="form-label">Certificate image <?= $selected === null ? '*' : '(choose to replace current image)' ?></label>
                                <input id="certificate-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" <?= $selected === null ? 'required' : '' ?>>
                                <small class="text-muted">JPG, PNG, WEBP, or GIF; maximum 5 MB.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="certificate-description" class="form-label">Description</label>
                                <textarea id="certificate-description" class="form-control" name="description" rows="3"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <?php if ($selected !== null && !empty($selected['image'])): ?>
                                    <label class="form-label">Current image</label><br>
                                    <a href="<?= e(img_url($selected['image'])) ?>" target="_blank" rel="noopener">
                                        <img src="<?= e(img_url($selected['image'])) ?>" alt="<?= e($selected['title']) ?>" style="max-width:100%; max-height:150px; border-radius:10px;">
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="certificate-issuer" class="form-label">Issued by</label>
                                <input id="certificate-issuer" class="form-control" name="issued_by" maxlength="160" value="<?= e($selected['issued_by'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="certificate-date" class="form-label">Date</label>
                                <input id="certificate-date" class="form-control" type="date" name="issued_on" value="<?= e($selected['issued_on'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="certificate-order" class="form-label">Display order</label>
                                <input id="certificate-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($certificates) + 1) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="certificate-status" class="form-label">Status</label>
                                <select id="certificate-status" class="form-select" name="status">
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Visible on website</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Certificate' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Certificate Gallery Items</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:900px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Image</th><th>Title</th><th>Issued by</th><th>Date</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($certificates === []): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No certificates yet. Add one using the form above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($certificates as $certificate): ?>
                                    <tr>
                                        <td><img src="<?= e(img_url($certificate['image'])) ?>" alt="<?= e($certificate['title']) ?>" style="width:100px; height:65px; object-fit:cover; border-radius:8px;"></td>
                                        <td><?= e($certificate['title']) ?></td>
                                        <td><?= e($certificate['issued_by']) ?></td>
                                        <td><?= e($certificate['issued_on']) ?></td>
                                        <td><?= e($certificate['sort_order']) ?></td>
                                        <td><?= (int) $certificate['status'] === 1 ? 'Visible' : 'Hidden' ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary" href="certificates.php?edit=<?= e($certificate['id']) ?>" aria-label="Edit certificate"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this certificate?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($certificate['id']) ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" aria-label="Delete certificate"><i class="fas fa-trash"></i></button>
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
