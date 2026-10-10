<?php
require_once __DIR__ . '/includes/functions.php';

$featureId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$feature = $featureId !== false && $featureId !== null && $featureId > 0
    ? row('SELECT id, title, description, detail, icon FROM home_features WHERE id = ? AND status = 1', [$featureId])
    : null;

if ($feature === null) {
    http_response_code(404);
    $pageTitle = 'Homepage Feature Not Found';
    $pageMeta = 'The requested homepage feature could not be found.';
} else {
    $pageTitle = (string) $feature['title'];
    $pageMeta = excerpt((string) (($feature['detail'] ?? '') !== '' ? $feature['detail'] : ($feature['description'] ?? '')), 160);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <main class="main">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title"><?= $feature !== null ? e($feature['title']) : 'Feature Not Found' ?></h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><?= $feature !== null ? e($feature['title']) : 'Not Found' ?></li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <?php if ($feature === null): ?>
                    <div class="text-center">
                        <h1 class="site-title">This homepage feature is unavailable</h1>
                        <p>The feature may have been removed or is no longer published.</p>
                        <a href="index.php" class="theme-btn">Back to Home <i class="fas fa-circle-arrow-right"></i></a>
                    </div>
                <?php else:
                    $featureIcon = img_url((string) ($feature['icon'] ?? ''), 'assets/img/icon/team.svg');
                    $featureDetail = trim((string) (($feature['detail'] ?? '') !== '' ? $feature['detail'] : ($feature['description'] ?? '')));
                ?>
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <div class="text-center mb-4">
                                <div style="width:76px; height:76px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:20px; border-radius:50%; background:#e4f2e8;">
                                    <img src="<?= e($featureIcon) ?>" alt="" style="width:42px; height:42px;">
                                </div>
                                <span class="site-title-tagline"><i class="far fa-hands-helping"></i> Our Work</span>
                                <h1 class="site-title"><?= e($feature['title']) ?></h1>
                                <?php if (!empty($feature['description'])): ?>
                                    <p style="color:#667085; line-height:1.8;"><?= e($feature['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($featureDetail !== ''): ?>
                                <div style="color:#667085; font-size:17px; line-height:1.9;"><?= nl2br(e($featureDetail)) ?></div>
                            <?php endif; ?>
                            <div class="text-center mt-5">
                                <a href="index.php" class="theme-btn">Back to Home <i class="fas fa-circle-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
