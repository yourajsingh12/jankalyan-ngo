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
        .project-impact { padding: 65px 0; background: #eaf4ed; }
        .project-impact h2 { margin-bottom: 14px; }
        .project-impact p { color: #667085; line-height: 1.8; }
        .impact-point { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 18px; }
        .impact-point i { flex: 0 0 auto; margin-top: 4px; color: var(--theme-color); }
        .impact-point p { margin: 0; }
        @media (max-width: 575.98px) {
            .project-feature { min-height: 390px; padding: 30px 22px; }
            .project-card-body { padding: 22px; }
            .project-card-image { height: 190px; }
        }
    </style>
    <?php endif; ?>

</head>
