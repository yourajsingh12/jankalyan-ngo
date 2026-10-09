<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_name = $_POST['donor_name'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $date = $_POST['date'] ?? '';
    $method = $_POST['method'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $status = $_POST['status'] ?? 'Confirmed';
    
    if (!empty($donor_name) && !empty($amount) && !empty($date)) {
        $_SESSION['message'] = "Donation from '$donor_name' added successfully!";
        header('Location: donate.php');
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
                        style="background: linear-gradient(135deg,#0f172a,#f59e0b); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(245,158,11,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Add Donation</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff7ed; border-radius:12px; padding:8px 12px;">
                            <i class="fas fa-gift"></i> New Donation
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
                                    <label for="donor_name" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Donor Name *</label>
                                    <input type="text" id="donor_name" name="donor_name" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="amount" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Amount (₹) *</label>
                                    <input type="number" id="amount" name="amount" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="date" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Donation Date *</label>
                                    <input type="date" id="date" name="date" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="method" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Payment Method</label>
                                    <select id="method" name="method" 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                        <option value="">Select Method</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Online Payment">Online Payment</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="purpose" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Purpose / Program</label>
                                    <input type="text" id="purpose" name="purpose" placeholder="e.g., Education Program, Healthcare Initiative"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div class="mb-4">
                                    <label for="status" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Status</label>
                                    <select id="status" name="status" 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-3 mt-6">
                                    <button type="submit" class="btn btn-primary" 
                                        style="background:#0f766e; color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; cursor:pointer;">
                                        <i class="fas fa-check"></i> Save Donation
                                    </button>
                                    <a href="donate.php" class="btn btn-secondary" 
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
