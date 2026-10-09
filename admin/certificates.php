<?php
$certificates = [
    ['id' => 1, 'name' => 'Rakesh Verma', 'course' => 'Volunteer Recognition', 'issued_on' => '2026-09-21', 'status' => 'Approved', 'reference' => 'CERT-2026-001'],
    ['id' => 2, 'name' => 'Neha Sharma', 'course' => 'Health & Nutrition Support', 'issued_on' => '2026-09-22', 'status' => 'Pending', 'reference' => 'CERT-2026-002'],
    ['id' => 3, 'name' => 'Aarav Mehta', 'course' => 'Community Care Program', 'issued_on' => '2026-09-23', 'status' => 'Approved', 'reference' => 'CERT-2026-003'],
    ['id' => 4, 'name' => 'Sonal Gupta', 'course' => 'Women Empowerment', 'issued_on' => '2026-09-23', 'status' => 'Pending', 'reference' => 'CERT-2026-004']
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
                        style="background: linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(15,118,110,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Certificates</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ecfeff; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-certificate"></i> Certificate Panel
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
                                    <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Issued Certificates</h4>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:1100px;">
                                    <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                        <tr>
                                            <th style="padding:14px 12px;">ID</th>
                                            <th style="padding:14px 12px;">Name</th>
                                            <th style="padding:14px 12px;">Course / Program</th>
                                            <th style="padding:14px 12px;">Reference</th>
                                            <th style="padding:14px 12px;">Issued On</th>
                                            <th style="padding:14px 12px;">Status</th>
                                            <th style="padding:14px 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($certificates as $certificate): ?>
                                            <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                                <td style="padding:14px 12px; font-weight:600; color:#334155;">
                                                    <?= $certificate['id'] ?>
                                                </td>
                                                <td style="padding:14px 12px; font-weight:600; color:#0f172a;">
                                                    <?= htmlspecialchars($certificate['name']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($certificate['course']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($certificate['reference']) ?>
                                                </td>
                                                <td style="padding:14px 12px; color:#475569;">
                                                    <?= htmlspecialchars($certificate['issued_on']) ?>
                                                </td>
                                                <td style="padding:14px 12px;">
                                                    <span style="padding:6px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; background: <?= $certificate['status'] === 'Approved' ? '#dcfce7' : '#fef3c7' ?>; color: <?= $certificate['status'] === 'Approved' ? '#166534' : '#b45309' ?>;">
                                                        <?= htmlspecialchars($certificate['status']) ?>
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
