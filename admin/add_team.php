<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $status = $_POST['status'] ?? 'Active';
    
    if (!empty($name) && !empty($email)) {
        $_SESSION['message'] = "Team member '$name' added successfully!";
        header('Location: team.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once('header.php'); ?>

<body class="crm_body_bg">
    <?php include_once('sidebar.php'); ?>

    <section class="main_content dashboard_part large_header_bg" style="padding: 24px;">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                        style="background: linear-gradient(135deg,#0f172a,#ec4899); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(236,72,153,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Add Team Member</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fce7f3; border-radius:12px; padding:8px 12px;">
                            <i class="fas fa-user-plus"></i> New Member
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="white_card card_height_100 mb_30 pt-4"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:30px;">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label for="name" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Full Name *</label>
                                    <input type="text" id="name" name="name" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="role" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Role / Designation *</label>
                                    <input type="text" id="role" name="role" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="email" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Email Address *</label>
                                    <input type="email" id="email" name="email" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="phone" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="status" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Status</label>
                                    <select id="status" name="status" 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-3 mt-6">
                                    <button type="submit" class="btn btn-primary" 
                                        style="background:#0f766e; color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; cursor:pointer;">
                                        <i class="fas fa-check"></i> Save Member
                                    </button>
                                    <a href="team.php" class="btn btn-secondary" 
                                        style="background:#94a3b8; color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; text-decoration:none;">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php'); ?>
</body>
</html>
