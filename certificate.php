<?php
require_once __DIR__ . '/includes/functions.php';
$certificateIntro = sections('certificate')['intro'] ?? [];
$certificates = rows(
    'SELECT title, description, image, issued_by, issued_on
       FROM certificate_showcase
      WHERE status = 1
      ORDER BY sort_order ASC, id ASC'
);
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <main class="main">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Certificates</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Certificates</li>
                </ul>
            </div>
        </div>

        <section class="gallery-area py-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 mx-auto">
                        <div class="site-heading text-center wow fadeInDown" data-wow-delay=".25s">
                            <span class="site-title-tagline"><i class="far fa-certificate"></i> <?= e($certificateIntro['tagline'] ?? 'Our Recognition') ?></span>
                            <h1 class="site-title"><?= e($certificateIntro['heading'] ?? 'Certificates & Achievements') ?></h1>
                            <p><?= e($certificateIntro['body'] ?? 'Explore the certificates and recognitions that reflect our commitment to serving communities with care, transparency and purpose.') ?></p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 popup-gallery">
                    <?php foreach ($certificates as $certificate): ?>
                        <?php $image = img_url($certificate['image']); ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <article class="gallery-item wow fadeInUp" data-wow-delay=".25s">
                                <div class="gallery-img">
                                    <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($certificate['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="gallery-content">
                                    <h5><?= e($certificate['title']) ?></h5>
                                    <?php if (!empty($certificate['issued_by']) || !empty($certificate['issued_on'])): ?>
                                        <p class="mb-0"><?= e($certificate['issued_by']) ?><?= !empty($certificate['issued_by']) && !empty($certificate['issued_on']) ? ' · ' : '' ?><?= e(fmt_date($certificate['issued_on'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($certificate['description'])): ?>
                                        <p><?= e($certificate['description']) ?></p>
                                    <?php endif; ?>
                                    <a class="popup-img gallery-link" href="<?= e($image) ?>" aria-label="View <?= e($certificate['title']) ?>">
                                        <i class="fal fa-plus"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($certificates === []): ?>
                    <p class="text-center text-muted">Certificates will be added soon.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
