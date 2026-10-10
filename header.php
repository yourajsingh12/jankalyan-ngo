<?php
require_once __DIR__ . '/includes/functions.php';
$headerTickerItems = rows(
    'SELECT text FROM ticker_items WHERE status = 1 ORDER BY sort_order ASC, id ASC'
);
$socialLinks = rows(
    'SELECT platform, icon_class, url FROM social_links WHERE status = 1 ORDER BY sort_order ASC, id ASC'
);
?>

<!-- header area -->
<header class="header">

    <!-- header top -->
    <div class="header-top">
        <div class="container">
            <div class="header-top-wrap">
                <div class="header-top-left">
                    <?php if ($headerTickerItems !== []): ?>
                        <div class="header-top-list">
                            <div class="header-top-track">
                                <?php for ($copy = 0; $copy < 2; $copy++): ?>
                                    <?php foreach ($headerTickerItems as $item): ?>
                                        <span><?= e($item['text']) ?></span>
                                    <?php endforeach; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="header-top-right">
                    <div class="header-top-social">
                        <span>Follow Us: </span>
                        <?php foreach ($socialLinks as $socialLink): ?>
                            <a href="<?= e($socialLink['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($socialLink['platform']) ?>"><i class="<?= e($socialLink['icon_class']) ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- header top end -->

    <!-- navbar -->
    <div class="main-navigation">
        <nav class="navbar navbar-expand-lg">
            <div class="container position-relative">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/img/logo/study.png" alt="StudyLoan Experts logo">
                </a>
                <div class="mobile-menu-right">
                    <div class="mobile-menu-btn">
                        <button type="button" class="nav-right-link search-box-outer"><i
                                class="far fa-search"></i></button>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                        aria-label="Toggle navigation">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <a href="index.php" class="offcanvas-brand" id="offcanvasNavbarLabel">
                            <img src="assets/img/logo/study.png" alt="StudyLoan Experts logo">
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                                class="far fa-xmark"></i></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                            <li class="nav-item"><a class="nav-link" href="certificate.php">Certificate</a></li>
                            <li class="nav-item"><a class="nav-link" href="achievements.php">Achievements</a></li>
                            <li class="nav-item"><a class="nav-link" href="projects.php">Projects</a></li>
                            <li class="nav-item"><a class="nav-link" href="team.php">Team</a></li>
                            <li class="nav-item"><a class="nav-link" href="mission.php">Mission</a></li>
                            <li class="nav-item"><a class="nav-link" href="participate.php">Participate</a></li>
                            <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        
                        </ul>
                        <!-- nav-right -->
                        <div class="nav-right">
                            <div class="nav-btn">
                                <a href="donate.php" class="theme-btn">Donate<i
                                        class="fas fa-circle-arrow-right"></i></a>
                            </div>
                            <button type="button" class="sidebar-btn nav-right-link" data-bs-toggle="offcanvas"
                                data-bs-target="#sidebarPopup">
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- navbar end-->

</header>
<!-- header area end -->


<!-- sidebar-popup -->
<div class="sidebar-popup offcanvas offcanvas-end" tabindex="-1" id="sidebarPopup">
    <div class="offcanvas-header">
        <a href="index.php" class="sidebar-popup-logo">
            <img src="assets/img/logo/study.png" alt="StudyLoan Experts logo">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="far fa-xmark"></i>
        </button>
    </div>
    <div class="sidebar-popup-wrap offcanvas-body">
        <div class="sidebar-popup-content">
            <div class="sidebar-popup-about">
                <h4>About Us</h4>
                <p>There are many loan options available in the market, but choosing the right one can be confusing. Our experts ensure you get the most suitable loan with transparent terms, quick processing, and complete guidance at every step. We make the entire loan process simple, secure, and stress-free for you.</p>
            </div>
            <div class="sidebar-popup-contact">
                <h4>Contact Info</h4>
                <ul>
                    <li>
                        <div class="icon">
                            <i class="far fa-envelope"></i>
                        </div>
                        <div class="content">
                            <h6>Email</h6>
                            <a href="mailto:support@jankalyansanghfoundation.com">support@jankalyansanghfoundation.com</a>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <i class="far fa-phone"></i>
                        </div>
                        <div class="content">
                            <h6>Phone</h6>
                            <a href="tel:+918929939502">+91 8929939502</a>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <i class="far fa-location-dot"></i>
                        </div>
                        <div class="content">
                            <h6>Address</h6>
                            <a href="#">252-A/2, KH No-44/18,<br>
                            Nangli Vihar Extn., Baprola BLK C5&amp;C6,<br>
                            New Delhi, India</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="sidebar-popup-social">
                <h4>Follow Us</h4>
                <?php foreach ($socialLinks as $socialLink): ?>
                    <a href="<?= e($socialLink['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($socialLink['platform']) ?>"><i class="<?= e($socialLink['icon_class']) ?>"></i></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- sidebar-popup end -->