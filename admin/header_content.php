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
        $text = trim((string) ($_POST['text'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '');

        if (
            $text === ''
            || mb_strlen($text) > 160
            || $sortOrder === false
            || $sortOrder < 0
            || !in_array($status, ['0', '1'], true)
        ) {
            flash('Enter header text (up to 160 characters), a valid order, and a status.', 'error');
        } elseif ($action === 'add') {
            q(
                'INSERT INTO ticker_items (text, sort_order, status) VALUES (?, ?, ?)',
                [$text, $sortOrder, (int) $status]
            );
            flash('Header content added successfully.');
        } else {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1 || row('SELECT id FROM ticker_items WHERE id = ?', [$id]) === null) {
                flash('That header item could not be found.', 'error');
            } else {
                q(
                    'UPDATE ticker_items SET text = ?, sort_order = ?, status = ? WHERE id = ?',
                    [$text, $sortOrder, (int) $status, $id]
                );
                flash('Header content updated successfully.');
            }
        }
    } elseif ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1 || row('SELECT id FROM ticker_items WHERE id = ?', [$id]) === null) {
            flash('That header item could not be found.', 'error');
        } else {
            q('DELETE FROM ticker_items WHERE id = ?', [$id]);
            flash('Header content deleted successfully.');
        }
    } else {
        flash('The requested action is not valid.', 'error');
    }

    header('Location: header_content.php');
    exit;
}

$headerContentItems = rows(
    'SELECT id, text, sort_order, status FROM ticker_items ORDER BY sort_order ASC, id ASC'
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
                style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(15,118,110,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Header Content</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#e2e8f0; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-stream"></i> Header Ticker
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
                    <?php endforeach; ?>

                    <h4 class="mb-3" style="font-size:1.2rem; font-weight:700; color:#0f172a;">Add Header Text</h4>
                    <form method="post" class="row g-3 align-items-end mb-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <div class="col-lg-7">
                            <label class="form-label" for="new-header-text">Header text</label>
                            <input class="form-control" id="new-header-text" name="text" maxlength="160" required>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <label class="form-label" for="new-header-order">Display order</label>
                            <input class="form-control" id="new-header-order" type="number" name="sort_order" min="0" value="<?= e(count($headerContentItems) + 1) ?>" required>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <label class="form-label" for="new-header-status">Status</label>
                            <select class="form-select" id="new-header-status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-lg-1">
                            <button class="btn btn-success w-100" type="submit"><i class="fas fa-plus"></i> Add</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:760px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Header text</th><th>Display order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($headerContentItems === []): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">No header content yet. Add the first item above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($headerContentItems as $item): ?>
                                    <?php $formId = 'ticker-update-' . (int) $item['id']; ?>
                                    <tr>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                                            </form>
                                            <input class="form-control" form="<?= e($formId) ?>" name="text" maxlength="160" value="<?= e($item['text']) ?>" required aria-label="Header text">
                                        </td>
                                        <td><input class="form-control" form="<?= e($formId) ?>" type="number" name="sort_order" min="0" value="<?= e($item['sort_order']) ?>" required aria-label="Display order"></td>
                                        <td>
                                            <select class="form-select" form="<?= e($formId) ?>" name="status" aria-label="Status">
                                                <option value="1" <?= (int) $item['status'] === 1 ? 'selected' : '' ?>>Active</option>
                                                <option value="0" <?= (int) $item['status'] === 0 ? 'selected' : '' ?>>Hidden</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" type="submit" form="<?= e($formId) ?>" aria-label="Save header content"><i class="fas fa-save"></i></button>
                                                <form method="post" onsubmit="return confirm('Delete this header item?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                                                    <button class="btn btn-danger" type="submit" aria-label="Delete header content"><i class="fas fa-trash"></i></button>
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
