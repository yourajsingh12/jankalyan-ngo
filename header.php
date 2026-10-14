<?php
require_once __DIR__ . '/includes/functions.php';
$headerTickerItems = rows(
    'SELECT text FROM ticker_items WHERE status = 1 ORDER BY sort_order ASC, id ASC'
);
$socialLinks = rows(
    'SELECT platform, icon_class, url FROM social_links WHERE status = 1 ORDER BY sort_order ASC, id ASC'
);
$contactEmail = setting('contact_email');
$contactPhones = contact_phone_numbers(setting('contact_phone'));
$contactAddress = setting('contact_address_plain');
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
                    <img class="site-brand__logo" src="assets/img/logo/study.png" alt="Jan Kalyan Sangh Foundation logo">
                    <span class="site-brand__name">Jan Kalyan Sangh Foundation</span>
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
                            <img class="site-brand__logo" src="assets/img/logo/study.png" alt="Jan Kalyan Sangh Foundation logo">
                            <span class="site-brand__name">Jan Kalyan Sangh Foundation</span>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                                class="far fa-xmark"></i></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item dropdown nav-section-dropdown about-dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">About Us</a>
                                <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                    <li><a class="dropdown-item" href="about.php"><i class="fas fa-circle-info"></i> About Us</a></li>
                                    <li><a class="dropdown-item" href="mission.php"><i class="fas fa-bullseye"></i> Mission</a></li>
                                    <li><a class="dropdown-item" href="team.php"><i class="fas fa-people-group"></i> Team</a></li>
                                    <li><a class="dropdown-item" href="transparency.php"><i class="fas fa-file-pdf"></i> Transparency</a></li>
                                    <li><a class="dropdown-item" href="certificate.php"><i class="fas fa-certificate"></i> Certificates</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown nav-section-dropdown our-work-dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="ourWorkDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-sparkles"></i> Our Work</a>
                                <ul class="dropdown-menu" aria-labelledby="ourWorkDropdown">
                                    <li><a class="dropdown-item" href="projects.php"><i class="fas fa-diagram-project"></i> Projects</a></li>
                                    <li><a class="dropdown-item" href="achievements.php"><i class="fas fa-trophy"></i> Achievements</a></li>
                                    <li><a class="dropdown-item" href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
                                    <li><a class="dropdown-item" href="campaigns.php"><i class="fas fa-hand-holding-heart"></i> Campaigns</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="participate.php" id="participateDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">Participate</a>
                                <ul class="dropdown-menu" aria-labelledby="participateDropdown">
                                    <li><a class="dropdown-item" href="participate.php">Participate With Us</a></li>
                                    <li><a class="dropdown-item" href="participant_login.php"><i class="fas fa-id-card"></i> Participant Login</a></li>
                                </ul>
                            </li>
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
            
            <div class="sidebar-popup-contact">
                <h4>Contact Info</h4>
                <ul>
                    <li>
                        <div class="icon">
                            <i class="far fa-envelope"></i>
                        </div>
                        <div class="content">
                            <h6>Email</h6>
                            <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <i class="far fa-phone"></i>
                        </div>
                        <div class="content">
                            <h6>Phone</h6>
                            <?php foreach ($contactPhones as $contactPhone): ?>
                                <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $contactPhone)) ?>"><?= e($contactPhone) ?></a><br>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <i class="far fa-location-dot"></i>
                        </div>
                        <div class="content">
                            <h6>Address</h6>
                            <a href="#"><?= nl2br(e($contactAddress)) ?></a>
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

<style>
    .navbar .navbar-brand,
    .navbar .offcanvas-brand {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        white-space: normal;
    }

    .navbar .navbar-brand {
        flex: 0 1 auto;
        min-width: 0;
    }

    .navbar .navbar-brand img.site-brand__logo,
    .navbar .offcanvas-brand img.site-brand__logo {
        width: 92px;
        height: 92px;
        flex: 0 0 auto;
        object-fit: contain;
        transform: scale(1.18);
    }

    .site-brand__name {
        max-width: none;
        color: #0c2d8a;
        font-family: 'Playfair Display', serif;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: .025em;
        overflow-wrap: normal;
        white-space: nowrap;
    }

    .navbar .offcanvas-brand .site-brand__name {
        color: #0c2d8a;
    }

    .nav-section-dropdown .nav-link i {
        margin-right: 5px;
        color: var(--theme-color);
        font-size: 14px;
    }

    .navbar .nav-section-dropdown .dropdown-menu {
        width: 255px;
        padding: 12px;
        border: 1px solid rgba(24, 59, 42, .08);
        box-shadow: 0 18px 42px rgba(24, 59, 42, .16);
    }

    .navbar .nav-section-dropdown .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
    }

    .nav-section-dropdown .dropdown-item i {
        width: 18px;
        color: var(--theme-color);
        text-align: center;
    }

    @media (min-width: 992px) {
        .navbar .nav-item .nav-link {
            margin-right: 18px;
        }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
        .navbar .nav-item .nav-link {
            margin-right: 12px;
        }
    }

    @media (max-width: 991px) {
        .navbar .navbar-brand {
            flex: 1 1 auto;
            max-width: calc(100% - 48px);
        }

        .navbar .navbar-brand img.site-brand__logo {
            width: 62px;
            height: 62px;
            transform: scale(1.15);
        }

        .navbar .navbar-brand,
        .navbar .offcanvas-brand {
            gap: 10px;
        }

        .site-brand__name {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 190px;
            font-size: 12px;
            line-height: 1.25;
            white-space: normal;
        }

        .navbar .offcanvas-brand {
            width: auto;
            max-width: calc(100% - 40px);
        }

        .navbar .offcanvas-brand img.site-brand__logo {
            width: 62px;
            height: 62px;
            margin-right: 12px;
            transform: scale(1.15);
        }

        .navbar .mobile-menu-btn {
            display: none;
        }

        .navbar .mobile-menu-right {
            gap: 0;
        }

        .navbar .nav-section-dropdown .dropdown-menu {
            width: auto;
            padding: 8px;
            box-shadow: none;
        }
    }
</style>