<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

require_once dirname(__DIR__) . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

    if ($action === 'delete_contact_message') {
        $messageId = filter_var($_POST['message_id'] ?? null, FILTER_VALIDATE_INT);
        if ($messageId === false || $messageId === null || $messageId < 1) {
            http_response_code(400);
            exit('Invalid contact message.');
        }

        q('DELETE FROM contact_messages WHERE id = ?', [$messageId]);
        header('Location: index.php#contact-messages');
        exit();
    }

    if ($action === 'save_stat') {
        $statKey = is_string($_POST['stat_key'] ?? null) ? $_POST['stat_key'] : '';
        $statFields = ['donations', 'programs', 'beneficiaries', 'volunteers'];
        $label = is_string($_POST['label'] ?? null) ? trim($_POST['label']) : '';
        $value = is_string($_POST['value'] ?? null) ? trim($_POST['value']) : '';
        $trend = is_string($_POST['trend'] ?? null) ? trim($_POST['trend']) : '';

        if (
            !in_array($statKey, $statFields, true)
            || $label === ''
            || mb_strlen($label) > 80
            || $value === ''
            || mb_strlen($value) > 100
            || mb_strlen($trend) > 100
        ) {
            header('Location: index.php?dashboard_status=invalid#dashboard-stats');
            exit();
        }

        q(
            'UPDATE dashboard_stats SET label = ?, value = ?, trend = ? WHERE stat_key = ?',
            [$label, $value, $trend, $statKey]
        );
        header('Location: index.php?dashboard_status=updated#dashboard-stats');
        exit();
    }

}

$dashboardStatus = is_string($_GET['dashboard_status'] ?? null) ? $_GET['dashboard_status'] : '';
$dashboardNotice = [
    'updated' => 'Dashboard details updated successfully.',
    'invalid' => 'Please check the entered details and try again.',
][$dashboardStatus] ?? '';

$stats = rows(
    'SELECT stat_key, label, value, trend, icon, color
     FROM dashboard_stats
     ORDER BY sort_order, stat_key'
);
$donors = rows(
    "SELECT donor_name AS name, SUM(amount) AS total_amount, COUNT(*) AS donation_count
       FROM donation_records
      WHERE status = 'Confirmed'
      GROUP BY donor_name
      ORDER BY total_amount DESC, donor_name ASC
      LIMIT 10"
);
$confirmedDonationTotal = (float) val(
    "SELECT COALESCE(SUM(amount), 0) FROM donation_records WHERE status = 'Confirmed'",
    [],
    0
);

