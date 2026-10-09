<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
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
                <div class="projects-intro">
                    <span class="site-title-tagline"><i class="far fa-hands-helping"></i> Our Work In Action</span>
                    <h1 class="site-title">Projects that turn <span>care into change</span></h1>
                    <p>From classrooms and health camps to skills training and environmental action, our projects are designed with communities and built for lasting impact.</p>
                </div>

                <div class="project-feature mb-100">
                    <div class="project-feature-content">
                        <span class="site-title-tagline">Featured Initiative</span>
                        <h2>Building stronger communities, one program at a time</h2>
                        <p>We bring local people, volunteers and partners together to create practical solutions for education, healthcare, livelihoods and a healthier environment.</p>
                        <a href="donate.php" class="theme-btn">Support This Work <i class="fas fa-circle-arrow-right"></i></a>
                    </div>
                </div>

                <div class="row g-4">
                    <?php
                    $projects = [
                        ['image' => 'assets/img/coaching/01.jpg', 'icon' => 'assets/img/icon/student-visa.svg', 'label' => 'Education', 'title' => 'Learning For Every Child', 'text' => 'Providing learning materials, mentoring and safe spaces that help children stay connected to education.', 'progress' => 78, 'status' => '78% of current goal'],
                        ['image' => 'assets/img/coaching/02.jpg', 'icon' => 'assets/img/icon/quality-service.svg', 'label' => 'Healthcare', 'title' => 'Healthy Family Outreach', 'text' => 'Health checkups, nutrition support and awareness sessions for families who need care close to home.', 'progress' => 64, 'status' => '64% of current goal'],
                        ['image' => 'assets/img/coaching/03.jpg', 'icon' => 'assets/img/icon/team.svg', 'label' => 'Empowerment', 'title' => 'Women & Youth Skills', 'text' => 'Building confidence and earning opportunities through practical skills and community leadership.', 'progress' => 72, 'status' => '72% of current goal'],
                        ['image' => 'assets/img/coaching/04.jpg', 'icon' => 'assets/img/icon/country.svg', 'label' => 'Environment', 'title' => 'Greener Neighbourhoods', 'text' => 'Supporting local action for cleaner surroundings, environmental awareness and sustainable living.', 'progress' => 55, 'status' => '55% of current goal'],
                    ];

                    foreach ($projects as $project):
                    ?>
                        <div class="col-md-6 col-lg-3">
                            <article class="project-card">
                                <div class="project-card-image"><img src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>"></div>
                                <div class="project-card-body">
                                    <img class="mb-3" src="<?php echo htmlspecialchars($project['icon'], ENT_QUOTES, 'UTF-8'); ?>" width="42" height="42" alt="">
                                    <span class="project-label"><?php echo htmlspecialchars($project['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <h4><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p><?php echo htmlspecialchars($project['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="project-progress"><span style="width: <?php echo (int) $project['progress']; ?>%;"></span></div>
                                    <div class="project-meta"><span>Project progress</span><strong><?php echo htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="project-impact">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="site-title-tagline"><i class="far fa-chart-line"></i> Why These Projects Matter</span>
                        <h2>Real work. Local leadership. Lasting outcomes.</h2>
                        <p>Our projects are shaped by the people they serve. We listen to local needs, work with community leaders and measure progress honestly.</p>
                    </div>
                    <div class="col-lg-5 ms-auto">
                        <div class="impact-point"><i class="fas fa-check-circle"></i><p>Communities help decide the priorities.</p></div>
                        <div class="impact-point"><i class="fas fa-check-circle"></i><p>Volunteers turn support into direct action.</p></div>
                        <div class="impact-point"><i class="fas fa-check-circle"></i><p>Every contribution helps a program move forward.</p></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
