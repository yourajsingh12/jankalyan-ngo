<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($action !== 'delete' || $id === false || $id === null || $id < 1) {
        flash('The requested action is not valid.', 'error');
    } elseif (row('SELECT id FROM donation_records WHERE id = ?', [$id]) === null) {
        flash('That donation could not be found.', 'error');
    } else {
        q('DELETE FROM donation_records WHERE id = ?', [$id]);
        flash('Donation record deleted successfully.');
    }

    header('Location: donate.php');
    exit;
}

$donations = rows(
    'SELECT id, donor_name, donor_mobile, amount, donation_date, payment_method, purpose, status
       FROM donation_records
      ORDER BY donation_date DESC, id DESC'
);
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>
<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#f59e0b); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(245,158,11,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Donations</h3>
                <a href="../donate.php" target="_blank" rel="noopener" class="btn btn-light">
                    <i class="fas fa-external-link-alt"></i> Public donate page
                </a>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>"
                    role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <div>
                            <h4 class="mb-1" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Donation Records</h4>
                            <p class="mb-0 text-muted">Add and manage donation records here. Confirmed donations automatically update Top Donors on the dashboard.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="update_donate.php" class="btn btn-sm btn-warning" style="background:#f59e0b; border:none; border-radius:8px; padding:8px 16px; font-weight:600; color:#fff;">
                                <i class="fas fa-cog"></i> Edit Donate Page
                            </a>
                            <a href="add_donate.php" class="btn btn-sm btn-primary" style="background:#0f766e; border:none; border-radius:8px; padding:8px 16px; font-weight:600;">
                                <i class="fas fa-plus"></i> Add Donation
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:1000px;">
                            <thead style="background:linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                <tr>
                                    <th style="padding:14px 12px;">ID</th>
                                    <th style="padding:14px 12px;">Donor Name</th>
                                    <th style="padding:14px 12px;">Mobile</th>
                                    <th style="padding:14px 12px;">Amount</th>
                                    <th style="padding:14px 12px;">Date</th>
                                    <th style="padding:14px 12px;">Method</th>
                                    <th style="padding:14px 12px;">Purpose</th>
                                    <th style="padding:14px 12px;">Status</th>
                                    <th style="padding:14px 12px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($donations === []): ?>
                                    <tr><td colspan="9" class="text-center text-muted py-4">No donation records yet. Add the first donation to get started.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($donations as $donation): ?>
                                    <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                        <td style="padding:14px 12px; font-weight:600; color:#334155;"><?= (int) $donation['id'] ?></td>
                                        <td style="padding:14px 12px; font-weight:600; color:#0f172a;"><?= e($donation['donor_name']) ?></td>
                                        <td style="padding:14px 12px; color:#475569;"><?= e($donation['donor_mobile'] ?: '—') ?></td>
                                        <td style="padding:14px 12px; color:#475569; font-weight:600;">₹ <?= e(number_format((float) $donation['amount'], 2)) ?></td>
                                        <td style="padding:14px 12px; color:#475569;"><?= e($donation['donation_date']) ?></td>
                                        <td style="padding:14px 12px; color:#475569;"><?= e($donation['payment_method'] ?: '—') ?></td>
                                        <td style="padding:14px 12px; color:#475569;"><?= e($donation['purpose'] ?: '—') ?></td>
                                        <td style="padding:14px 12px;">
                                            <span style="padding:6px 10px; border-radius:999px; font-size:.75rem; font-weight:700; background:<?= $donation['status'] === 'Confirmed' ? '#dcfce7' : ($donation['status'] === 'Pending' ? '#fef3c7' : '#fee2e2') ?>; color:<?= $donation['status'] === 'Confirmed' ? '#166534' : ($donation['status'] === 'Pending' ? '#b45309' : '#b91c1c') ?>;">
                                                <?= e($donation['status']) ?>
                                            </span>
                                        </td>
                                        <td style="padding:14px 12px;">
                                            <div class="d-flex gap-2">
                                                <a href="add_donate.php?edit=<?= (int) $donation['id'] ?>" class="btn btn-sm btn-primary" title="Edit donation" style="background:#0f766e; border:none; border-radius:8px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="post" onsubmit="return confirm('Delete this donation record?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int) $donation['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete donation" style="border-radius:8px;">
                                                        <i class="fas fa-trash"></i>
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
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
