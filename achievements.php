<?php
require_once __DIR__ . '/includes/functions.php';

$achievementSections = sections('achievements');
$achievementIntro = $achievementSections['intro'] ?? [];

$achievementStats = [
    ['key' => 'lives', 'value' => '6,500+', 'label' => 'Lives Touched', 'description' => 'Families reached through community programs.', 'icon' => 'assets/img/icon/happy.svg'],
    ['key' => 'volunteers', 'value' => '48+', 'label' => 'Active Volunteers', 'description' => 'People giving their time and skills.', 'icon' => 'assets/img/icon/team.svg'],
    ['key' => 'communities', 'value' => '25+', 'label' => 'Communities', 'description' => 'Local areas supported with care.', 'icon' => 'assets/img/icon/country.svg'],
    ['key' => 'service', 'value' => '12+', 'label' => 'Years Of Service', 'description' => 'A continuing journey of public service.', 'icon' => 'assets/img/icon/award.svg'],
];

foreach ($achievementStats as &$stat) {
    $savedStat = $achievementSections['stat_' . $stat['key']] ?? [];
    $stat['value'] = $savedStat['heading'] ?? $stat['value'];
    $stat['label'] = $savedStat['subheading'] ?? $stat['label'];
    $stat['description'] = $savedStat['body'] ?? $stat['description'];
}
unset($stat);

$defaultAchievements = [
    ['year' => '2014', 'title' => 'Umeed Foundation Begins', 'description' => 'Started with a simple commitment to support families and strengthen local communities.', 'image' => 'assets/img/gallery/01.jpg', 'icon' => 'assets/img/icon/award.svg'],
    ['year' => '2017', 'title' => 'Health & Nutrition Outreach', 'description' => 'Expanded health checkups, nutrition distribution and awareness activities for families in need.', 'image' => 'assets/img/gallery/02.jpg', 'icon' => 'assets/img/icon/quality-service.svg'],
    ['year' => '2020', 'title' => 'Education Support Program', 'description' => 'Introduced learning support and educational resources to help children continue their studies.', 'image' => 'assets/img/gallery/03.jpg', 'icon' => 'assets/img/icon/student-visa.svg'],
    ['year' => '2023', 'title' => 'Volunteer Network Grows', 'description' => 'Built a growing network of volunteers, partners and community leaders working for lasting change.', 'image' => 'assets/img/gallery/04.jpg', 'icon' => 'assets/img/icon/team.svg'],
];

try {
    $achievementItems = rows(
        'SELECT title, description, year, image, icon
         FROM achievements
         WHERE status = 1
         ORDER BY sort_order ASC, year DESC, id DESC'
    );
} catch (PDOException $e) {
    $achievementItems = [];
}

if ($achievementItems === [] || $achievementItems === false) {
    $achievementItems = $defaultAchievements;
}
?>
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
                    <span class="site-title-tagline"><i class="far fa-award"></i> <?= e($achievementIntro['tagline'] ?? 'Our Journey') ?></span>
                    <h1 class="site-title"><?= e($achievementIntro['heading'] ?? 'Progress powered by people') ?></h1>
                    <p><?= e($achievementIntro['body'] ?? 'Every milestone represents the trust of our supporters, the dedication of our volunteers and the strength of the communities we serve.') ?></p>
                </div>

                <div class="row g-4 mb-100">
                    <?php foreach ($achievementStats as $stat): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="impact-card">
                                <div class="icon"><img src="<?= e(img_url($stat['icon'])) ?>" alt="<?= e($stat['label']) ?>"></div>
                                <h3><?= e($stat['value']) ?></h3>
                                <h5><?= e($stat['label']) ?></h5>
                                <p class="mb-0"><?= e($stat['description']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                    <?php foreach ($achievementItems as $achievement): ?>
                        <div class="achievement-row">
                            <span class="achievement-dot"></span>
                            <article class="achievement-card">
                                <div class="achievement-photo">
                                    <img src="<?= e(img_url($achievement['image'] ?? '')) ?>" alt="<?= e($achievement['title'] ?? 'Achievement') ?>">
                                </div>
                                <img class="achievement-icon" src="<?= e(img_url($achievement['icon'] ?? '')) ?>" alt="<?= e($achievement['title'] ?? 'Achievement') ?>">
                                <span class="achievement-year"><?= e($achievement['year'] ?? '') ?></span>
                                <h4><?= e($achievement['title'] ?? '') ?></h4>
                                <p class="mb-0"><?= e($achievement['description'] ?? '') ?></p>
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
