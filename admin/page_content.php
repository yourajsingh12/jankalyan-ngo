<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';
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
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Choose page content</h3>
                <a href="index.php" class="btn btn-light"><i class="fas fa-arrow-left"></i> Dashboard</a>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="white_card h-100" style="background:#fff; border-radius:20px;">
                        <div class="white_card_body p-4">
                            <div class="mb-3" style="font-size:2rem; color:#0f766e;"><i class="fas fa-house"></i></div>
                            <h4>Home Page Content</h4>
                            <p class="text-muted">Edit homepage content in page order: highlight cards, Focus Areas, call to action, and Get Involved.</p>
                            <a href="editor.php" class="btn btn-success"><i class="fas fa-edit"></i> Edit Home Page</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="white_card h-100" style="background:#fff; border-radius:20px;">
                        <div class="white_card_body p-4">
                            <div class="mb-3" style="font-size:2rem; color:#0f766e;"><i class="fas fa-circle-info"></i></div>
                            <h4>About Us Content</h4>
                            <p class="text-muted">Edit the About Us introduction, images, focus points, experience, and impact counters.</p>
                            <a href="about_content.php" class="btn btn-success"><i class="fas fa-edit"></i> Edit About Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
