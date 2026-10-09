<?php
session_start();

// Sample stored donation settings (in real scenario, this would be from database)
$donated_settings = [
    'bank_name' => 'State Bank of India',
    'account_holder' => 'Jan Kalyan Sangh Foundation',
    'account_number' => '3149XXXX1234',
    'ifsc_code' => 'SBIN0005678',
    'branch' => 'Indore Branch',
    'upi_id' => 'jankalyan@sbi',
    'qr_code_image' => 'assets/img/qr-code.png',
    'payment_note' => 'All donations are tax-exempted under section 80G',
    'contact_email' => 'donate@jankalyan.org',
    'contact_phone' => '+91-9999999999'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = $_POST['bank_name'] ?? '';
    $account_holder = $_POST['account_holder'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $ifsc_code = $_POST['ifsc_code'] ?? '';
    $branch = $_POST['branch'] ?? '';
    $upi_id = $_POST['upi_id'] ?? '';
    $payment_note = $_POST['payment_note'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    
    if (!empty($bank_name) && !empty($account_number)) {
        $_SESSION['message'] = "Donation settings updated successfully!";
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
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Update Donation Page</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff7ed; border-radius:12px; padding:8px 12px;">
                            <i class="fas fa-cog"></i> Settings
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-10">
                    <div class="white_card card_height_100 mb_30 pt-4"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:30px;">
                            <form method="POST" action="">
                                <div style="background:#f0fdf4; padding:16px; border-radius:12px; margin-bottom:24px; border-left:4px solid #10b981;">
                                    <h5 style="margin:0 0 8px 0; color:#166534; font-weight:700;">Bank Account Details</h5>
                                    <p style="margin:0; color:#047857; font-size:0.9rem;">Update your bank account information for donations</p>
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                                    <div>
                                        <label for="bank_name" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Bank Name *</label>
                                        <input type="text" id="bank_name" name="bank_name" value="<?= htmlspecialchars($donated_settings['bank_name']) ?>" required 
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                    <div>
                                        <label for="branch" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Branch</label>
                                        <input type="text" id="branch" name="branch" value="<?= htmlspecialchars($donated_settings['branch']) ?>"
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="account_holder" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Account Holder Name *</label>
                                    <input type="text" id="account_holder" name="account_holder" value="<?= htmlspecialchars($donated_settings['account_holder']) ?>" required 
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                                    <div>
                                        <label for="account_number" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Account Number *</label>
                                        <input type="text" id="account_number" name="account_number" value="<?= htmlspecialchars($donated_settings['account_number']) ?>" required 
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                    <div>
                                        <label for="ifsc_code" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">IFSC Code *</label>
                                        <input type="text" id="ifsc_code" name="ifsc_code" value="<?= htmlspecialchars($donated_settings['ifsc_code']) ?>" required 
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="upi_id" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">UPI ID</label>
                                    <input type="text" id="upi_id" name="upi_id" value="<?= htmlspecialchars($donated_settings['upi_id']) ?>" placeholder="e.g., jankalyan@sbi"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                </div>

                                <div style="background:#fef3c7; padding:16px; border-radius:12px; margin-bottom:24px; border-left:4px solid #f59e0b;">
                                    <h5 style="margin:0 0 8px 0; color:#b45309; font-weight:700;">QR Code & Additional Info</h5>
                                    <p style="margin:0; color:#92400e; font-size:0.9rem;">Upload QR code and payment information</p>
                                </div>

                                <div class="mb-4">
                                    <label for="qr_code_image" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">QR Code Image URL</label>
                                    <input type="text" id="qr_code_image" name="qr_code_image" value="<?= htmlspecialchars($donated_settings['qr_code_image']) ?>" placeholder="e.g., assets/img/qr-code.png"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    <small style="color:#64748b; margin-top:4px; display:block;">Path to your QR code image file</small>
                                </div>

                                <div class="mb-4">
                                    <label for="payment_note" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Payment Note / Tax Info</label>
                                    <textarea id="payment_note" name="payment_note" rows="3"
                                        style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem; font-family:inherit;"><?= htmlspecialchars($donated_settings['payment_note']) ?></textarea>
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                                    <div>
                                        <label for="contact_email" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Contact Email</label>
                                        <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($donated_settings['contact_email']) ?>"
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                    <div>
                                        <label for="contact_phone" style="display:block; margin-bottom:8px; font-weight:600; color:#0f172a;">Contact Phone</label>
                                        <input type="tel" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($donated_settings['contact_phone']) ?>"
                                            style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem;">
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mt-6">
                                    <button type="submit" class="btn btn-primary" 
                                        style="background:#0f766e; color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; cursor:pointer;">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                    <a href="donate.php" class="btn btn-secondary" 
                                        style="background:#94a3b8; color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; text-decoration:none;">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-10 mt-4">
                    <div class="white_card card_height_100 mb_30 pt-4"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:30px;">
                            <h4 style="margin:0 0 20px 0; font-size:1.2rem; font-weight:700; color:#0f172a;">Preview</h4>
                            
                            <div style="background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #cbd5e1;">
                                <h5 style="margin:0 0 12px 0; color:#0f172a; font-weight:700;">🏦 Bank Account Details</h5>
                                <p style="margin:4px 0; color:#475569;"><strong>Bank:</strong> <?= htmlspecialchars($donated_settings['bank_name']) ?> - <?= htmlspecialchars($donated_settings['branch']) ?></p>
                                <p style="margin:4px 0; color:#475569;"><strong>Account:</strong> <?= htmlspecialchars($donated_settings['account_holder']) ?></p>
                                <p style="margin:4px 0; color:#475569;"><strong>A/C No.:</strong> <?= htmlspecialchars($donated_settings['account_number']) ?></p>
                                <p style="margin:4px 0; color:#475569;"><strong>IFSC:</strong> <?= htmlspecialchars($donated_settings['ifsc_code']) ?></p>
                                <?php if (!empty($donated_settings['upi_id'])): ?>
                                    <p style="margin:4px 0; color:#475569;"><strong>UPI:</strong> <?= htmlspecialchars($donated_settings['upi_id']) ?></p>
                                <?php endif; ?>
                                
                                <hr style="border:none; border-top:1px solid #cbd5e1; margin:12px 0;">
                                
                                <p style="margin:8px 0; color:#475569; font-size:0.9rem; font-style:italic;">
                                    <strong>Note:</strong> <?= htmlspecialchars($donated_settings['payment_note']) ?>
                                </p>
                                
                                <p style="margin:8px 0; color:#475569; font-size:0.85rem;">
                                    Contact: <?= htmlspecialchars($donated_settings['contact_email']) ?> | <?= htmlspecialchars($donated_settings['contact_phone']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('footer.php'); ?>
</body>
</html>
