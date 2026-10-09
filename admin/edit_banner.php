<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$_SESSION['banner_csrf'] ??= bin2hex(random_bytes(32));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$selected = null;

if ($id !== null && $id !== false) {
    $selected = row(
        "SELECT id, title, image_path, sort_order, status
         FROM banners
         WHERE id = ? AND page_name = 'home'",
        [$id]
    );
    if ($selected === null) {
        header('Location: banner.php?error=not_found');
        exit;
    }
}

$errors = [
    'invalid_image' => 'Choose a valid JPG, PNG, or WEBP image up to 5 MB.',
    'upload_failed' => 'The image could not be uploaded. Please try again.',
    'save_failed' => 'The banner could not be saved. Please try again.',
    'invalid_details' => 'Please enter a title and valid banner details.',
];
$error = $errors[(string) ($_GET['error'] ?? '')] ?? '';
$isEditing = $selected !== null;
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding: 24px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                        style="background: linear-gradient(135deg,#0f172a,#10b981); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(16,185,129,.18);">
                        <h3 style="margin:0; font-size:1.8rem; color:#fff;"><?= $isEditing ? 'Edit Home Page Banner' : 'Add Home Page Banner' ?></h3>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.15); color:#ecfdf5; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-image"></i> Banner Editor
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="white_card card_height_100 mb_30"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:22px;">
                            <?php if ($error !== ''): ?>
                                <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Banner Details</h4>
                                <a href="banner.php" class="btn btn-secondary"
                                    style="border-radius:10px; background:#475569; border:none; padding:10px 14px;">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>

                            <form method="post" action="update_banner.php" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['banner_csrf']) ?>">
                                <input type="hidden" name="action" value="save">
                                <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                                <input type="hidden" name="MAX_FILE_SIZE" value="5242880">

                                <div class="row g-4">
                                    <div class="col-lg-5">
                                        <?php if ($isEditing): ?>
                                            <div class="mb-3 text-center">
                                                <label class="form-label fw-bold" style="color:#334155;">Current Banner</label><br>
                                                <img src="<?= e(img_url($selected['image_path'])) ?>" alt="<?= e($selected['title']) ?>"
                                                    style="width:100%; max-width:420px; height:220px; object-fit:cover; border-radius:16px; border:1px solid #dbeafe; box-shadow:0 10px 20px rgba(15,23,42,.08);">
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">Upload a wide image to show as a slide on the home page.</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="mb-3">
                                            <label for="title" class="form-label" style="font-weight:600; color:#334155;">Banner Title *</label>
                                            <input type="text" id="title" name="title" class="form-control" maxlength="255" required
                                                value="<?= e($selected['title'] ?? '') ?>" style="border-radius:10px; border:1px solid #dbeafe;">
                                        </div>

                                        <div class="mb-3">
                                            <label for="banner_image" class="form-label" style="font-weight:600; color:#334155;">
                                                <?= $isEditing ? 'Replace Banner Image' : 'Banner Image *' ?>
                                            </label>
                                            <input type="file" id="banner_image" name="banner_image" class="form-control"
                                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                                <?= $isEditing ? '' : 'required' ?>
                                                style="border-radius:10px; border:1px solid #dbeafe;">
                                            <div class="mt-2 text-muted small">Recommended: wide image (about 1920 × 700 px). JPG, PNG, or WEBP; maximum 5 MB.</div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="sort_order" class="form-label" style="font-weight:600; color:#334155;">Display Order</label>
                                                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0" max="999999"
                                                    value="<?= e($selected['sort_order'] ?? '0') ?>" style="border-radius:10px; border:1px solid #dbeafe;">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="status" class="form-label" style="font-weight:600; color:#334155;">Status</label>
                                                <select id="status" name="status" class="form-select" style="border-radius:10px; border:1px solid #dbeafe;">
                                                    <option value="Active" <?= ($selected['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="Draft" <?= ($selected['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="submit" class="btn btn-success" style="background:#16a34a; border:none; border-radius:10px;">
                                        <i class="fas fa-save"></i> <?= $isEditing ? 'Save Changes' : 'Add Banner' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
