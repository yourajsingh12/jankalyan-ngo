<?php
$donations = [
    ['id' => 1, 'donor_name' => 'Rajesh Kumar', 'amount' => '₹5000', 'date' => '2026-09-20', 'method' => 'Bank Transfer', 'status' => 'Confirmed', 'purpose' => 'Education Program'],
    ['id' => 2, 'donor_name' => 'Priya Sharma', 'amount' => '₹2500', 'date' => '2026-09-21', 'method' => 'Online Payment', 'status' => 'Confirmed', 'purpose' => 'Healthcare Initiative'],
    ['id' => 3, 'donor_name' => 'Amit Verma', 'amount' => '₹10000', 'date' => '2026-09-22', 'method' => 'Cheque', 'status' => 'Pending', 'purpose' => 'Relief Program'],
    ['id' => 4, 'donor_name' => 'Anonymous', 'amount' => '₹1500', 'date' => '2026-09-23', 'method' => 'Cash', 'status' => 'Confirmed', 'purpose' => 'Community Support']
];
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
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Donations</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff7ed; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-heart"></i> Donation Panel
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="white_card card_height_100 mb_30 pt-4"
                        style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:20px;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                                <div>
                                    <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Donation Records</h4>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="update_donate.php" class="btn btn-sm btn-warning" style="background:#f59e0b; border:none; border-radius:8px; padding:8px 16px; font-weight:600; color:#fff;">
                                        <i class="fas fa-cog"></i> Update Donation Page
                                    </a>
                                    <a href="add_donate.php" class="btn btn-sm btn-primary" style="background:#0f766e; border:none; border-radius:8px; padding:8px 16px; font-weight:600;">
                                        <i class="fas fa-plus"></i> Add Donation
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:1100px;">
                                    <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                        <tr>
                                            <th style="padding:14px 12px;">ID</th>
                                            <th style="padding:14px 12px;">Donor Name</th>
                                            <th style="padding:14px 12px;">Amount</th>
                                            <th style="padding:14px 12px;">Date</th>
                                            <th style="padding:14px 12px;">Method</th>
                                            <th style="padding:14px 12px;">Purpose</th>
                                            <th style="padding:14px 12px;">Status</th>
                                            <th style="padding:14px 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donations as $donation): ?>
                                            <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                                <td style="padding:14px 12px; font-weight:600; color:#334155;">
                                                    <?= $donation['id'] ?>
                                                </td>
                                                <td style="padding:14px 12px; font-weight:600; color:#0f172a;">
                                                    <?= htmlspecialchars($donation['donor_name']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569; font-weight:600;">
                                                    <?= htmlspecialchars($donation['amount']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($donation['date']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($donation['method']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($donation['purpose']) ?>
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <span style="padding:6px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; background: <?= $donation['status'] === 'Confirmed' ? '#dcfce7' : '#fef3c7' ?>; color: <?= $donation['status'] === 'Confirmed' ? '#166534' : '#b45309' ?>;">
                                                        <?= htmlspecialchars($donation['status']) ?>
                                                    </span>
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="#" class="btn btn-sm btn-primary" title="Edit" style="background:#0f766e; border:none; border-radius:8px;">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-danger" title="Delete" style="background:#dc2626; border:none; border-radius:8px; color:#fff;">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
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

    <?php include_once('footer.php'); ?>
</body>
</html>
