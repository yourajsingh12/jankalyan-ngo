<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

$stats = [
    ['label' => 'Total Donations', 'value' => '₹ 4,82,500', 'trend' => '+18.2%', 'icon' => '💰', 'color' => '#22c55e'],
    ['label' => 'Active Programs', 'value' => '12', 'trend' => '+3 this month', 'icon' => '📚', 'color' => '#0f766e'],
    ['label' => 'Beneficiaries', 'value' => '8,240', 'trend' => '+12.4%', 'icon' => '🤝', 'color' => '#f59e0b'],
    ['label' => 'Volunteers', 'value' => '356', 'trend' => '+9 new', 'icon' => '👥', 'color' => '#ec4899']
];

$programs = [
    ['name' => 'Education Support', 'progress' => 82, 'amount' => '₹ 1.6L', 'color' => '#0f766e'],
    ['name' => 'Health & Nutrition', 'progress' => 68, 'amount' => '₹ 1.2L', 'color' => '#10b981'],
    ['name' => 'Women Empowerment', 'progress' => 74, 'amount' => '₹ 94K', 'color' => '#f59e0b'],
    ['name' => 'Community Relief', 'progress' => 58, 'amount' => '₹ 76K', 'color' => '#ef4444']
];

$recentActivity = [
    ['title' => 'School kits delivered', 'meta' => 'Education Program · 2 hours ago', 'type' => 'success'],
    ['title' => 'Medical camp scheduled', 'meta' => 'Health Initiative · Today', 'type' => 'info'],
    ['title' => 'New volunteer onboarded', 'meta' => 'People Team · Yesterday', 'type' => 'warning'],
    ['title' => 'Relief support requested', 'meta' => 'Field Office · 3 days ago', 'type' => 'danger']
];

$donors = [
    ['name' => 'Aarav Mehta', 'amount' => '₹ 25,000', 'type' => 'Education'],
    ['name' => 'Neha Sharma', 'amount' => '₹ 18,500', 'type' => 'Health'],
    ['name' => 'Rakesh Verma', 'amount' => '₹ 40,000', 'type' => 'Relief'],
    ['name' => 'Sonal Gupta', 'amount' => '₹ 12,000', 'type' => 'Women'],
];
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

        .content-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 22px;
            margin-bottom: 26px;
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

        .bar-chart {
            display: grid;
            grid-template-columns: repeat(7, minmax(0,1fr));
            gap: 14px;
            align-items: end;
            height: 220px;
            padding-top: 18px;
        }

        .bar-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: end;
            height: 100%;
            gap: 8px;
        }

        .bar {
            width: 100%;
            max-width: 36px;
            border-radius: 12px 12px 0 0;
            background: linear-gradient(180deg, #4f46e5, #7c3aed);
            box-shadow: 0 12px 18px rgba(79,70,229,0.2);
        }

        .bar-label {
            color: #64748b;
            font-size: 0.74rem;
        }

        .program-list {
            display: grid;
            gap: 16px;
        }

        .program-item {
            background: #f8fafc;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid #edf2f7;
        }

        .program-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 0.92rem;
            color: #1e293b;
        }

        .progress-bar {
            height: 10px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--prog-color), rgba(255,255,255,0.8));
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 22px;
        }

        .activity-list, .donor-list {
            display: grid;
            gap: 12px;
        }

        .activity-item, .donor-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            font-size: 16px;
        }

        .activity-meta {
            font-size: 0.76rem;
            color: #64748b;
            margin-top: 4px;
        }

        .tag {
            border-radius: 999px;
            padding: 5px 8px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .tag.success { background: #dcfce7; color: #15803d; }
        .tag.info { background: #ccfbf1; color: #0f766e; }
        .tag.warning { background: #fef3c7; color: #b45309; }
        .tag.danger { background: #fee2e2; color: #b91c1c; }

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
            .content-grid, .bottom-grid { grid-template-columns: 1fr; }
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

            <div class="stats-grid">
                <?php foreach ($stats as $stat): ?>
                    <div class="stat-card" style="--card-color: <?= $stat['color'] ?>;">
                        <div class="stat-top">
                            <div>
                                <span class="stat-label"><?= $stat['label'] ?></span>
                                <div class="stat-value"><?= $stat['value'] ?></div>
                            </div>
                            <div class="stat-icon" style="background: <?= $stat['color'] ?>1a; color: <?= $stat['color'] ?>;">
                                <?= $stat['icon'] ?>
                            </div>
                        </div>
                        <div class="stat-trend"><?= $stat['trend'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="content-grid">
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">Monthly Impact</h3>
                        <span class="mini-label">This Year</span>
                    </div>

                    <div class="bar-chart">
                        <?php $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul']; $monthValues = [38, 52, 48, 72, 68, 82, 90]; foreach ($monthLabels as $index => $label): ?>
                            <div class="bar-col">
                                <div class="bar" style="height: <?= $monthValues[$index] ?>%;"></div>
                                <div class="bar-label"><?= $label ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">Program Health</h3>
                        <span class="mini-label">Live</span>
                    </div>

                    <div class="program-list">
                        <?php foreach ($programs as $program): ?>
                            <div class="program-item">
                                <div class="program-head">
                                    <span><?= $program['name'] ?></span>
                                    <span><?= $program['amount'] ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $program['progress'] ?>%; --prog-color: <?= $program['color'] ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bottom-grid">
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">Recent Activity</h3>
                        <span class="mini-label">Updates</span>
                    </div>
                    <div class="activity-list">
                        <?php foreach ($recentActivity as $item): ?>
                            <div class="activity-item">
                                <div style="display:flex; align-items:center; gap:12px; flex:1;">
                                    <div class="activity-icon"><?= $item['type'] === 'success' ? '✓' : ($item['type'] === 'info' ? 'ℹ' : ($item['type'] === 'warning' ? '⚠' : '✦')) ?></div>
                                    <div>
                                        <strong><?= $item['title'] ?></strong>
                                        <div class="activity-meta"><?= $item['meta'] ?></div>
                                    </div>
                                </div>
                                <span class="tag <?= $item['type'] ?>"><?= ucfirst($item['type']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">Top Donors</h3>
                        <span class="mini-label">This Week</span>
                    </div>
                    <div class="donor-list">
                        <?php foreach ($donors as $donor): ?>
                            <div class="donor-item">
                                <div>
                                    <strong><?= $donor['name'] ?></strong>
                                    <small><?= $donor['type'] ?></small>
                                </div>
                                <div style="font-weight:700; color:#0f172a;"><?= $donor['amount'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="quick-actions">
                        <a href="editor.php" class="action-btn primary">✍ Add Content</a>
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
