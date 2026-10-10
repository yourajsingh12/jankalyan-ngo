<?php
require_once __DIR__ . '/includes/functions.php';

$missionDefaults = [
    'intro' => [
        'tagline' => 'Our Purpose',
        'heading' => 'Creating a future built on equality and opportunity',
        'body' => 'At JAN KALYAN SANGH FOUNDATION, we believe lasting change begins when communities have the resources, knowledge and confidence to shape their own future.',
        'extra' => '',
    ],
    'mission' => [
        'tagline' => 'Our Mission',
        'heading' => 'Driving social change through sustainable development',
        'body' => 'We are dedicated to improving education, healthcare, environmental conservation and women\'s empowerment by providing communities with the resources they need to build brighter futures.',
        'extra' => 'Our ultimate goal is to create self-reliant, resilient communities that thrive in harmony with their environment.',
        'image' => 'assets/img/icon/accurate-guidance.svg',
    ],
    'vision' => [
        'tagline' => 'Our Vision',
        'heading' => 'A world where every person can reach their potential',
        'body' => 'We envision a future where equality, education and environmental sustainability form the bedrock of thriving communities.',
        'extra' => 'We strive for a society where collaboration drives innovation and communities work together to build lasting solutions for generations to come.',
        'image' => 'assets/img/icon/country.svg',
    ],
    'focus' => [
        'tagline' => 'Our Focus',
        'heading' => 'Turning purpose into action',
        'body' => 'Our work is focused on practical support that helps people build secure, healthy and independent lives.',
        'extra' => '',
    ],
];

$storedSections = rows(
    'SELECT section_key, tagline, heading, body, extra, image, status
       FROM page_sections
      WHERE page = ?',
    ['mission']
);
$missionSections = [];
foreach ($storedSections as $section) {
    $missionSections[$section['section_key']] = $section;
}
$hasMissionConfiguration = $missionSections !== [];

foreach ($missionDefaults as $key => $default) {
    if (!isset($missionSections[$key])) {
        $missionSections[$key] = $default + ['status' => 1];
    }
}

$focusAreas = rows(
    'SELECT title, description, icon
       FROM mission_focus_areas
      WHERE status = 1
      ORDER BY sort_order ASC, id ASC'
);
if (!$hasMissionConfiguration && $focusAreas === []) {
    $focusAreas = [
        ['title' => 'Education', 'description' => 'We create access to learning, guidance and opportunities for children and young people.', 'icon' => 'assets/img/icon/student-visa.svg'],
        ['title' => 'Healthcare', 'description' => 'We support healthier families through awareness, checkups and nutrition programs.', 'icon' => 'assets/img/icon/quality-service.svg'],
        ['title' => 'Environment', 'description' => 'We encourage responsible community action that protects our shared environment.', 'icon' => 'assets/img/icon/country.svg'],
        ['title' => 'Women\'s Empowerment', 'description' => 'We help women access skills, confidence and opportunities to lead with independence.', 'icon' => 'assets/img/icon/team.svg'],
    ];
}

$intro = $missionSections['intro'];
$mission = $missionSections['mission'];
$vision = $missionSections['vision'];
$focus = $missionSections['focus'];
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <main class="main mission-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Our Mission &amp; Vision</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Mission</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <?php if ((int) $intro['status'] === 1): ?>
                    <div class="mission-intro">
                        <span class="site-title-tagline"><i class="far fa-compass"></i> <?= e($intro['tagline']) ?></span>
                        <h1 class="site-title"><?= e($intro['heading']) ?></h1>
                        <p><?= e($intro['body']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ((int) $mission['status'] === 1 || (int) $vision['status'] === 1): ?>
                    <div class="row g-4 mb-100">
                        <?php if ((int) $mission['status'] === 1): ?>
                            <div class="col-lg-6">
                                <article class="mission-panel dark">
                                    <div class="mission-mark"><img src="<?= e(img_url((string) ($mission['image'] ?? ''), 'assets/img/icon/accurate-guidance.svg')) ?>" alt="Mission"></div>
                                    <span class="site-title-tagline"><?= e($mission['tagline']) ?></span>
                                    <h2><?= e($mission['heading']) ?></h2>
                                    <p><?= e($mission['body']) ?></p>
                                    <?php if (trim((string) ($mission['extra'] ?? '')) !== ''): ?><p class="mb-0"><?= e($mission['extra']) ?></p><?php endif; ?>
                                </article>
                            </div>
                        <?php endif; ?>
                        <?php if ((int) $vision['status'] === 1): ?>
                            <div class="col-lg-6">
                                <article class="mission-panel">
                                    <div class="mission-mark"><img src="<?= e(img_url((string) ($vision['image'] ?? ''), 'assets/img/icon/country.svg')) ?>" alt="Vision"></div>
                                    <span class="site-title-tagline"><?= e($vision['tagline']) ?></span>
                                    <h2><?= e($vision['heading']) ?></h2>
                                    <p><?= e($vision['body']) ?></p>
                                    <?php if (trim((string) ($vision['extra'] ?? '')) !== ''): ?><p class="mb-0"><?= e($vision['extra']) ?></p><?php endif; ?>
                                </article>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ((int) $focus['status'] === 1): ?>
                    <div class="row">
                        <div class="col-lg-7 mx-auto">
                            <div class="site-heading text-center mb-5">
                                <span class="site-title-tagline"><i class="far fa-hand-holding-heart"></i> <?= e($focus['tagline']) ?></span>
                                <h2 class="site-title"><?= e($focus['heading']) ?></h2>
                                <p><?= e($focus['body']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($focusAreas as $area): ?>
                            <div class="col-md-6 col-lg-3">
                                <article class="focus-card">
                                    <div class="icon"><img src="<?= e(img_url((string) ($area['icon'] ?? ''), 'assets/img/icon/team.svg')) ?>" alt="<?= e($area['title']) ?>"></div>
                                    <h4><?= e($area['title']) ?></h4>
                                    <p class="mb-0"><?= e($area['description']) ?></p>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
