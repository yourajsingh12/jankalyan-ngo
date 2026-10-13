<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function bannerRedirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function bannerEditUrl(?int $id, string $error): string
{
    $url = 'edit_banner.php';
    if ($id !== null) {
        $url .= '?id=' . $id . '&';
    } else {
        $url .= '?';
    }

    return $url . 'error=' . rawurlencode($error);
}

function removeUnusedBannerImage(string $imagePath): void
{
    $uploadPrefix = trim(UPLOAD_DIR, '/') . '/';
    if (!str_starts_with($imagePath, $uploadPrefix)) {
        return;
    }

    if ((int) val('SELECT COUNT(*) FROM banners WHERE image_path = ?', [$imagePath], 0) !== 0) {
        return;
    }

    $uploadRoot = realpath(UPLOAD_PATH);
    $filePath = realpath(
        BASE_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath)
    );
    if ($uploadRoot === false || $filePath === false || !is_file($filePath)) {
        return;
    }

    $prefix = rtrim($uploadRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (str_starts_with($filePath, $prefix)) {
        unlink($filePath);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bannerRedirect('banner.php');
}

$sessionToken = (string) ($_SESSION['banner_csrf'] ?? '');
$requestToken = (string) ($_POST['csrf_token'] ?? '');
if ($sessionToken === '' || !hash_equals($sessionToken, $requestToken)) {
    bannerRedirect('banner.php?error=invalid_request');
}

$action = (string) ($_POST['action'] ?? '');
$idInput = trim((string) ($_POST['id'] ?? ''));
$id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);
if ($id === false || ($id !== null && $id < 1)) {
    bannerRedirect('banner.php?error=not_found');
}

if ($action === 'delete') {
    if ($id === null) {
        bannerRedirect('banner.php?error=not_found');
    }

    $banner = row("SELECT id, image_path FROM banners WHERE id = ? AND page_name = 'home'", [$id]);
    if ($banner === null) {
        bannerRedirect('banner.php?error=not_found');
    }

    q("DELETE FROM banners WHERE id = ? AND page_name = 'home'", [$id]);
    removeUnusedBannerImage((string) $banner['image_path']);
    bannerRedirect('banner.php?success=deleted');
}

if ($action !== 'save') {
    bannerRedirect('banner.php?error=invalid_request');
}

$title = trim((string) ($_POST['title'] ?? ''));
$status = (string) ($_POST['status'] ?? 'Active');
$sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);

if (
    $title === ''
    || mb_strlen($title) > 255
    || !in_array($status, ['Active', 'Draft'], true)
    || $sortOrder === false
    || $sortOrder < 0
    || $sortOrder > 999999
) {
    bannerRedirect(bannerEditUrl($id, 'invalid_details'));
}

$existing = null;
if ($id !== null) {
    $existing = row(
        "SELECT id, image_path FROM banners WHERE id = ? AND page_name = 'home'",
        [$id]
    );
    if ($existing === null) {
        bannerRedirect('banner.php?error=not_found');
    }
}

$newImagePath = null;
$file = $_FILES['banner_image'] ?? null;
$uploadError = is_array($file) ? (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;

if ($uploadError !== UPLOAD_ERR_NO_FILE) {
    if (
        $uploadError !== UPLOAD_ERR_OK
        || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))
        || (int) ($file['size'] ?? 0) > UPLOAD_MAX_BYTES
    ) {
        bannerRedirect(bannerEditUrl($id, 'invalid_image'));
    }

    $imageInfo = @getimagesize((string) $file['tmp_name']);
    $mime = $imageInfo['mime'] ?? '';
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($extensions[$mime]) || $imageInfo[0] !== 1672 || $imageInfo[1] !== 941) {
        bannerRedirect(bannerEditUrl($id, 'invalid_image'));
    }

    if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0755, true) && !is_dir(UPLOAD_PATH)) {
        bannerRedirect(bannerEditUrl($id, 'upload_failed'));
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $newImagePath = trim(UPLOAD_DIR, '/') . '/' . $filename;
    $destination = UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        bannerRedirect(bannerEditUrl($id, 'upload_failed'));
    }
} elseif ($existing === null) {
    bannerRedirect(bannerEditUrl(null, 'invalid_image'));
}

$imagePath = $newImagePath ?? (string) $existing['image_path'];

try {
    if ($existing === null) {
        q(
            'INSERT INTO banners (page_name, title, subtitle, image_path, link_url, link_label, sort_order, status)
             VALUES (?, ?, NULL, ?, NULL, NULL, ?, ?)',
            ['home', $title, $imagePath, $sortOrder, $status]
        );
        $success = 'created';
    } else {
        q(
            'UPDATE banners
             SET title = ?, image_path = ?, sort_order = ?, status = ?
             WHERE id = ? AND page_name = \'home\'',
            [$title, $imagePath, $sortOrder, $status, $id]
        );
        $success = 'updated';
    }
} catch (PDOException $exception) {
    if ($newImagePath !== null) {
        removeUnusedBannerImage($newImagePath);
    }
    error_log('Banner save failed: ' . $exception->getMessage());
    bannerRedirect(bannerEditUrl($id, 'save_failed'));
}

if ($newImagePath !== null && $existing !== null) {
    removeUnusedBannerImage((string) $existing['image_path']);
}

bannerRedirect('banner.php?success=' . $success);
