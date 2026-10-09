<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <style>
        .achievement-page {
            background: #f8faf7;
        }

        .achievement-intro {
            max-width: 760px;
            margin: 0 auto 48px;
            text-align: center;
        }

        .achievement-intro p,
        .achievement-card p,
        .impact-card p {
            color: #667085;
            line-height: 1.8;
        }

        .impact-card {
            height: 100%;
            padding: 28px;
            background: #fff;
            border-bottom: 4px solid var(--theme-color);
            box-shadow: 0 10px 30px rgba(31, 55, 43, .08);
        }

        .impact-card .icon {
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border-radius: 50%;
            background: rgba(33, 121, 86, .1);
        }

        .impact-card .icon img {
            width: 34px;
            height: 34px;
        }

        .impact-card h3 {
            margin-bottom: 8px;
            color: var(--theme-color);
            font-size: 36px;
        }

        .impact-card h5 {
            margin-bottom: 10px;
        }

        .achievement-timeline {
            position: relative;
            max-width: 980px;
            margin: 0 auto;
        }

        .achievement-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background: #d8e4dc;
            transform: translateX(-50%);
        }

        .achievement-row {
            position: relative;
            display: flex;
            align-items: center;
            width: 50%;
            padding: 0 42px 42px 0;
        }

        .achievement-row:nth-child(even) {
            margin-left: 50%;
            padding: 0 0 42px 42px;
        }

        .achievement-dot {
            position: absolute;
            top: 32px;
            right: -9px;
            z-index: 1;
            width: 18px;
            height: 18px;
            border: 4px solid #f8faf7;
            border-radius: 50%;
            background: var(--theme-color);
            box-shadow: 0 0 0 2px var(--theme-color);
        }

        .achievement-row:nth-child(even) .achievement-dot {
            right: auto;
            left: -9px;
        }

        .achievement-card {
            width: 100%;
            padding: 28px;
            background: #fff;
            border: 1px solid #e5ebe6;
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(31, 55, 43, .06);
        }

        .achievement-photo {
            width: 100%;
            height: 170px;
            margin: -28px -28px 24px;
            width: calc(100% + 56px);
            overflow: hidden;
        }

        .achievement-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .achievement-year {
            display: inline-block;
            margin-bottom: 14px;
            padding: 5px 12px;
            border-radius: 20px;
            background: #e4f2e8;
            color: var(--theme-color);
            font-size: 13px;
            font-weight: 700;
        }

        .achievement-card h4 {
            margin-bottom: 10px;
        }

        .achievement-icon {
            float: right;
            width: 42px;
            height: 42px;
        }

        @media (max-width: 767.98px) {
            .achievement-timeline::before {
                left: 10px;
            }

            .achievement-row,
            .achievement-row:nth-child(even) {
                width: 100%;
                margin-left: 0;
                padding: 0 0 28px 38px;
            }

            .achievement-dot,
            .achievement-row:nth-child(even) .achievement-dot {
                top: 32px;
                right: auto;
                left: 1px;
            }

            .impact-card,
            .achievement-card {
                padding: 22px;
            }
        }
    </style>

    <main class="main achievement-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Our Achievements</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Achievements</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <div class="achievement-intro">
                    <span class="site-title-tagline"><i class="far fa-award"></i> Our Journey</span>
                    <h1 class="site-title">Progress powered by <span>people</span></h1>
                    <p>Every milestone represents the trust of our supporters, the dedication of our volunteers and the strength of the communities we serve.</p>
                </div>

                <div class="row g-4 mb-100">
                    <div class="col-md-6 col-lg-3"><div class="impact-card"><div class="icon"><img src="assets/img/icon/happy.svg" alt=""></div><h3>6,500+</h3><h5>Lives Touched</h5><p class="mb-0">Families reached through community programs.</p></div></div>
                    <div class="col-md-6 col-lg-3"><div class="impact-card"><div class="icon"><img src="assets/img/icon/team.svg" alt=""></div><h3>48+</h3><h5>Active Volunteers</h5><p class="mb-0">People giving their time and skills.</p></div></div>
                    <div class="col-md-6 col-lg-3"><div class="impact-card"><div class="icon"><img src="assets/img/icon/country.svg" alt=""></div><h3>25+</h3><h5>Communities</h5><p class="mb-0">Local areas supported with care.</p></div></div>
                    <div class="col-md-6 col-lg-3"><div class="impact-card"><div class="icon"><img src="assets/img/icon/award.svg" alt=""></div><h3>12+</h3><h5>Years Of Service</h5><p class="mb-0">A continuing journey of public service.</p></div></div>
                </div>

                <div class="row">
                    <div class="col-lg-7 mx-auto">
                        <div class="site-heading text-center mb-5">
                            <span class="site-title-tagline"><i class="far fa-chart-line"></i> Milestones</span>
                            <h2 class="site-title">What we have achieved <span>together</span></h2>
                        </div>
                    </div>
                </div>

                <div class="achievement-timeline">
                    <div class="achievement-row">
                        <span class="achievement-dot"></span>
                        <article class="achievement-card">
                            <div class="achievement-photo"><img src="assets/img/gallery/01.jpg" alt="Umeed Foundation beginning in 2014"></div>
                            <img class="achievement-icon" src="assets/img/icon/award.svg" alt="">
                            <span class="achievement-year">2014</span>
                            <h4>Umeed Foundation Begins</h4>
                            <p class="mb-0">Started with a simple commitment to support families and strengthen local communities.</p>
                        </article>
                    </div>
                    <div class="achievement-row">
                        <span class="achievement-dot"></span>
                        <article class="achievement-card">
                            <div class="achievement-photo"><img src="assets/img/gallery/02.jpg" alt="Health and nutrition outreach in 2017"></div>
                            <img class="achievement-icon" src="assets/img/icon/quality-service.svg" alt="">
                            <span class="achievement-year">2017</span>
                            <h4>Health & Nutrition Outreach</h4>
                            <p class="mb-0">Expanded health checkups, nutrition distribution and awareness activities for families in need.</p>
                        </article>
                    </div>
                    <div class="achievement-row">
                        <span class="achievement-dot"></span>
                        <article class="achievement-card">
                            <div class="achievement-photo"><img src="assets/img/gallery/03.jpg" alt="Education support program in 2020"></div>
                            <img class="achievement-icon" src="assets/img/icon/student-visa.svg" alt="">
                            <span class="achievement-year">2020</span>
                            <h4>Education Support Program</h4>
                            <p class="mb-0">Introduced learning support and educational resources to help children continue their studies.</p>
                        </article>
                    </div>
                    <div class="achievement-row">
                        <span class="achievement-dot"></span>
                        <article class="achievement-card">
                            <div class="achievement-photo"><img src="assets/img/gallery/04.jpg" alt="Volunteer network growth in 2023"></div>
                            <img class="achievement-icon" src="assets/img/icon/team.svg" alt="">
                            <span class="achievement-year">2023</span>
                            <h4>Volunteer Network Grows</h4>
                            <p class="mb-0">Built a growing network of volunteers, partners and community leaders working for lasting change.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
