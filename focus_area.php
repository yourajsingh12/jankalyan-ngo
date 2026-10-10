<?php
require_once __DIR__ . '/includes/functions.php';

$focusId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$focusArea = $focusId !== false && $focusId !== null && $focusId > 0
    ? row('SELECT id, title, description, detail, image, icon, sort_order FROM home_features WHERE id = ? AND status = 1', [$focusId])
    : null;

if ($focusArea === null) {
    http_response_code(404);
    $pageTitle = 'Focus Area Not Found';
    $pageMeta = 'The requested focus area could not be found.';
} else {
    $pageTitle = (string) $focusArea['title'];
    $pageMeta = excerpt((string) (($focusArea['detail'] ?? '') !== '' ? $focusArea['detail'] : ($focusArea['description'] ?? '')), 160);
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
                <h2 class="breadcrumb-title"><?= $focusArea !== null ? e($focusArea['title']) : 'Focus Area Not Found' ?></h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#focus-areas">Our Focus Areas</a></li>
                    <li class="active"><?= $focusArea !== null ? e($focusArea['title']) : 'Not Found' ?></li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <?php if ($focusArea === null): ?>
                    <div class="text-center">
                        <h1 class="site-title">This focus area is unavailable</h1>
                        <p>The focus area may have been removed or is no longer published.</p>
                        <a href="index.php#focus-areas" class="theme-btn">Back to Our Focus Areas <i class="fas fa-circle-arrow-right"></i></a>
                    </div>
                <?php else:
                    $focusIcon = img_url((string) ($focusArea['icon'] ?? ''), 'assets/img/icon/team.svg');
                    $focusImage = img_url(
                        (string) ($focusArea['image'] ?? ''),
                        (int) $focusArea['sort_order'] % 2 === 0 ? 'assets/img/visa/seva2.png' : 'assets/img/visa/seva1.png'
                    );
                    $focusDetail = trim((string) (($focusArea['detail'] ?? '') !== '' ? $focusArea['detail'] : ($focusArea['description'] ?? '')));
                ?>
                    <div class="feature-detail-card">
                        <div class="row g-0 align-items-stretch">
                            <div class="col-lg-6">
                                <div class="feature-detail-image-wrap">
                                    <img class="feature-detail-image" src="<?= e($focusImage) ?>" alt="<?= e($focusArea['title']) ?>">
                                    <span class="feature-detail-image-label"><i class="fas fa-heart"></i> Together for a better tomorrow</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="feature-detail-content">
                                    <div class="feature-detail-icon">
                                        <img src="<?= e($focusIcon) ?>" alt="">
                                    </div>
                                    <span class="site-title-tagline"><i class="far fa-hands-helping"></i> Our Work</span>
                                    <h1 class="feature-detail-title"><?= e($focusArea['title']) ?></h1>
                                    <?php if (!empty($focusArea['description'])): ?>
                                        <p class="feature-detail-intro"><?= e($focusArea['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="feature-detail-copy"><?= nl2br(e($focusDetail)) ?></div>
                                    <a href="index.php#focus-areas" class="theme-btn feature-detail-back">Explore more programs <i class="fas fa-circle-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
    <style>
        .feature-detail-card { overflow: hidden; border: 1px solid #e7eee9; border-radius: 18px; background: #fff; box-shadow: 0 24px 65px rgba(24, 59, 42, .12); }
        .feature-detail-image-wrap { position: relative; height: 100%; min-height: 520px; background: #eaf4ed; }
        .feature-detail-image-wrap::after { position: absolute; inset: 45% 0 0; background: linear-gradient(transparent, rgba(10, 35, 24, .64)); content: ''; }
        .feature-detail-image { position: absolute; width: 100%; height: 100%; inset: 0; object-fit: cover; }
        .feature-detail-image-label { position: absolute; z-index: 1; right: 24px; bottom: 24px; left: 24px; color: #fff; font-weight: 700; font-size: 17px; }
        .feature-detail-image-label i { margin-right: 8px; color: #ffc36a; }
        .feature-detail-content { padding: 52px 48px; }
        .feature-detail-icon { display: flex; width: 70px; height: 70px; align-items: center; justify-content: center; margin-bottom: 24px; border: 1px solid #dceee2; border-radius: 50%; background: #eff8f1; }
        .feature-detail-icon img { width: 38px; height: 38px; }
        .feature-detail-title { margin: 16px 0; color: #183b2a; font-size: clamp(30px, 3vw, 42px); line-height: 1.15; }
        .feature-detail-intro { margin-bottom: 22px; color: #41584a; font-size: 17px; font-weight: 600; line-height: 1.8; }
        .feature-detail-copy { color: #66756b; font-size: 16px; line-height: 1.95; }
        .feature-detail-back { margin-top: 28px; }
        @media (max-width: 991.98px) { .feature-detail-image-wrap { min-height: 360px; } .feature-detail-content { padding: 36px 28px; } }
        @media (max-width: 575.98px) { .feature-detail-image-wrap { min-height: 280px; } .feature-detail-content { padding: 30px 22px; } }
    </style>
</body>
</html>
