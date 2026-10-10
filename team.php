<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Team';
$teamMembers = rows(
    "SELECT name, position, image, facebook, twitter, linkedin, instagram
       FROM team_members
      WHERE status = 'Active'
      ORDER BY sort_order ASC, id ASC"
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
                <h2 class="breadcrumb-title">Our Team</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Our Team</li>
                </ul>
            </div>
        </div>

        <div class="team-area py-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="site-heading text-center wow fadeInDown" data-wow-delay=".25s">
                            <span class="site-title-tagline"><i class="far fa-passport"></i> Our Team</span>
                            <h2 class="site-title">Meet With Our <span>Awesome Team</span> Members</h2>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <?php if ($teamMembers === []): ?>
                        <div class="col-12 text-center">
                            <p>Our team profiles will be available soon.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($teamMembers as $index => $member): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="team-item wow fadeInUp" data-wow-delay="<?= e(number_format(0.25 + ($index % 4) * 0.25, 2)) ?>s">
                                <div class="team-img">
                                    <img src="<?= e(img_url($member['image'])) ?>" alt="<?= e($member['name']) ?>">
                                    <?php
                                    $networks = [
                                        'facebook' => ['fab fa-facebook-f', $member['facebook']],
                                        'twitter' => ['fab fa-x-twitter', $member['twitter']],
                                        'linkedin' => ['fab fa-linkedin-in', $member['linkedin']],
                                        'instagram' => ['fab fa-instagram', $member['instagram']],
                                    ];
                                    $hasSocialLinks = false;
                                    foreach ($networks as [, $url]) {
                                        if (is_string($url) && preg_match('#^https?://#i', $url)) {
                                            $hasSocialLinks = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($hasSocialLinks): ?>
                                        <div class="team-social-wrap">
                                            <div class="team-social-btn">
                                                <button type="button" aria-label="Show social links"><i class="far fa-share-alt"></i></button>
                                            </div>
                                            <div class="team-social">
                                                <?php foreach ($networks as [$icon, $url]): ?>
                                                    <?php if (is_string($url) && preg_match('#^https?://#i', $url)): ?>
                                                        <a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($member['name']) ?> social profile"><i class="<?= e($icon) ?>"></i></a>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="team-content">
                                    <h4><?= e($member['name']) ?></h4>
                                    <span><?= e($member['position']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
