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
                            <span class="site-title-tagline"><i class="far fa-certificate"></i> Our Recognition</span>
                            <h1 class="site-title">Certificates & <span>Achievements</span></h1>
                            <p>Explore the certificates and recognitions that reflect our commitment to serving communities with care, transparency and purpose.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 popup-gallery">
                    <?php
                    $certificates = [
                        ['file' => '01.jpg', 'title' => 'Community Service Recognition'],
                        ['file' => '02.jpg', 'title' => 'Education Support Initiative'],
                        ['file' => '03.jpg', 'title' => 'Health & Nutrition Program'],
                        ['file' => '04.jpg', 'title' => 'Community Development Award'],
                        ['file' => '05.jpg', 'title' => 'Volunteer Appreciation'],
                        ['file' => '06.jpg', 'title' => 'Social Impact Recognition'],
                        ['file' => '07.jpg', 'title' => 'Partner Appreciation'],
                        ['file' => '08.jpg', 'title' => 'Outstanding Contribution'],
                    ];

                    foreach ($certificates as $certificate):
                        $image = 'assets/img/gallery/' . $certificate['file'];
                    ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <article class="gallery-item wow fadeInUp" data-wow-delay=".25s">
                                <div class="gallery-img">
                                    <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($certificate['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="gallery-content">
                                    <h5><?php echo htmlspecialchars($certificate['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <a class="popup-img gallery-link" href="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" aria-label="View <?php echo htmlspecialchars($certificate['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fal fa-plus"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
