<?php
require_once __DIR__ . '/includes/functions.php';

$transparencyDocuments = rows(
    'SELECT title, category, description, report_year, file_path
       FROM transparency_documents
      WHERE status = 1
      ORDER BY sort_order ASC, id ASC'
);
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <style>
        .transparency-page { background: #f8faf7; }
        .transparency-intro { max-width: 760px; margin: 0 auto 48px; text-align: center; }
        .transparency-intro p { color: #667085; font-size: 17px; line-height: 1.8; }
        .transparency-card {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 32px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e5ebe6;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(31, 55, 43, .07);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .transparency-card:hover { transform: translateY(-5px); box-shadow: 0 18px 38px rgba(31, 55, 43, .12); }
        .transparency-card::before { position: absolute; inset: 0 0 auto; height: 4px; background: var(--theme-color); content: ''; }
        .transparency-card__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin-bottom: 24px;
            border-radius: 18px;
            background: #edf5ef;
            color: var(--theme-color);
            font-size: 27px;
        }
        .transparency-card__meta { display: flex; flex-wrap: wrap; align-items: center; gap: 9px; margin-bottom: 14px; }
        .transparency-card__badge, .transparency-card__year {
            display: inline-flex;
            padding: 6px 11px;
            border-radius: 30px;
            background: #edf5ef;
            color: var(--theme-color);
            font-size: 12px;
            font-weight: 700;
        }
        .transparency-card__year { background: #f3f4f6; color: #475467; }
        .transparency-card h3 { margin-bottom: 12px; color: #183b2a; font-size: 23px; }
        .transparency-card p { flex: 1; margin-bottom: 26px; color: #667085; line-height: 1.75; }
        .transparency-card .theme-btn { align-self: flex-start; }
        .transparency-empty { padding: 44px 24px; border: 1px dashed #cbd5ce; border-radius: 12px; color: #667085; text-align: center; }
        @media (max-width: 575.98px) { .transparency-card { padding: 25px; } }
    </style>

    <main class="main transparency-page">
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Transparency</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Transparency</li>
                </ul>
            </div>
        </div>

        <section class="py-100">
            <div class="container">
                <div class="transparency-intro">
                    <span class="site-title-tagline"><i class="far fa-file-pdf"></i> Open &amp; Accountable</span>
                    <h1 class="site-title">Our Transparency</h1>
                    <p>We believe trust grows when our work and financial reporting are open. Browse our annual, audit and financial reports below.</p>
                </div>

                <?php if ($transparencyDocuments === []): ?>
                    <div class="transparency-empty">Reports will be published here soon.</div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($transparencyDocuments as $document): ?>
                            <div class="col-md-6 col-lg-4">
                                <article class="transparency-card">
                                    <div class="transparency-card__icon" aria-hidden="true"><i class="fas fa-file-pdf"></i></div>
                                    <div class="transparency-card__meta">
                                        <span class="transparency-card__badge"><?= e($document['category']) ?></span>
                                        <?php if (!empty($document['report_year'])): ?>
                                            <span class="transparency-card__year"><?= e($document['report_year']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <h3><?= e($document['title']) ?></h3>
                                    <p><?= e($document['description'] ?: 'Open this report to learn more about our work and accountability.') ?></p>
                                    <a class="theme-btn" href="<?= e($document['file_path']) ?>" target="_blank" rel="noopener">
                                        View Report <i class="fas fa-arrow-up-right-from-square"></i>
                                    </a>
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
