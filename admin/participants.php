<?php
$participants = [
    ['id' => 1, 'name' => 'Rakesh Verma', 'email' => 'rakesh@example.com', 'mobile' => '9876543210', 'designation' => 'Volunteer', 'status' => 'Approved', 'submitted_at' => '2026-09-21'],
    ['id' => 2, 'name' => 'Neha Sharma', 'email' => 'neha@example.com', 'mobile' => '9123456780', 'designation' => 'Coordinator', 'status' => 'Pending', 'submitted_at' => '2026-09-22'],
    ['id' => 3, 'name' => 'Aarav Mehta', 'email' => 'aarav@example.com', 'mobile' => '9988776655', 'designation' => 'Member', 'status' => 'Approved', 'submitted_at' => '2026-09-23'],
    ['id' => 4, 'name' => 'Sonal Gupta', 'email' => 'sonal@example.com', 'mobile' => '9654321870', 'designation' => 'Supporter', 'status' => 'Pending', 'submitted_at' => '2026-09-23']
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
                        style="background: linear-gradient(135deg,#0f172a,#0ea5e9); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(14,165,233,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Participants</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#e0f2fe; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-users"></i> Participation Panel
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
                                    <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Participation Records</h4>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:1100px;">
                                    <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                        <tr>
                                            <th style="padding:14px 12px;">ID</th>
                                            <th style="padding:14px 12px;">Name</th>
                                            <th style="padding:14px 12px;">Email</th>
                                            <th style="padding:14px 12px;">Mobile</th>
                                            <th style="padding:14px 12px;">Designation</th>
                                            <th style="padding:14px 12px;">Submitted</th>
                                            <th style="padding:14px 12px;">Status</th>
                                            <th style="padding:14px 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participants as $person): ?>
                                            <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                                <td style="padding:14px 12px; font-weight:600; color:#334155;">
                                                    <?= $person['id'] ?>
                                                </td>
                                                <td style="padding:14px 12px; font-weight:600; color:#0f172a;">
                                                    <?= htmlspecialchars($person['name']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($person['email']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($person['mobile']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($person['designation']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($person['submitted_at']) ?>
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <span style="padding:6px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; background: <?= $person['status'] === 'Approved' ? '#dcfce7' : '#fef3c7' ?>; color: <?= $person['status'] === 'Approved' ? '#166534' : '#b45309' ?>;">
                                                        <?= htmlspecialchars($person['status']) ?>
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
