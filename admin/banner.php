<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$_SESSION['banner_csrf'] ??= bin2hex(random_bytes(32));
$bannerData = rows(
    "SELECT id, title, image_path, sort_order, status
     FROM banners
     WHERE page_name = 'home'
     ORDER BY sort_order ASC, id ASC"
);

$messages = [
    'created' => 'Banner added successfully.',
    'updated' => 'Banner updated successfully.',
    'deleted' => 'Banner deleted successfully.',
];
$errors = [
    'invalid_request' => 'The request could not be verified. Please try again.',
    'invalid_image' => 'Choose a valid JPG, PNG, or WEBP image up to 5 MB.',
    'upload_failed' => 'The image could not be uploaded. Please try again.',
    'save_failed' => 'The banner could not be saved. Please try again.',
    'not_found' => 'That home page banner no longer exists.',
    'invalid_details' => 'Please enter a title and valid banner details.',
];
$notice = $messages[(string) ($_GET['success'] ?? '')] ?? '';
$error = $errors[(string) ($_GET['error'] ?? '')] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding: 24px;">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                        style="background: linear-gradient(135deg,#0f172a,#059669); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(5,150,105,.18);">
                        <h3 style="margin:0; font-size:1.8rem; color:#fff;">Manage Home Page Banners</h3>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ecfdf5; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-image"></i> Banner Panel
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="white_card card_height_100 mb_30 pt-4"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:20px;">
                            <?php if ($notice !== ''): ?>
                                <div class="alert alert-success" role="status"><?= e($notice) ?></div>
                            <?php endif; ?>
                            <?php if ($error !== ''): ?>
                                <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                            <?php endif; ?>

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                                <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Home Banner Library</h4>
                                <a href="edit_banner.php" class="btn btn-success"
                                    style="background:linear-gradient(135deg,#10b981,#059669); border:none; border-radius:10px; padding:10px 16px; font-weight:600;">
                                    <i class="fas fa-plus"></i> Add Banner
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:800px;">
                                    <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                        <tr>
                                            <th style="padding:14px 12px;">Order</th>
                                            <th style="padding:14px 12px;">Title</th>
                                            <th style="padding:14px 12px;">Preview</th>
                                            <th style="padding:14px 12px;">Status</th>
                                            <th style="padding:14px 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($bannerData === []): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No home page banners yet. Use “Add Banner” to create one.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($bannerData as $banner): ?>
                                            <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                                <td style="padding:14px 12px; font-weight:600; color:#334155;"><?= e($banner['sort_order']) ?></td>
                                                <td style="padding:14px 12px; font-weight:600; color:#0f172a;"><?= e($banner['title']) ?></td>
                                                <td style="padding:14px 12px;">
                                                    <img src="<?= e(img_url($banner['image_path'])) ?>" alt="<?= e($banner['title']) ?>"
                                                        style="width:120px; height:70px; object-fit:cover; border-radius:12px; border:1px solid #dbeafe; box-shadow:0 8px 18px rgba(15,23,42,.1);">
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <span style="padding:6px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; background:<?= $banner['status'] === 'Active' ? '#dcfce7' : '#fef3c7' ?>; color:<?= $banner['status'] === 'Active' ? '#166534' : '#b45309' ?>;">
                                                        <?= e($banner['status']) ?>
                                                    </span>
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="edit_banner.php?id=<?= e($banner['id']) ?>" class="btn btn-sm btn-primary" title="Edit banner" aria-label="Edit banner">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                        <form method="post" action="update_banner.php" onsubmit="return confirm('Delete this banner?');">
                                                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['banner_csrf']) ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?= e($banner['id']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete banner" aria-label="Delete banner">
                                                                <i class="far fa-trash-alt"></i>
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
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
