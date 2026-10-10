<?php
require_once __DIR__ . '/includes/functions.php';
$homeBanners = rows(
	"SELECT title, image_path
	 FROM banners
	 WHERE page_name = 'home' AND status = 'Active'
	 ORDER BY sort_order ASC, id ASC"
);
$homeFeatures = rows(
	'SELECT id, title, description, icon, image
	   FROM home_features
	  WHERE status = 1
	  ORDER BY sort_order ASC, id ASC'
);
$focusAreas = rows(
	'SELECT id, title, description, icon, image, sort_order
	   FROM focus_areas
	  WHERE status = 1
	  ORDER BY sort_order ASC, id ASC'
);
$homeSections = sections('home');
$homeCta = $homeSections['cta'] ?? [];
$homeFocus = $homeSections['focus'] ?? [];
$homeInvolvement = $homeSections['involve'] ?? [];
$ctaButton = explode('|', (string) ($homeCta['extra'] ?? 'Donate Now|donate.php'), 2);
$ctaButtonLabel = trim((string) ($ctaButton[0] ?? '')) ?: 'Donate Now';
$ctaButtonUrl = trim((string) ($ctaButton[1] ?? '')) ?: 'donate.php';
$homeSteps = rows(
	'SELECT title, description, icon, link_url
	   FROM process_steps
	  WHERE status = 1
	  ORDER BY sort_order ASC, id ASC'
);
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
<body>
	<?php include_once __DIR__ . '/header.php'; ?>

	<main class="main">
		<?php if ($homeBanners !== []): ?>
		<section class="hero-section">
			<div class="hero-slider owl-carousel">
				<?php foreach ($homeBanners as $banner): ?>
					<div class="hero-single">
						<img class="hero-banner-image" src="<?= e(img_url($banner['image_path'])) ?>" alt="<?= e($banner['title']) ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<section class="feature-area feature-negative">
			<div class="container">
				<div class="feature-wrapper">
					<div class="row g-4">
						<?php foreach ($homeFeatures as $index => $feature): ?>
							<div class="col-md-6 col-lg-4">
								<div class="feature-item">
									<span class="count"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
									<div class="feature-icon"><img src="<?= e(img_url((string) ($feature['icon'] ?? ''), 'assets/img/icon/team.svg')) ?>" alt="<?= e($feature['title']) ?>"></div>
									<div class="feature-content">
										<h4><?= e($feature['title']) ?></h4>
										<p><?= e($feature['description']) ?></p>
										<a href="focus_area.php?id=<?= (int) $feature['id'] ?>" class="theme-btn">More Details <i class="fas fa-circle-arrow-right"></i></a>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<div class="visa-area bg-2 pt-80 pb-120" id="focus-areas">
			<div class="container pb-80">
				<div class="row">
					<div class="col-lg-8 mx-auto wow fadeInDown" data-wow-delay=".25s">
						<div class="site-heading text-center">
							<span class="site-title-tagline"><i class="far fa-hands-helping"></i> <?= e($homeFocus['tagline'] ?? 'Our Focus Areas') ?></span>
							<h2 class="site-title"><?= e(strip_tags((string) ($homeFocus['heading'] ?? 'Building a stronger and healthier society'))) ?></h2>
						</div>
					</div>
				</div>
				<div class="visa-wrap wow fadeInUp" data-wow-delay=".25s">
					<div class="visa-slider owl-carousel">
						<?php foreach ($focusAreas as $index => $focusArea):
							$focusDetailUrl = 'focus_area.php?focus_id=' . (int) $focusArea['id'];
							$focusImage = img_url((string) ($focusArea['image'] ?? ''), $index % 2 === 0 ? 'assets/img/visa/seva1.png' : 'assets/img/visa/seva2.png');
						?>
							<div class="visa-item">
								<a href="<?= e($focusDetailUrl) ?>" class="focus-area-card-link" aria-label="View <?= e($focusArea['title']) ?> details">
									<span class="count"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
									<div class="visa-content">
										<div class="visa-icon">
											<img src="<?= e(img_url((string) ($focusArea['icon'] ?? ''), 'assets/img/icon/team.svg')) ?>" alt="">
										</div>
										<div class="visa-info">
											<h4 class="visa-title"><?= e($focusArea['title']) ?></h4>
											<p class="visa-text"><?= e(excerpt((string) ($focusArea['description'] ?? ''), 150)) ?></p>
											<span class="theme-btn">View Details <i class="fas fa-circle-arrow-right"></i></span>
										</div>
									</div>
									<div class="visa-img">
										<img src="<?= e($focusImage) ?>" alt="<?= e($focusArea['title']) ?>">
									</div>
								</a>
							</div>
						<?php endforeach; ?>
						<?php if ($focusAreas === []): ?>
							<p class="text-center">Our focus area details will be added soon.</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="cta-area cta-negative">
			<div class="container">
				<div class="cta-wrap">
					<div class="row align-items-center">
						<div class="col-lg-6">
							<div class="cta-img">
								<img src="<?= e(img_url((string) ($homeCta['image'] ?? ''), 'assets/img/cta/flood.png')) ?>" alt="<?= e(strip_tags((string) ($homeCta['heading'] ?? 'Jan Kalyan Sangh Foundation community outreach'))) ?>">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="cta-content">
								<h1><?= e(strip_tags((string) ($homeCta['heading'] ?? 'We work for a healthier, kinder, and stronger tomorrow'))) ?></h1>
								<p><?= e($homeCta['body'] ?? 'Jan Kalyan Sangh Foundation is committed to empowering underserved communities through education, healthcare, social awareness, and community development.') ?></p>
								<a href="<?= e($ctaButtonUrl) ?>" class="theme-btn2"><?= e($ctaButtonLabel) ?><i class="fas fa-circle-arrow-right"></i></a>
								<div class="shape">
									<img src="assets/img/shape/02.png" alt="shape">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<section class="process-area pt-80 pb-100">
			<div class="container">
				<div class="row">
					<div class="col-lg-7 mx-auto">
						<div class="site-heading text-center">
							<span class="site-title-tagline"><i class="fas fa-hands-helping"></i> <?= e($homeInvolvement['tagline'] ?? 'Get Involved') ?></span>
							<h2 class="site-title"><?= e(strip_tags((string) ($homeInvolvement['heading'] ?? 'Small actions create lasting change'))) ?></h2>
						</div>
					</div>
				</div>
				<div class="row g-4">
					<?php foreach ($homeSteps as $index => $step): ?>
						<div class="col-md-6 col-lg-3">
							<div class="process-item <?= $index % 2 === 1 ? 'mt-lg-5' : '' ?>">
								<span class="count"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
								<div class="process-icon"><img src="<?= e(img_url((string) ($step['icon'] ?? ''), 'assets/img/icon/team.svg')) ?>" alt="<?= e($step['title']) ?>"></div>
								<div class="process-content">
									<h4><?= e($step['title']) ?></h4>
									<p><?= e($step['description']) ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	</main>

	<?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
