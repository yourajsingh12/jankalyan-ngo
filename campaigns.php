<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$campaignId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$campaignFallbackImage = 'assets/img/coaching/banner.jpg';

if (isset($_GET['id']) && ($campaignId === false || $campaignId === null || $campaignId < 1)) {
    header('Location: campaigns.php');
    exit;
}

if ($campaignId !== null && $campaignId !== false) {
    $campaign = row(
        "SELECT *
           FROM campaigns
          WHERE id = ?
            AND status IN ('Active', 'Completed', 'Upcoming')",
        [$campaignId]
    );

    if ($campaign === null) {
        header('Location: campaigns.php');
        exit;
    }

    $campaignName = trim((string) $campaign['title']);
    $campaignImage = !empty($campaign['image'])
        ? img_url((string) $campaign['image'], $campaignFallbackImage)
        : base_url($campaignFallbackImage);
    $goalAmount = (float) $campaign['goal_amount'];
    $raisedAmount = (float) $campaign['raised_amount'];
    $progress = $goalAmount > 0 ? min(100, (int) round(($raisedAmount / $goalAmount) * 100)) : 0;
    $remainingAmount = max(0, $goalAmount - $raisedAmount);
    $pageTitle = $campaignName;
    include_once __DIR__ . '/head.php';
    ?>
    <body>
        <?php include_once __DIR__ . '/header.php'; ?>
        <main class="main campaigns-page">
            <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
                <div class="container">
                    <h2 class="breadcrumb-title"><?= e($campaignName) ?></h2>
                    <ul class="breadcrumb-menu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="campaigns.php">Campaigns</a></li>
                        <li class="active"><?= e($campaignName) ?></li>
                    </ul>
                </div>
            </div>

            <section class="py-100">
                <div class="container">
                    <article class="campaign-detail-card">
                        <img class="campaign-detail-image" src="<?= e($campaignImage) ?>" alt="<?= e($campaignName) ?>">
                        <div class="campaign-detail-content">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="campaign-category position-static"><?= e((string) $campaign['category']) ?></span>
                                <span class="campaign-status position-static <?= $campaign['status'] === 'Completed' ? 'is-completed' : ($campaign['status'] === 'Upcoming' ? 'is-upcoming' : '') ?>"><?= e((string) $campaign['status']) ?></span>
                            </div>
                            <h1 class="mb-3"><?= e($campaignName) ?></h1>
                            <p class="campaign-detail-copy"><?= nl2br(e((string) $campaign['description'])) ?></p>

                            <div class="campaign-amounts">
                                <span class="campaign-raised">₹<?= e(number_format($raisedAmount, 0)) ?> raised</span>
                                <span>Goal: ₹<?= e(number_format($goalAmount, 0)) ?></span>
                            </div>
                            <div class="campaign-progress" role="progressbar" aria-label="Campaign fundraising progress" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                <span style="width: <?= $progress ?>%;"></span>
                            </div>
                            <p class="campaign-remaining <?= $remainingAmount <= 0 ? 'is-complete' : '' ?>">
                                <?= $remainingAmount <= 0 ? 'Goal Completed!' : '₹' . number_format($remainingAmount, 0) . ' remaining' ?>
                            </p>

                            <div class="campaign-detail-stats">
                                <div class="campaign-detail-stat"><strong>Campaign dates</strong><span><?= e(date('d M Y', strtotime((string) $campaign['start_date']))) ?> – <?= e(date('d M Y', strtotime((string) $campaign['end_date']))) ?></span></div>
                                <div class="campaign-detail-stat"><strong>Supporters</strong><span><?= e(number_format((int) $campaign['supporters'])) ?></span></div>
                                <div class="campaign-detail-stat"><strong>Category</strong><span><?= e((string) $campaign['category']) ?></span></div>
                                <div class="campaign-detail-stat"><strong>Status</strong><span><?= e((string) $campaign['status']) ?></span></div>
                            </div>

                            <div class="mt-4">
                                <h3>Campaign Details</h3>
                                <div class="campaign-detail-copy"><?= nl2br(e((string) $campaign['full_details'])) ?></div>
                            </div>
                            <div class="d-flex flex-wrap gap-3 mt-4">
                                <a class="theme-btn" href="donate.php">Donate Now</a>
                                <a class="btn btn-outline-secondary rounded-pill px-4 py-3" href="campaigns.php">Back to Campaigns</a>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
        </main>
        <?php include_once __DIR__ . '/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$campaigns = rows(
    "SELECT id, title, category, description, image, start_date, end_date, goal_amount, raised_amount, supporters, status
       FROM campaigns
      WHERE status IN ('Active', 'Completed', 'Upcoming')
      ORDER BY is_featured DESC, sort_order ASC, id DESC"
);
$pageTitle = 'Campaigns';
$pageMeta = 'Explore Jan Kalyan Sangh Foundation campaigns and learn how your support helps communities.';
include_once __DIR__ . '/head.php';
?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>
    <main class="main campaigns-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Campaigns</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Campaigns</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <div class="campaigns-intro">
                    <span class="site-title-tagline"><i class="far fa-hands-helping"></i> Make a difference</span>
                    <h2 class="site-title">Featured <span>Campaigns</span></h2>
                    <p>Discover the campaigns working to create lasting change. Explore the details and help make each goal possible.</p>
                </div>
                <div class="row g-4">
                    <?php foreach ($campaigns as $campaign):
                        $campaignImage = !empty($campaign['image'])
                            ? img_url((string) $campaign['image'], $campaignFallbackImage)
                            : base_url($campaignFallbackImage);
                        $goalAmount = (float) $campaign['goal_amount'];
                        $raisedAmount = (float) $campaign['raised_amount'];
                        $progress = $goalAmount > 0 ? min(100, (int) round(($raisedAmount / $goalAmount) * 100)) : 0;
                        $remainingAmount = max(0, $goalAmount - $raisedAmount);
                        $campaignLink = 'campaigns.php?id=' . (int) $campaign['id'];
                    ?>
                        <div class="col-md-6 col-xl-4">
                            <article class="campaign-card">
                                <a class="campaign-card-media" href="<?= e($campaignLink) ?>" aria-label="View <?= e($campaign['title']) ?> campaign details">
                                    <img src="<?= e($campaignImage) ?>" alt="<?= e($campaign['title']) ?>">
                                    <span class="campaign-category"><?= e($campaign['category']) ?></span>
                                    <span class="campaign-status <?= $campaign['status'] === 'Completed' ? 'is-completed' : ($campaign['status'] === 'Upcoming' ? 'is-upcoming' : '') ?>"><?= e($campaign['status']) ?></span>
                                </a>
                                <div class="campaign-card-content">
                                    <h3><a href="<?= e($campaignLink) ?>"><?= e($campaign['title']) ?></a></h3>
                                    <p><?= e(excerpt((string) $campaign['description'], 120)) ?></p>
                                    <a class="campaign-read-more" href="<?= e($campaignLink) ?>">Read More <i class="fas fa-chevron-down"></i></a>
                                    <div class="campaign-dates">
                                        <span><i class="fas fa-calendar-days"></i><?= e(date('d M Y', strtotime((string) $campaign['start_date']))) ?></span>
                                        <span><i class="fas fa-calendar-check"></i><?= e(date('d M Y', strtotime((string) $campaign['end_date']))) ?></span>
                                    </div>
                                    <div class="campaign-amounts">
                                        <span class="campaign-raised">₹<?= e(number_format($raisedAmount, 0)) ?> raised</span>
                                        <span>Goal: ₹<?= e(number_format($goalAmount, 0)) ?></span>
                                    </div>
                                    <div class="campaign-progress" role="progressbar" aria-label="Campaign fundraising progress" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                        <span style="width: <?= $progress ?>%;"></span>
                                    </div>
                                    <div class="campaign-remaining <?= $remainingAmount <= 0 ? 'is-complete' : '' ?>">
                                        <?= $remainingAmount <= 0 ? '<i class="fas fa-circle-check"></i> Goal Completed!' : '<i class="fas fa-bullseye"></i> ₹' . e(number_format($remainingAmount, 0)) . ' remaining' ?>
                                    </div>
                                    <div class="campaign-supporters"><i class="fas fa-users me-1"></i> <?= e(number_format((int) $campaign['supporters'])) ?> supporters</div>
                                </div>
                                <div class="campaign-card-actions">
                                    <a class="campaign-details-button" href="<?= e($campaignLink) ?>"><i class="fas fa-circle-info me-1"></i> Details</a>
                                    <a class="campaign-donate-button" href="donate.php"><i class="fas fa-heart me-1"></i> Donate Now</a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($campaigns === []): ?>
                        <div class="col-12"><p class="text-center text-muted">Campaigns will be added soon.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
