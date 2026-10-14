<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$footerFields = [
    'footer_about' => [
        'label' => 'Footer description',
        'group' => 'footer',
        'input_type' => 'textarea',
        'max_length' => 2000,
    ],
    'contact_address_plain' => [
        'label' => 'Office address',
        'group' => 'contact',
        'input_type' => 'textarea',
        'max_length' => 1000,
    ],
    'contact_phone' => [
        'label' => 'Phone numbers (one per line)',
        'group' => 'contact',
        'input_type' => 'text',
        'max_length' => 500,
    ],
    'contact_email' => [
        'label' => 'Email address',
        'group' => 'contact',
        'input_type' => 'text',
        'max_length' => 190,
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $key = (string) ($_POST['key'] ?? '');

    if (!isset($footerFields[$key])) {
        flash('That footer field is not valid.', 'error');
    } elseif ($action === 'add' || $action === 'update') {
        $field = $footerFields[$key];
        $value = trim((string) ($_POST['value'] ?? ''));
        $exists = row('SELECT setting_key FROM site_settings WHERE setting_key = ?', [$key]) !== null;
        $phoneNumbers = $key === 'contact_phone' ? contact_phone_numbers($value) : [];
        $validPhoneNumbers = $phoneNumbers !== [];
        foreach ($phoneNumbers as $phoneNumber) {
            if (preg_match('/^\+?[0-9\s().-]{10,25}$/D', $phoneNumber) !== 1) {
                $validPhoneNumbers = false;
                break;
            }
        }

        if (
            $value === ''
            || mb_strlen($value) > $field['max_length']
            || ($key === 'contact_email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false)
            || ($key === 'contact_phone' && !$validPhoneNumbers)
        ) {
            flash('Enter a valid, non-empty value within the field limit.', 'error');
        } elseif (($action === 'add' && $exists) || ($action === 'update' && !$exists)) {
            flash('That footer field has changed. Refresh the page and try again.', 'error');
        } else {
            q(
                'INSERT INTO site_settings (setting_key, setting_value, setting_group, label, input_type)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
                [$key, $value, $field['group'], $field['label'], $field['input_type']]
            );
            flash($action === 'add' ? 'Footer content added successfully.' : 'Footer content updated successfully.');
        }
    } elseif ($action === 'delete') {
        if (row('SELECT setting_key FROM site_settings WHERE setting_key = ?', [$key]) === null) {
            flash('That footer field could not be found.', 'error');
        } else {
            q('DELETE FROM site_settings WHERE setting_key = ?', [$key]);
            flash('Footer content deleted successfully.');
        }
    } else {
        flash('The requested action is not valid.', 'error');
    }

    header('Location: footer_content.php');
    exit;
}

$footerSettings = [];
foreach (rows(
    'SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN (?, ?, ?, ?)',
    array_keys($footerFields)
) as $settingRow) {
    $footerSettings[$settingRow['setting_key']] = (string) $settingRow['setting_value'];
}
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
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Footer Content</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#e2e8f0; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-edit"></i> Footer Settings
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
                    <?php endforeach; ?>

                    <?php
                    $missingFields = array_diff_key($footerFields, $footerSettings);
                    if ($missingFields !== []):
                    ?>
                        <h4 class="mb-3" style="font-size:1.2rem; font-weight:700; color:#0f172a;">Add Footer Content</h4>
                        <form method="post" class="row g-3 align-items-end mb-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="add">
                            <div class="col-md-4">
                                <label class="form-label" for="new-footer-key">Content field</label>
                                <select class="form-select" id="new-footer-key" name="key" required>
                                    <?php foreach ($missingFields as $key => $field): ?>
                                        <option value="<?= e($key) ?>"><?= e($field['label']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="new-footer-value">Content</label>
                                <textarea class="form-control" id="new-footer-value" name="value" rows="2" maxlength="2000" required></textarea>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success w-100" type="submit"><i class="fas fa-plus"></i> Add</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <h4 class="mb-3" style="font-size:1.2rem; font-weight:700; color:#0f172a;">Edit Footer Content</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:760px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Field</th><th>Content</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($footerSettings === []): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">No footer content yet. Add a field above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($footerSettings as $key => $value): ?>
                                    <?php $formId = 'footer-update-' . $key; ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($footerFields[$key]['label']) ?></td>
                                        <td>
                                            <form id="<?= e($formId) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="key" value="<?= e($key) ?>">
                                            </form>
                                            <?php if ($key === 'contact_phone'): ?>
                                                <textarea class="form-control" form="<?= e($formId) ?>" name="value" rows="3" maxlength="<?= e($footerFields[$key]['max_length']) ?>" placeholder="Add one phone number per line" required><?= e($value) ?></textarea>
                                            <?php elseif ($footerFields[$key]['input_type'] === 'textarea'): ?>
                                                <textarea class="form-control" form="<?= e($formId) ?>" name="value" rows="3" maxlength="<?= e($footerFields[$key]['max_length']) ?>" required><?= e($value) ?></textarea>
                                            <?php else: ?>
                                                <input class="form-control" form="<?= e($formId) ?>" name="value" maxlength="<?= e($footerFields[$key]['max_length']) ?>" value="<?= e($value) ?>" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary" type="submit" form="<?= e($formId) ?>" aria-label="Save <?= e($footerFields[$key]['label']) ?>"><i class="fas fa-save"></i></button>
                                                <form method="post" onsubmit="return confirm('Delete this footer content?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="key" value="<?= e($key) ?>">
                                                    <button class="btn btn-danger" type="submit" aria-label="Delete <?= e($footerFields[$key]['label']) ?>"><i class="fas fa-trash"></i></button>
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
