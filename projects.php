<!DOCTYPE html>
<html lang="en">
<?php
require_once __DIR__ . '/includes/functions.php';
$projects = rows(
    "SELECT name, description, category, progress, status, image, location, beneficiaries, is_featured
       FROM projects
      WHERE LOWER(status) IN ('active', 'completed')
      ORDER BY is_featured DESC, sort_order ASC, id ASC"
);
$projectFallbackImages = [
    'assets/img/coaching/01.jpg',
    'assets/img/coaching/02.jpg',
    'assets/img/coaching/03.jpg',
    'assets/img/coaching/04.jpg',
];
$projectIcons = [
    'education' => 'assets/img/icon/student-visa.svg',
    'healthcare' => 'assets/img/icon/quality-service.svg',
    'empowerment' => 'assets/img/icon/team.svg',
    'environment' => 'assets/img/icon/country.svg',
];
include_once __DIR__ . '/head.php';
?>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <main class="main projects-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Our Projects</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Projects</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                

                

                <div class="row g-4">
                    <?php foreach ($projects as $index => $project):
                        $category = trim((string) ($project['category'] ?? ''));
                        $icon = $projectIcons[strtolower($category)] ?? 'assets/img/icon/team.svg';
                        $image = !empty($project['image'])
                            ? img_url((string) $project['image'], $projectFallbackImages[$index % count($projectFallbackImages)])
                            : base_url($projectFallbackImages[$index % count($projectFallbackImages)]);
                        $progress = max(0, min(100, (int) $project['progress']));
                    ?>
                        <div class="col-md-6 col-lg-3">
                            <article class="project-card">
                                <div class="project-card-image"><img src="<?= e($image) ?>" alt="<?= e($project['name']) ?>"></div>
                                <div class="project-card-body">
                                    <img class="mb-3" src="<?= e(base_url($icon)) ?>" width="42" height="42" alt="">
                                    <span class="project-label"><?= e($category !== '' ? $category : 'Community') ?></span>
                                    <?php if ((int) $project['is_featured'] === 1): ?>
                                        <span class="project-label">Featured</span>
                                    <?php endif; ?>
                                    <h4><?= e($project['name']) ?></h4>
                                    <p><?= e(excerpt((string) ($project['description'] ?? ''), 180)) ?></p>
                                    <?php if (!empty($project['location'])): ?>
                                        <p class="mb-2"><i class="fas fa-map-marker-alt"></i> <?= e($project['location']) ?></p>
                                    <?php endif; ?>
                                    <div class="project-progress"><span style="width: <?= $progress ?>%;"></span></div>
                                    <div class="project-meta"><span>Project progress</span><strong><?= $progress ?>%<?= $project['status'] === 'Completed' ? ' · Completed' : '' ?></strong></div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($projects === []): ?>
                        <div class="col-12">
                            <p class="text-center">Project details will be added soon.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
