<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $focus_area = $_POST['focus_area'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'Active';
    
    if (!empty($title) && !empty($category)) {
        $_SESSION['message'] = "Mission/Vision '$title' added successfully!";
        header('Location: mission.php');
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
                        style="background: linear-gradient(135deg,#0f172a,#10b981); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(16,185,129,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Add Mission / Vision</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#d1fae5; border-radius:12px; padding:8px 12px;">
                            <i class="fas fa-plus-circle"></i> New Entry
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
                                    <label for="title" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Title / Statement *</label>
                                    <input type="text" id="title" name="title" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="category" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Category *</label>
                                    <select id="category" name="category" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                        <option value="">Select Category</option>
                                        <option value="Mission">Mission</option>
                                        <option value="Vision">Vision</option>
                                        <option value="Value">Value</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="focus_area" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Focus Areas</label>
                                    <input type="text" id="focus_area" name="focus_area" placeholder="e.g., Education, Healthcare, Environment"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="description" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Description</label>
                                    <textarea id="description" name="description" rows="5"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem; font-family:inherit;"></textarea>
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
                                        <i class="fas fa-check"></i> Save Mission
                                    </button>
                                    <a href="mission.php" class="btn btn-secondary" 
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
