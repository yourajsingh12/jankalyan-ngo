<!DOCTYPE html>
<html lang="en">
<?php
require_once __DIR__ . '/includes/functions.php';

$projectId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
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

if ($projectId !== null && $projectId !== false) {
    $project = row(
        "SELECT *
           FROM projects
          WHERE id = ?
            AND LOWER(status) IN ('active', 'completed')",
        [$projectId]
    );

    if ($project === null) {
        header('Location: projects.php');
        exit;
    }

    $category = trim((string) ($project['category'] ?? ''));
    $icon = $projectIcons[strtolower($category)] ?? 'assets/img/icon/team.svg';
    $image = !empty($project['image'])
        ? img_url((string) $project['image'], $projectFallbackImages[0])
        : base_url($projectFallbackImages[0]);
    $progress = max(0, min(100, (int) $project['progress']));
    $projectName = trim((string) ($project['name'] ?? 'Project'));
    $projectDescription = (string) ($project['description'] ?? '');
    $projectFullDetails = trim((string) ($project['full_details'] ?? $projectDescription));
    $projectLocation = trim((string) ($project['location'] ?? ''));
    $goalAmount = isset($project['goal_amount']) && $project['goal_amount'] !== null && $project['goal_amount'] !== ''
        ? (float) $project['goal_amount']
        : null;
    $raisedAmount = isset($project['raised_amount']) && $project['raised_amount'] !== null && $project['raised_amount'] !== ''
        ? (float) $project['raised_amount']
        : null;
    $displayGoal = $goalAmount !== null ? '₹' . number_format($goalAmount, 0) : 'N/A';
    $displayRaised = $raisedAmount !== null ? '₹' . number_format($raisedAmount, 0) : 'N/A';
    $beneficiaries = isset($project['beneficiaries']) && $project['beneficiaries'] !== null && $project['beneficiaries'] !== ''
        ? (int) $project['beneficiaries']
        : null;
    $pageTitle = $projectName;
    include_once __DIR__ . '/head.php';
    ?>
    <body>
        <?php include_once __DIR__ . '/header.php'; ?>
        <main class="main projects-page project-detail-page">
            <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
                <div class="container">
                    <h2 class="breadcrumb-title"><?= e($projectName) ?></h2>
                    <ul class="breadcrumb-menu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li class="active"><?= e($projectName) ?></li>
                    </ul>
                </div>
            </div>

            <section class="py-100">
                <div class="container">
                    <div class="project-detail-card">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-6">
                                <img src="<?= e($image) ?>" alt="<?= e($projectName) ?>" class="project-detail-image">
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <span class="project-label"><?= e($category !== '' ? $category : 'Community') ?></span>
                                    <?php if ((int) ($project['is_featured'] ?? 0) === 1): ?>
                                        <span class="project-label">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="mb-3"><?= e($projectName) ?></h3>
                                <p class="project-detail-summary"><?= e($projectDescription !== '' ? $projectDescription : 'Project details coming soon.') ?></p>
                                <div class="project-progress"><span style="width: <?= $progress ?>%;"></span></div>
                                <div class="project-meta"><span>Project progress</span><strong><?= $progress ?>%<?= $project['status'] === 'Completed' ? ' · Completed' : '' ?></strong></div>
                                <div class="project-detail-actions">
                                    <a href="projects.php" class="theme-btn">Back to projects</a>
                                </div>
                            </div>
                        </div>

                        <div class="project-detail-metrics">
                            <div class="project-detail-metric">
                                <strong>Location</strong>
                                <span><?= e($projectLocation !== '' ? $projectLocation : 'Community outreach') ?></span>
                            </div>
                            <div class="project-detail-metric">
                                <strong>Beneficiaries</strong>
                                <span><?= e($beneficiaries !== null ? number_format($beneficiaries) : 'N/A') ?></span>
                            </div>
                            <div class="project-detail-metric">
                                <strong>Goal</strong>
                                <span><?= e($displayGoal) ?></span>
                            </div>
                            <div class="project-detail-metric">
                                <strong>Raised</strong>
                                <span><?= e($displayRaised) ?></span>
                            </div>
                            <div class="project-detail-metric">
                                <strong>Status</strong>
                                <span><?= e((string) ($project['status'] ?? 'Active')) ?></span>
                            </div>
                        </div>

                        <div class="project-detail-copy">
                            <h4>Project Overview</h4>
                            <?php if ($projectFullDetails !== ''): ?>
                                <p><?= nl2br(e($projectFullDetails)) ?></p>
                            <?php else: ?>
                                <p>More information about this project will be shared soon.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once __DIR__ . '/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$projects = rows(
    "SELECT id, name, description, category, progress, status, image, location, beneficiaries, goal_amount, raised_amount, is_featured
       FROM projects
      WHERE LOWER(status) IN ('active', 'completed')
      ORDER BY is_featured DESC, sort_order ASC, id ASC"
);
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
                        $projectName = trim((string) ($project['name'] ?? 'Project'));
                        $projectDescription = (string) ($project['description'] ?? '');
                        $projectLocation = trim((string) ($project['location'] ?? ''));
                        $goalAmount = isset($project['goal_amount']) && $project['goal_amount'] !== null && $project['goal_amount'] !== ''
                            ? (float) $project['goal_amount']
                            : null;
                        $raisedAmount = isset($project['raised_amount']) && $project['raised_amount'] !== null && $project['raised_amount'] !== ''
                            ? (float) $project['raised_amount']
                            : null;
                        $displayGoal = $goalAmount !== null ? '₹' . number_format($goalAmount, 0) : null;
                        $displayRaised = $raisedAmount !== null ? '₹' . number_format($raisedAmount, 0) : null;
                    ?>
                        <div class="col-md-6 col-lg-3">
                            <article class="project-card">
                                <div class="project-card-image"><img src="<?= e($image) ?>" alt="<?= e($projectName) ?>"></div>
                                <div class="project-card-body">
                                    <img class="mb-3" src="<?= e(base_url($icon)) ?>" width="42" height="42" alt="">
                                    <span class="project-label"><?= e($category !== '' ? $category : 'Community') ?></span>
                                    <?php if ((int) $project['is_featured'] === 1): ?>
                                        <span class="project-label">Featured</span>
                                    <?php endif; ?>
                                    <h4><?= e($projectName) ?></h4>
                                    <p><?= e(excerpt($projectDescription, 180)) ?></p>
                                    <?php if (!empty($projectLocation)): ?>
                                        <p class="mb-2"><i class="fas fa-map-marker-alt"></i> <?= e($projectLocation) ?></p>
                                    <?php endif; ?>
                                    <div class="project-progress"><span style="width: <?= $progress ?>%;"></span></div>
                                    <div class="project-meta"><span>Project progress</span><strong><?= $progress ?>%<?= $project['status'] === 'Completed' ? ' · Completed' : '' ?></strong></div>
                                    <a href="projects.php?id=<?= (int) $project['id'] ?>" class="theme-btn mt-3">View details</a>
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
