<?php
/**
 * Document head.
 *
 * Loaded by every public page, and the first thing on each of them, so this is
 * also where the helper layer is pulled in. A page may set $pageTitle and
 * $pageMeta before including this file; otherwise the site defaults from
 * site_settings are used.
 */
require_once __DIR__ . '/includes/functions.php';

$siteName = setting('site_name', 'Jan Kalyan Sangh Foundation');
$title    = isset($pageTitle) && $pageTitle !== ''
    ? $pageTitle . ' | ' . $siteName
    : $siteName;
$metaDesc = $pageMeta     ?? setting('meta_description');
$metaKeys = $pageKeywords ?? setting('meta_keywords');
?>
<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta name="keywords" content="<?= e($metaKeys) ?>">

    <!-- title -->
    <title><?= e($title) ?></title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="<?= e(img_url(setting('favicon'), 'assets/img/logo/study.png')) ?>">

    <!-- css -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500&family=Lora:wght@400&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all-fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/nice-select.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <?php if (basename($_SERVER['SCRIPT_NAME']) === 'donate.php'): ?>
    <style>
        .donation-page { background: #f7f8f3; }
        .donation-intro { max-width: 760px; margin: 0 auto 42px; text-align: center; }
        .donation-intro p { color: #667085; font-size: 17px; line-height: 1.8; }
        .donation-card { height: 100%; padding: 34px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 12px 35px rgba(25, 50, 35, .08); }
        .donation-card h3 { margin-bottom: 24px; color: #183b2a; }
        .bank-details { margin: 0; }
        .bank-row { display: flex; justify-content: space-between; gap: 24px; padding: 14px 0; border-bottom: 1px solid #edf0eb; }
        .bank-row:last-child { border-bottom: 0; }
        .bank-row dt { color: #667085; font-weight: 600; }
        .bank-row dd { margin: 0; color: #183b2a; font-weight: 700; text-align: right; overflow-wrap: anywhere; }
        .donation-note { height: 100%; padding: 34px; background: #183b2a; color: #fff; border-radius: 8px; }
        .donation-note h3, .donation-note h4 { color: #fff; }
        .donation-note p, .donation-note li { color: rgba(255, 255, 255, .82); line-height: 1.8; }
        .donation-note ul { padding-left: 20px; margin-bottom: 28px; }
        .donation-contact { padding-top: 22px; border-top: 1px solid rgba(255, 255, 255, .2); }
        .donation-contact a { color: #fff; overflow-wrap: anywhere; }
        @media (max-width: 575.98px) {
            .donation-card, .donation-note { padding: 24px 20px; }
            .bank-row { display: block; }
            .bank-row dt { margin-bottom: 5px; }
            .bank-row dd { text-align: left; }
        }
    </style>
    <?php endif; ?>

    <?php if (basename($_SERVER['SCRIPT_NAME']) === 'mission.php'): ?>
    <style>
        .mission-page { background: #f7faf7; }
        .mission-intro { max-width: 800px; margin: 0 auto 45px; text-align: center; }
        .mission-intro p, .mission-panel p, .focus-card p, .values-copy p { color: #667085; line-height: 1.85; }
        .mission-panel { height: 100%; padding: 42px; border-radius: 8px; background: #fff; border: 1px solid #e4ebe5; box-shadow: 0 12px 35px rgba(25, 50, 35, .08); }
        .mission-panel.dark { background: #183b2a; border-color: #183b2a; }
        .mission-panel.dark h2, .mission-panel.dark p { color: #fff; }
        .mission-panel h2 { margin-bottom: 20px; }
        .mission-mark { width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border-radius: 50%; background: #e4f2e8; }
        .mission-mark img { width: 38px; height: 38px; }
        .mission-panel.dark .mission-mark { background: rgba(255, 255, 255, .14); }
        .focus-card { height: 100%; padding: 28px; background: #fff; border-top: 3px solid var(--theme-color); box-shadow: 0 8px 24px rgba(25, 50, 35, .07); }
        .focus-card .icon { width: 54px; height: 54px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-radius: 50%; background: #e4f2e8; }
        .focus-card .icon img { width: 30px; height: 30px; }
        .focus-card h4 { margin-bottom: 10px; }
        .values-band { padding: 70px 0; background: #183b2a; }
        .values-copy h2, .values-copy p { color: #fff; }
        .values-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin: 0; padding: 0; list-style: none; }
        .values-list li { padding: 18px; color: #fff; border: 1px solid rgba(255, 255, 255, .2); border-radius: 6px; }
        .values-list i { margin-right: 8px; color: #a9d9b6; }
        @media (max-width: 575.98px) {
            .mission-panel { padding: 28px 22px; }
            .values-list { grid-template-columns: 1fr; }
        }
    </style>
    <?php endif; ?>

    <?php if (basename($_SERVER['SCRIPT_NAME']) === 'projects.php'): ?>
    <style>
        .projects-page { background: #f7faf7; }
        .projects-intro { max-width: 780px; margin: 0 auto 45px; text-align: center; }
        .projects-intro p, .project-card p, .project-feature p { color: #667085; line-height: 1.8; }
        .project-feature { overflow: hidden; position: relative; min-height: 330px; padding: 44px; border-radius: 8px; background: #183b2a url('assets/img/coaching/banner.jpg') center/cover; }
        .project-feature::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(24, 59, 42, .96), rgba(24, 59, 42, .55)); }
        .project-feature-content { position: relative; z-index: 1; max-width: 620px; }
        .project-feature h2, .project-feature p { color: #fff; }
        .project-feature p { color: rgba(255,255,255,.82); }
        .project-feature .theme-btn { margin-top: 12px; }
        .project-card { height: 100%; overflow: hidden; background: #fff; border: 1px solid #e3ebe5; box-shadow: 0 10px 28px rgba(25, 50, 35, .07); }
        .project-card-image { height: 220px; overflow: hidden; }
        .project-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
        .project-card:hover .project-card-image img { transform: scale(1.06); }
        .project-card-body { padding: 26px; }
        .project-label { display: inline-block; margin-bottom: 14px; color: var(--theme-color); font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .project-card h4 { margin-bottom: 10px; }
        .project-progress { height: 6px; margin-top: 20px; overflow: hidden; border-radius: 10px; background: #e7eee9; }
        .project-progress span { display: block; height: 100%; border-radius: inherit; background: var(--theme-color); }
        .project-meta { display: flex; justify-content: space-between; gap: 16px; margin-top: 9px; color: #667085; font-size: 13px; }
        .project-detail-card { background: #fff; border: 1px solid #e3ebe5; border-radius: 18px; padding: 32px; box-shadow: 0 14px 34px rgba(25, 50, 35, .08); }
        .project-detail-image { width: 100%; height: 100%; min-height: 330px; object-fit: cover; border-radius: 16px; }
        .project-detail-summary { color: #4b5563; line-height: 1.8; font-size: 1.02rem; }
        .project-detail-actions { margin-top: 24px; }
        .project-detail-metrics { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; margin: 28px 0; }
        .project-detail-metric { padding: 18px; border: 1px solid #e5ece7; border-radius: 12px; background: #f8fbf9; }
        .project-detail-metric strong { display: block; margin-bottom: 8px; color: #183b2a; font-size: 13px; }
        .project-detail-metric span { color: #667085; font-size: 14px; }
        .project-detail-copy { margin-top: 10px; }
        .project-detail-copy h4 { margin-bottom: 16px; }
        .project-detail-copy p { color: #4b5563; line-height: 1.9; }
        .project-impact { padding: 65px 0; background: #eaf4ed; }
        .project-impact h2 { margin-bottom: 14px; }
        .project-impact p { color: #667085; line-height: 1.8; }
        .impact-point { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 18px; }
        .impact-point i { flex: 0 0 auto; margin-top: 4px; color: var(--theme-color); }
        .impact-point p { margin: 0; }
        @media (max-width: 991.98px) {
            .project-detail-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 575.98px) {
            .project-feature { min-height: 390px; padding: 30px 22px; }
            .project-card-body { padding: 22px; }
            .project-card-image { height: 190px; }
            .project-detail-card { padding: 22px; }
            .project-detail-metrics { grid-template-columns: 1fr; }
        }
    </style>
    <?php endif; ?>

    <?php if (basename($_SERVER['SCRIPT_NAME']) === 'campaigns.php'): ?>
    <style>
        .campaigns-page { background: #f3f7fd; }
        .campaigns-intro { max-width: 760px; margin: 0 auto 38px; text-align: center; }
        .campaigns-intro p { color: #64748b; line-height: 1.8; }
        .campaign-card { height: 100%; overflow: hidden; display: flex; flex-direction: column; border: 1px solid #e3eaf2; border-radius: 20px; background: #fff; box-shadow: 0 12px 32px rgba(20, 48, 82, .08); }
        .campaign-card-media { display: block; height: 250px; position: relative; overflow: hidden; background: #dbe5f0; }
        .campaign-card-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .campaign-card:hover .campaign-card-media img { transform: scale(1.045); }
        .campaign-card-media::after { content: ''; position: absolute; inset: 40% 0 0; background: linear-gradient(transparent, rgba(9, 24, 40, .78)); pointer-events: none; }
        .campaign-category, .campaign-status { position: absolute; z-index: 1; top: 16px; border-radius: 999px; padding: 8px 14px; color: #fff; font-size: .76rem; font-weight: 700; }
        .campaign-category { left: 16px; background: #f59e0b; }
        .campaign-status { right: 16px; background: #059669; }
        .campaign-status.is-completed { background: #2784bd; }
        .campaign-status.is-upcoming { background: #7c3aed; }
        .campaign-card-content { flex: 1; padding: 22px 24px 18px; }
        .campaign-card-content h3 { margin: 0 0 9px; color: #183b5b; font-size: 1.2rem; }
        .campaign-card-content > p { min-height: 48px; margin-bottom: 14px; color: #718096; line-height: 1.55; }
        .campaign-read-more { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 18px; color: #087e78; font-size: .9rem; font-weight: 700; }
        .campaign-read-more:hover { color: #075e59; }
        .campaign-dates { display: flex; flex-wrap: wrap; gap: 10px 18px; margin-bottom: 18px; color: #64748b; font-size: .84rem; }
        .campaign-dates i { margin-right: 5px; color: #0f766e; }
        .campaign-amounts { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 10px; color: #173958; font-size: .9rem; font-weight: 700; }
        .campaign-raised { color: #087e78; }
        .campaign-progress { height: 9px; margin-bottom: 8px; overflow: hidden; border-radius: 99px; background: #e2e8f0; }
        .campaign-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #159b71, #0f766e); }
        .campaign-remaining { margin-bottom: 15px; color: #ef5146; font-size: .85rem; font-weight: 700; }
        .campaign-remaining.is-complete { color: #159b71; }
        .campaign-supporters { padding: 12px 15px; border-radius: 13px; background: #f1f5f9; color: #64748b; font-size: .88rem; }
        .campaign-card-actions { display: flex; align-items: center; gap: 10px; padding: 15px 20px; border-top: 1px solid #edf1f5; background: #fbfcfe; }
        .campaign-card-actions .campaign-details-button { flex: 0 0 auto; padding: 11px 15px; border: 1px solid #dbe5ef; border-radius: 999px; color: #173958; font-size: .88rem; font-weight: 700; white-space: nowrap; }
        .campaign-card-actions .campaign-donate-button { flex: 1; min-width: 0; padding: 12px 16px; border: 0; border-radius: 999px; background: linear-gradient(100deg, #183b5b, #0f766e); color: #fff; text-align: center; font-size: .9rem; font-weight: 700; white-space: nowrap; }
        .campaign-card-actions a:hover { filter: brightness(.96); }
        .campaign-detail-card { overflow: hidden; border: 1px solid #e3eaf2; border-radius: 22px; background: #fff; box-shadow: 0 14px 36px rgba(20, 48, 82, .08); }
        .campaign-detail-image { width: 100%; max-height: 520px; object-fit: cover; }
        .campaign-detail-content { padding: 28px; }
        .campaign-detail-content .campaign-category, .campaign-detail-content .campaign-status { position: static; }
        .campaign-detail-content h1 { color: #183b5b; }
        .campaign-detail-copy { color: #475569; line-height: 1.85; }
        .campaign-detail-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 22px; }
        .campaign-detail-stat { padding: 16px; border-radius: 13px; background: #f1f5f9; }
        .campaign-detail-stat strong, .campaign-detail-stat span { display: block; }
        .campaign-detail-stat strong { margin-bottom: 5px; color: #183b5b; font-size: .85rem; }
        .campaign-detail-stat span { color: #64748b; }
        @media (max-width: 767.98px) {
            .campaign-card-media { height: 225px; }
            .campaign-detail-content { padding: 22px; }
            .campaign-detail-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 420px) {
            .campaign-card-actions { flex-wrap: wrap; }
            .campaign-card-actions .campaign-donate-button { flex-basis: 100%; }
            .campaign-amounts { font-size: .82rem; }
        }
    </style>
    <?php endif; ?>

</head>