$contactMessages = rows(
    'SELECT id, name, email, phone, subject, message, created_at
     FROM contact_messages
     ORDER BY created_at DESC, id DESC'
);
$messagesByDate = [];
foreach ($contactMessages as $contactMessage) {
    $dateKey = date('Y-m-d', strtotime($contactMessage['created_at']));
    $messagesByDate[$dateKey][] = $contactMessage;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include_once('header.php'); ?>
<head>
    <style>
        body.crm_body_bg {
            background: linear-gradient(180deg, #f4f7fb 0%, #eef3f9 100%);
            color: #1f2937;
        }

        .main_content.dashboard_part {
            margin-left: 260px;
            min-height: 100vh;
            padding: 24px;
        }

        .dashboard-shell {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(148,163,184,0.2);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            padding: 24px;
        }

        .page_title_box {
            background: linear-gradient(135deg, #0f172a, #0f766e);
            padding: 20px 28px;
            border-radius: 18px;
            color: #fff;
            box-shadow: 0 16px 30px rgba(15,118,110,0.25);
            margin-bottom: 24px;
        }

        .page_title_box h3 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
        }

        .page_date_button {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 12px;
            padding: 8px 14px;
            color: #e2e8f0;
            font-size: 0.9rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 18px;
            padding: 20px 18px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 14px 30px rgba(15,23,42,0.04);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: var(--card-color, #3b82f6);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59,130,246,0.08);
            font-size: 22px;
        }

        .stat-label {
            display: block;
            color: #64748b;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-trend {
            margin-top: 10px;
            font-size: 0.8rem;
            color: #16a34a;
            font-weight: 700;
        }

        .stat-edit {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #edf2f7;
        }

        .stat-edit summary {
            color: #0f766e;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
        }

        .stat-edit-form {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .stat-edit-form label {
            display: grid;
            gap: 5px;
            color: #475569;
            font-size: .78rem;
            font-weight: 600;
        }

        .stat-edit-form input {
            width: 100%;
            min-height: 38px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #0f172a;
            background: #fff;
        }

        .dashboard-save-button {
            justify-self: start;
            padding: 8px 13px;
            border: 0;
            border-radius: 9px;
            background: #0f766e;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .dashboard-notice {
            margin-bottom: 18px;
            padding: 12px 16px;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            background: #f0fdf4;
            color: #166534;
            font-weight: 600;
        }

        .dashboard-notice.error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .panel {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 14px 30px rgba(15,23,42,0.04);
            border: 1px solid rgba(148,163,184,0.2);
            padding: 22px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .mini-label {
            background: #f0fdf4;
            color: #0f766e;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
        }

        .donor-list {
            display: grid;
            gap: 12px;
        }

        .donor-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .activity-meta {
            font-size: 0.76rem;
            color: #64748b;
            margin-top: 4px;
        }

        .contact-inbox {
            margin: 26px 0;
        }

        .contact-day {
            border-top: 1px solid #edf2f7;
            padding-top: 18px;
            margin-top: 18px;
        }

        .contact-day:first-of-type {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .contact-day-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0 0 12px;
            color: #334155;
            font-size: .9rem;
            font-weight: 700;
        }

        .contact-day-count {
            border-radius: 999px;
            padding: 4px 9px;
            background: #ecfdf5;
            color: #0f766e;
            font-size: .72rem;
        }

        .contact-message-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
            gap: 16px;
            padding: 16px;
            margin-top: 10px;
            border: 1px solid #e8eef4;
            border-radius: 15px;
            background: linear-gradient(145deg, #fff, #fbfdff);
        }

        .contact-message-subject {
            margin: 0 0 5px;
            color: #0f172a;
            font-size: .98rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .contact-message-meta {
            color: #64748b;
            font-size: .8rem;
            overflow-wrap: anywhere;
        }

        .contact-message-preview {
            margin: 9px 0 0;
            color: #475569;
            font-size: .87rem;
            line-height: 1.55;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .contact-message-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contact-message-actions button,
        .contact-message-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 7px 11px;
            border: 0;
            border-radius: 9px;
            font: inherit;
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .contact-view-button {
            background: #ecfdf5;
            color: #0f766e;
        }

        .contact-delete-button {
            background: #fff1f2;
            color: #be123c;
        }

        .contact-empty {
            padding: 28px 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            color: #64748b;
            text-align: center;
        }

        .contact-empty strong {
            display: block;
            margin-bottom: 5px;
            color: #334155;
        }

        .donor-item strong {
            display: block;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .donor-item small {
            color: #64748b;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 54px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: 0.2s ease;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #0f766e, #10b981);
            color: white;
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .action-btn.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .action-btn.dark {
            background: linear-gradient(135deg, #0f172a, #334155);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            opacity: 0.96;
        }

        @media (max-width: 1180px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(200px, 1fr)); }
            .bottom-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 992px) {
            .main_content.dashboard_part { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .main_content.dashboard_part { padding: 14px; }
            .dashboard-shell { padding: 16px; }
            .stats-grid { grid-template-columns: 1fr; }
            .quick-actions { grid-template-columns: 1fr; }
            .page_title_box { padding: 18px 20px; }
            .contact-message-card { grid-template-columns: 1fr; }
            .contact-message-actions { justify-content: flex-start; }
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include_once('sidebar.php'); ?>

    <section class="main_content dashboard_part">
        <div class="dashboard-shell">
            <div class="page_title_box d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3>Jan Kalyan Sangh Foundation</h3>
                </div>
                <div class="page_date_button">📅 <?= date('F d, Y') ?></div>
            </div>

            <?php if ($dashboardNotice !== ''): ?>
                <div class="dashboard-notice <?= $dashboardStatus === 'invalid' ? 'error' : '' ?>" role="status">
                    <?= e($dashboardNotice) ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid" id="dashboard-stats">
                <?php foreach ($stats as $stat): ?>
                    <div class="stat-card" style="--card-color: <?= e($stat['color']) ?>;">
                        <div class="stat-top">
                            <div>
                                <span class="stat-label"><?= e($stat['label']) ?></span>
                                <div class="stat-value"><?= $stat['stat_key'] === 'donations' ? '₹ ' . e(number_format($confirmedDonationTotal, 2)) : e($stat['value']) ?></div>
                            </div>
                            <div class="stat-icon" style="background: <?= $stat['color'] ?>1a; color: <?= $stat['color'] ?>;">
                                <?= e($stat['icon']) ?>
                            </div>
                        </div>
                        <?php if ($stat['trend'] !== '' && $stat['stat_key'] !== 'donations'): ?>
                            <div class="stat-trend"><?= e($stat['trend']) ?></div>
                        <?php endif; ?>
                        <details class="stat-edit">
                            <summary>Edit details</summary>
                            <form method="post" class="stat-edit-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="save_stat">
                                <input type="hidden" name="stat_key" value="<?= e($stat['stat_key']) ?>">
                                <label>Title
                                    <input type="text" name="label" maxlength="80" value="<?= e($stat['label']) ?>" required>
                                </label>
                                <?php if ($stat['stat_key'] === 'donations'): ?>
                                    <input type="hidden" name="value" value="<?= e(number_format($confirmedDonationTotal, 2)) ?>">
                                    <input type="hidden" name="trend" value="">
                                    <small class="activity-meta">Total is calculated from confirmed donation records.</small>
                                <?php else: ?>
                                    <label>Value
                                        <input type="text" name="value" maxlength="100" value="<?= e($stat['value']) ?>" required>
                                    </label>
                                    <label>Note
                                        <input type="text" name="trend" maxlength="100" value="<?= e($stat['trend']) ?>">
                                    </label>
                                <?php endif; ?>
                                <button type="submit" class="dashboard-save-button">Save changes</button>
                            </form>
                        </details>
                    </div>
                <?php endforeach; ?>
            </div>

            <section class="panel contact-inbox" id="contact-messages">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Contact Messages</h3>
                        <div class="activity-meta">Website enquiries, grouped by submission date</div>
                    </div>
                    <span class="mini-label"><?= count($contactMessages) ?> total</span>
                </div>

                <?php if ($messagesByDate === []): ?>
                    <div class="contact-empty">
                        <strong>Your inbox is all clear</strong>
                        Contact form messages will appear here when someone reaches out.
                    </div>
                <?php else: ?>
                    <?php foreach ($messagesByDate as $date => $dayMessages): ?>
                        <div class="contact-day">
                            <h4 class="contact-day-title">
                                <span><?= e(date('F j, Y', strtotime($date))) ?></span>
                                <span class="contact-day-count"><?= count($dayMessages) ?> <?= count($dayMessages) === 1 ? 'message' : 'messages' ?></span>
                            </h4>

                            <?php foreach ($dayMessages as $contactMessage): ?>
                                <article class="contact-message-card">
                                    <div>
                                        <h5 class="contact-message-subject"><?= e($contactMessage['subject'] ?: 'No subject') ?></h5>
                                        <div class="contact-message-meta">
                                            <strong><?= e($contactMessage['name']) ?></strong>
                                            · <a href="mailto:<?= e($contactMessage['email']) ?>"><?= e($contactMessage['email']) ?></a>
                                            · <a href="tel:<?= e($contactMessage['phone']) ?>"><?= e($contactMessage['phone']) ?></a>
                                            · <?= e(date('g:i A', strtotime($contactMessage['created_at']))) ?>
                                        </div>
                                        <details>
                                            <summary class="contact-view-button" style="display:inline-flex; margin-top:10px; min-height:32px; padding:6px 10px; border-radius:9px; font-size:.76rem; font-weight:700; cursor:pointer;">
                                                View full message
                                            </summary>
                                            <p class="contact-message-preview"><?= e($contactMessage['message']) ?></p>
                                        </details>
                                    </div>
                                    <div class="contact-message-actions">
                                        <a class="contact-view-button" href="mailto:<?= e($contactMessage['email']) ?>">Reply</a>
                                        <form method="post" onsubmit="return confirm('Delete this contact message?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_contact_message">
                                            <input type="hidden" name="message_id" value="<?= (int) $contactMessage['id'] ?>">
                                            <button type="submit" class="contact-delete-button">Delete</button>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <div class="bottom-grid">
                <div class="panel" id="top-donors">
                    <div class="panel-header">
                        <h3 class="panel-title">Top Donors</h3>
                        <a class="mini-label" href="donate.php">Manage donations</a>
                    </div>
                    <div class="donor-list">
                        <?php if ($donors === []): ?>
                            <div class="contact-empty">No confirmed donations yet. Add donation records to see your top donors here.</div>
                        <?php else: ?>
                            <?php foreach ($donors as $index => $donor): ?>
                                <div class="donor-item">
                                    <div>
                                        <strong><?= $index + 1 ?>. <?= e($donor['name']) ?></strong>
                                        <small><?= (int) $donor['donation_count'] ?> <?= (int) $donor['donation_count'] === 1 ? 'donation' : 'donations' ?></small>
                                    </div>
                                    <div style="font-weight:700; color:#0f172a;">₹ <?= e(number_format((float) $donor['total_amount'], 2)) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="quick-actions">
                        <a href="page_content.php" class="action-btn primary">✍ Add Content</a>
                        <a href="banner.php" class="action-btn secondary">🖼 Banner</a>
                        <a href="gallery.php" class="action-btn warning">📸 Gallery</a>
                        <a href="logout.php" class="action-btn dark">🚪 Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
