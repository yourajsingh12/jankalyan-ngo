<?php
$pages = [
    ['id' => 1, 'name' => 'Home', 'section' => 'Landing Page', 'updated' => '2026-09-23', 'summary' => 'Main hero banner, mission, and highlights for Jan Kalyan Sangh Foundation.'],
    ['id' => 2, 'name' => 'About', 'section' => 'Organization', 'updated' => '2026-09-21', 'summary' => 'About the NGO, impact story, and community values.'],
    ['id' => 3, 'name' => 'Mission', 'section' => 'Programs', 'updated' => '2026-09-20', 'summary' => 'Our social service goals, education, and health initiatives.'],
    ['id' => 4, 'name' => 'Projects', 'section' => 'Impact', 'updated' => '2026-09-19', 'summary' => 'Current field projects, relief efforts, and development work.'],
    ['id' => 5, 'name' => 'Contact', 'section' => 'Support', 'updated' => '2026-09-18', 'summary' => 'Contact numbers, email, support information, and donation requests.'],
    ['id' => 6, 'name' => 'Footer', 'section' => 'Global', 'updated' => '2026-09-17', 'summary' => 'Footer branding, quick links, and social media details.']
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
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Manage Content</h3>
                        </div>
                        <div class="page_date_button"
                            style="background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#e2e8f0; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-calendar-alt"></i> <?= date('F d, Y') ?>
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
                                    <h4 class="mb-0"
                                        style="font-size:1.3rem; font-weight:700; color:#0f172a;">Website Pages</h4>
                                </div>
                                <a href="edit_content.php"
                                    class="btn btn-primary"
                                    style="background:linear-gradient(135deg,#0f766e,#10b981); border:none; border-radius:10px; padding:10px 16px; font-weight:600;">
                                    <i class="fas fa-table"></i> View All Content
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle"
                                    style="border-collapse:separate; border-spacing:0; min-width:700px; border-radius:12px; overflow:hidden;">
                                    <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                        <tr>
                                            <th style="padding:14px 12px;">ID</th>
                                            <th style="padding:14px 12px;">Page Name</th>
                                            <th style="padding:14px 12px;">Section</th>
                                            <th style="padding:14px 12px;">Content Summary</th>
                                            <th style="padding:14px 12px;">Updated</th>
                                            <th style="padding:14px 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pages as $page): ?>
                                        <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                            <td style="padding:14px 12px; font-weight:600; color:#334155;"><?= $page['id'] ?>
                                            </td>
                                            <td style="padding:14px 12px; font-weight:600; color:#0f172a;">
                                                <?= htmlspecialchars($page['name']) ?>
                                            </td>
                                            <td style="padding:14px 12px; color:#475569;">
                                                <?= htmlspecialchars($page['section']) ?>
                                            </td>
                                            <td style="padding:14px 12px; color:#475569; max-width:260px;">
                                                <?= htmlspecialchars($page['summary']) ?>
                                            </td>
                                            <td style="padding:14px 12px; color:#475569;">
                                                <?= htmlspecialchars($page['updated']) ?>
                                            </td>
                                            <td style="padding:14px 12px;">
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="edit_content.php?id=<?= $page['id'] ?>"
                                                        class="btn btn-sm btn-primary" title="Edit"
                                                        style="background:#0f766e; border:none; border-radius:8px;">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger" title="Delete"
                                                        style="background:#ef4444; border:none; border-radius:8px;">
                                                        <i class="far fa-trash-alt"></i>
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
