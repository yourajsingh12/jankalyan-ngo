<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'add' || $action === 'update') {
        $platform = trim((string) ($_POST['platform'] ?? ''));
        $iconClass = trim((string) ($_POST['icon_class'] ?? ''));
        $url = trim((string) ($_POST['url'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '');
        $hasHttpScheme = preg_match('/^https?:\/\//i', $url) === 1;

        if (
            $platform === ''
            || mb_strlen($platform) > 40
            || $iconClass === ''
            || mb_strlen($iconClass) > 60
            || mb_strlen($url) > 255
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !$hasHttpScheme
            || $sortOrder === false
            || $sortOrder < 0
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Enter a platform, valid icon class and HTTP/HTTPS URL, a valid order, and a status.', 'error');
        } elseif ($action === 'add') {
            q(
                'INSERT INTO social_links (platform, icon_class, url, sort_order, status) VALUES (?, ?, ?, ?, ?)',
                [$platform, $iconClass, $url, $sortOrder, (int) $status]
            );
            flash('Social media link added successfully.');
        } else {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1 || row('SELECT id FROM social_links WHERE id = ?', [$id]) === null) {
                flash('That social media link could not be found.', 'error');
            } else {
                q(
                    'UPDATE social_links SET platform = ?, icon_class = ?, url = ?, sort_order = ?, status = ? WHERE id = ?',
                    [$platform, $iconClass, $url, $sortOrder, (int) $status, $id]
                );
                flash('Social media link updated successfully.');
            }
        }
    } elseif ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1 || row('SELECT id FROM social_links WHERE id = ?', [$id]) === null) {
            flash('That social media link could not be found.', 'error');
        } else {
            q('DELETE FROM social_links WHERE id = ?', [$id]);
            flash('Social media link deleted successfully.');
        }
    } else {
        flash('The requested action is not valid.', 'error');
    }

    header('Location: social_links.php');
    exit;
}

$socialLinks = rows(
    'SELECT id, platform, icon_class, url, sort_order, status FROM social_links ORDER BY sort_order ASC, id ASC'
);
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>
<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#2563eb); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(37,99,235,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Social Media Links</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#dbeafe; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-share-alt"></i> Social Panel
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
                    <?php endforeach; ?>

                    <h4 class="mb-3" style="font-size:1.2rem; font-weight:700; color:#0f172a;">Add Social Media Link</h4>
                    <form method="post" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <div class="col-sm-6 col-lg-2">
                            <label class="form-label" for="new-social-platform">Platform</label>
                            <input class="form-control" id="new-social-platform" name="platform" maxlength="40" required>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label" for="new-social-url">Profile URL</label>
                            <input class="form-control" id="new-social-url" type="url" name="url" maxlength="255" placeholder="https://..." required>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <label class="form-label" for="new-social-icon">Icon class</label>
                            <input class="form-control" id="new-social-icon" name="icon_class" maxlength="60" value="fab fa-link" required>
                        </div>
                        <div class="col-sm-4 col-lg-1">
                            <label class="form-label" for="new-social-order">Order</label>
                            <input class="form-control" id="new-social-order" type="number" name="sort_order" min="0" value="<?= e(count($socialLinks) + 1) ?>" required>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <label class="form-label" for="new-social-status">Status</label>
                            <select class="form-select" id="new-social-status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <button class="btn btn-success w-100" type="submit"><i class="fas fa-plus"></i> Add Link</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1000px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Platform</th><th>Profile URL</th><th>Icon class</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($socialLinks === []): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">No social media links yet. Add the first link above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($socialLinks as $socialLink): ?>
                                    <?php $formId = 'social-update-' . (int) $socialLink['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?= e($socialLink['id']) ?>">
                                            </form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="platform" maxlength="40" value="<?= e($socialLink['platform']) ?>" required aria-label="Platform">
                                        </td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="url" name="url" maxlength="255" value="<?= e($socialLink['url']) ?>" required aria-label="Profile URL"></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" name="icon_class" maxlength="60" value="<?= e($socialLink['icon_class']) ?>" required aria-label="Icon class"></td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="number" name="sort_order" min="0" value="<?= e($socialLink['sort_order']) ?>" required aria-label="Display order"></td>
                                        <td>
                                            <select class="form-select" form="<?= e($formId) ?>" name="status" aria-label="Status">
                                                <option value="1" <?= (int) $socialLink['status'] === 1 ? 'selected' : '' ?>>Active</option>
                                                <option value="0" <?= (int) $socialLink['status'] === 0 ? 'selected' : '' ?>>Hidden</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" type="submit" form="<?= e($formId) ?>" aria-label="Save social media link"><i class="fas fa-save"></i></button>
                                                <form method="post" onsubmit="return confirm('Delete this social media link?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($socialLink['id']) ?>">
                                                    <button class="btn btn-danger" type="submit" aria-label="Delete social media link"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
