<?php
require_once __DIR__ . '/includes/functions.php';
$aboutSections = sections('about');
$aboutPage = $aboutSections['page'] ?? [];
$aboutIntro = $aboutSections['intro'] ?? [];
$aboutExperience = $aboutSections['experience'] ?? [];
$aboutEducation = $aboutSections['education'] ?? [];
$aboutHealth = $aboutSections['health'] ?? [];
$aboutChildren = $aboutSections['counter_children'] ?? [];
$aboutFamilies = $aboutSections['counter_families'] ?? [];
$aboutVillages = $aboutSections['counter_villages'] ?? [];
$aboutPrograms = $aboutSections['counter_programs'] ?? [];
$aboutExplore = $aboutSections['explore'] ?? [];
$communityVoices = rows(
    'SELECT name, designation, message, image, rating
       FROM testimonials
      WHERE status = 1
      ORDER BY sort_order ASC, id ASC'
);
?>
<!DOCTYPE html>
<html lang="en">


<?php include_once('head.php');?>

<body>

  <?php include_once('header.php');?>


    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title"><?= e($aboutPage['heading'] ?? 'About Us') ?></h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><?= e($aboutPage['heading'] ?? 'About Us') ?></li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->


        <!-- about area -->
        <div class="about-area py-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 align-self-center">
                        <div class="about-left wow fadeInLeft" data-wow-delay=".25s">
                            <div class="about-img">
                                <div class="row">
                                    <div class="col-6">
                                        <img class="img-1" src="<?= e(img_url((string) ($aboutIntro['image'] ?? ''), 'assets/img/about/about1.png')) ?>" alt="NGO community support">
                                        <img class="shape" src="assets/img/shape/02.png" alt="shape">
                                    </div>
                                    <div class="col-6">
                                        <div class="about-experience">
                                            <h5><?= e($aboutExperience['heading'] ?? '10') ?><span>+</span></h5>
                                            <p><?= e($aboutExperience['body'] ?? 'Years of Service') ?></p>
                                        </div>
                                        <img class="img-2" src="<?= e(img_url((string) ($aboutIntro['extra'] ?? ''), 'assets/img/about/about.png')) ?>" alt="Jan Kalyan Sangh Foundation work">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="about-right wow fadeInUp" data-wow-delay=".25s">
                            <div class="site-heading mb-3">
                                <span class="site-title-tagline"><i class="far fa-hands-helping"></i> <?= e($aboutIntro['tagline'] ?? 'About Us') ?></span>
                                <h2 class="site-title"><?= e($aboutIntro['heading'] ?? 'Empowering communities through education, health, and dignity') ?></h2>
                            </div>

                            <p class="about-text">
                                <?= e($aboutIntro['body'] ?? 'Jan Kalyan Sangh Foundation is a grassroots social impact organisation dedicated to uplifting underserved communities through education support, health awareness, women empowerment, and sustainable local development.') ?>
                            </p>

                            <div class="about-content">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="about-item">
                                            <div class="icon">
                                                <img src="assets/img/icon/accurate.svg" alt="Education support icon">
                                            </div>
                                            <div class="content">
                                                <h6><?= e($aboutEducation['heading'] ?? 'Education Support') ?></h6>
                                                <p><?= e($aboutEducation['body'] ?? 'We help children continue their learning with school access, learning material, and mentorship.') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="about-item">
                                            <div class="icon">
                                                <img src="assets/img/icon/support.svg" alt="Health and care icon">
                                            </div>
                                            <div class="content">
                                                <h6><?= e($aboutHealth['heading'] ?? 'Health & Awareness') ?></h6>
                                                <p><?= e($aboutHealth['body'] ?? 'We organize health camps, nutrition support, and awareness programs for rural families.') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="<?= e($aboutExplore['extra'] ?? 'mission.php') ?>" class="theme-btn"><?= e($aboutExplore['heading'] ?? 'Explore More') ?><i class="fas fa-circle-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- about area end -->

        <div class="counter-area">
            <div class="counter-wrap">
                <div class="shape">
                    <img src="assets/img/shape/03.png" alt="shape">
                </div>
                <div class="container">
                    <div class="row g-4">
                        <div class="col-lg-3 col-sm-6">
                            <div class="counter-box">
                                <div class="icon">
                                    <img src="assets/img/icon/visa.svg" alt="Children supported icon">
                                </div>
                                <div>
                                    <span class="counter" data-count="+" data-to="<?= e($aboutChildren['heading'] ?? '5000') ?>" data-speed="3000"><?= e($aboutChildren['heading'] ?? '5000') ?></span>
                                    <h6 class="title"><?= e($aboutChildren['body'] ?? '+ Children Supported') ?></h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="counter-box mt-lg-5">
                                <div class="icon">
                                    <img src="assets/img/icon/happy.svg" alt="Families helped icon">
                                </div>
                                <div>
                                    <span class="counter" data-count="+" data-to="<?= e($aboutFamilies['heading'] ?? '6000') ?>" data-speed="3000"><?= e($aboutFamilies['heading'] ?? '6000') ?></span>
                                    <h6 class="title"><?= e($aboutFamilies['body'] ?? '+ Families Reached') ?></h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="counter-box">
                                <div class="icon">
                                    <img src="assets/img/icon/country.svg" alt="Villages served icon">
                                </div>
                                <div>
                                    <span class="counter" data-count="+" data-to="<?= e($aboutVillages['heading'] ?? '40') ?>" data-speed="3000"><?= e($aboutVillages['heading'] ?? '40') ?></span>
                                    <h6 class="title"><?= e($aboutVillages['body'] ?? '+ Villages Impacted') ?></h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="counter-box mt-lg-5">
                                <div class="icon">
                                    <img src="assets/img/icon/award.svg" alt="Impact programs icon">
                                </div>
                                <div>
                                    <span class="counter" data-count="+" data-to="<?= e($aboutPrograms['heading'] ?? '25') ?>" data-speed="3000"><?= e($aboutPrograms['heading'] ?? '25') ?></span>
                                    <h6 class="title"><?= e($aboutPrograms['body'] ?? '+ Community Programs') ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="partner-area py-100"></div>

        <?php if ($communityVoices !== []): ?>
        <div class="testimonial-area testimonial-bg pt-80 pb-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 mx-auto">
                        <div class="site-heading text-center wow fadeInDown" data-wow-delay=".25s">
                            <span class="site-title-tagline"><i class="far fa-heart"></i> Community Voices</span>
                            <h2 class="site-title text-white">Stories of hope, care, and change</h2>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slider owl-carousel owl-theme wow fadeInUp" data-wow-delay=".25s">
                    <?php foreach ($communityVoices as $voice): ?>
                    <div class="testimonial-item">
                        <div class="testimonial-quote-icon">
                            <img src="assets/img/icon/quote.svg" alt="quote icon">
                        </div>
                        <div class="testimonial-content">
                            <div class="testimonial-author-img">
                                <img src="<?= e($voice['image'] ?: 'assets/img/testimonial/01.jpg') ?>" alt="<?= e($voice['name']) ?>">
                            </div>
                            <div class="testimonial-author-info">
                                <h4><?= e($voice['name']) ?></h4>
                                <?php if ($voice['designation'] !== null && $voice['designation'] !== ''): ?>
                                    <p><?= e($voice['designation']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="testimonial-quote">
                            <p><?= e($voice['message']) ?></p>
                        </div>
                        <div class="testimonial-rate">
                            <?php for ($star = 0; $star < (int) $voice['rating']; $star++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="partner-area py-100"></div>
    </main>


    <?php include_once('footer.php');?>
</body>


<!-- Mirrored from live.themewild.com/visarx/about.php by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Nov 2025 06:36:36 GMT -->
</html>