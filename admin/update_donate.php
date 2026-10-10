<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$defaults = [
    'breadcrumb_title' => 'Support Our Mission',
    'tagline' => 'Make A Difference',
    'heading' => 'Your support creates',
    'heading_highlight' => 'lasting change',
    'intro' => 'Your generous contributions help us continue our mission and expand our reach to those in need. Together, we can make a difference.',
    'bank_section_title' => 'NGO Bank Account Details',
    'bank_name_label' => 'Bank Name',
    'bank_name' => 'HDFC BANK',
    'account_number_label' => 'Account Number',
    'account_number' => '50200101465765',
    'ifsc_label' => 'IFSC Code',
    'ifsc_code' => 'HDFC0002645',
    'micr_label' => 'MICR',
    'micr_code' => '1100240248',
    'impact_title' => 'Why Your Donation Matters',
    'impact_intro' => 'Your support directly impacts our community. With your donations, we can:',
    'benefit_1' => 'Support education and learning opportunities.',
    'benefit_2' => 'Provide health and nutrition assistance.',
    'benefit_3' => 'Expand community welfare programs.',
    'benefit_4' => 'Bring hope and dignity to families in need.',
    'contact_title' => 'Need More Information?',
    'contact_phone' => '+91 8929939502',
    'contact_email' => 'support@jankalyansanghfoundation.com',
    'closing_message' => 'Join us in our journey to create a better future for everyone. Your contribution, no matter how small, helps us achieve our goals and bring hope to many lives.',
];

$groups = [
    'Page introduction' => [
        'breadcrumb_title' => ['Breadcrumb heading', 160, 'text'],
        'tagline' => ['Tagline', 160, 'text'],
        'heading' => ['Main heading', 180, 'text'],
        'heading_highlight' => ['Highlighted heading text', 180, 'text'],
        'intro' => ['Introduction paragraph', 2000, 'textarea'],
    ],
    'Bank details' => [
        'bank_section_title' => ['Bank details heading', 160, 'text'],
        'bank_name_label' => ['Bank name label', 80, 'text'],
        'bank_name' => ['Bank name', 120, 'text'],
        'account_number_label' => ['Account number label', 80, 'text'],
        'account_number' => ['Account number', 80, 'text'],
        'ifsc_label' => ['IFSC label', 80, 'text'],
        'ifsc_code' => ['IFSC code', 30, 'text'],
        'micr_label' => ['MICR label', 80, 'text'],
        'micr_code' => ['MICR code', 30, 'text'],
    ],
    'Donation impact' => [
        'impact_title' => ['Impact section heading', 160, 'text'],
        'impact_intro' => ['Impact introduction', 1000, 'textarea'],
        'benefit_1' => ['Benefit 1', 255, 'text'],
        'benefit_2' => ['Benefit 2', 255, 'text'],
        'benefit_3' => ['Benefit 3', 255, 'text'],
        'benefit_4' => ['Benefit 4', 255, 'text'],
    ],
    'Contact and closing message' => [
        'contact_title' => ['Contact heading', 160, 'text'],
        'contact_phone' => ['Contact phone', 50, 'tel'],
        'contact_email' => ['Contact email', 190, 'email'],
        'closing_message' => ['Closing message', 2000, 'textarea'],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $values = [];
    $errors = [];
    foreach ($defaults as $key => $_default) {
        $value = trim((string) ($_POST[$key] ?? ''));
        $maxLength = 0;
        foreach ($groups as $fields) {
            if (isset($fields[$key])) {
                $maxLength = $fields[$key][1];
                break;
            }
        }

        if (($value === '' && $key !== 'micr_code') || mb_strlen($value) > $maxLength) {
            $errors[] = $key;
        }
        $values[$key] = $value;
    }

    if (filter_var($values['contact_email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'contact_email';
    }
    if (preg_match('/[0-9]/', $values['contact_phone']) !== 1) {
        $errors[] = 'contact_phone';
    }

    if ($errors !== []) {
        flash('Please complete every field with valid values within the displayed limits.', 'error');
    } else {
        $existingQrCode = row('SELECT qr_code_image FROM donate_page_content WHERE id = 1');
        $newQrCode = null;
        try {
            $newQrCode = upload_image($_FILES['qr_code_image'] ?? null, 'donations');
        } catch (RuntimeException $exception) {
            flash($exception->getMessage(), 'error');
            header('Location: update_donate.php');
            exit;
        }

        $qrCodeImage = $newQrCode
            ?? (!empty($_POST['remove_qr_code']) ? null : ($existingQrCode['qr_code_image'] ?? null));
        $columns = array_keys($defaults);
        $updates = implode(', ', array_map(
            static fn (string $column): string => $column . ' = VALUES(' . $column . ')',
            $columns
        ));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        try {
            q(
                'INSERT INTO donate_page_content (id, ' . implode(', ', $columns) . ', qr_code_image)
                 VALUES (1, ' . $placeholders . ', ?)
                 ON DUPLICATE KEY UPDATE ' . $updates . ', qr_code_image = VALUES(qr_code_image)',
                [...array_values($values), $qrCodeImage]
            );

            if (
                !empty($existingQrCode['qr_code_image'])
                && $existingQrCode['qr_code_image'] !== $qrCodeImage
            ) {
                delete_upload((string) $existingQrCode['qr_code_image']);
            }
            flash('Donate page content updated successfully.');
        } catch (PDOException $exception) {
            if ($newQrCode !== null) {
                delete_upload($newQrCode);
            }
            error_log('Donate page save failed: ' . $exception->getMessage());
            flash('The donate page could not be saved. Please try again.', 'error');
        }
    }

    header('Location: update_donate.php');
    exit;
}

$donatePage = array_merge(
    $defaults,
    row('SELECT * FROM donate_page_content WHERE id = 1') ?? []
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
                style="background:linear-gradient(135deg,#0f172a,#f59e0b); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(245,158,11,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Edit Donate Page</h3>
                <a href="../donate.php" target="_blank" rel="noopener" class="btn btn-light">
                    <i class="fas fa-external-link-alt"></i> Preview page
                </a>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:24px;">
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>"
                            role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
                    <?php endforeach; ?>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <?php foreach ($groups as $groupTitle => $fields): ?>
                            <fieldset class="mb-4">
                                <legend class="h5 mb-3"><?= e($groupTitle) ?></legend>
                                <div class="row g-3">
                                    <?php foreach ($fields as $key => [$label, $maxLength, $type]): ?>
                                        <div class="<?= $type === 'textarea' ? 'col-12' : 'col-md-6' ?>">
                                            <label class="form-label" for="<?= e($key) ?>"><?= e($label) ?></label>
                                            <?php if ($type === 'textarea'): ?>
                                                <textarea class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>"
                                                    maxlength="<?= $maxLength ?>" rows="3" required><?= e($donatePage[$key]) ?></textarea>
                                            <?php else: ?>
                                                <input class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>"
                                                    type="<?= e($type) ?>" maxlength="<?= $maxLength ?>"
                                                    value="<?= e($donatePage[$key]) ?>" required>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                        <?php endforeach; ?>
                        <fieldset class="mb-4">
                            <legend class="h5 mb-3">Donation QR code</legend>
                            <?php if (!empty($donatePage['qr_code_image'])): ?>
                                <div class="mb-3">
                                    <img src="<?= e(img_url((string) $donatePage['qr_code_image'])) ?>"
                                        alt="Current donation QR code" style="width:180px; height:180px; object-fit:contain; border:1px solid #e2e8f0; border-radius:8px; padding:8px;">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="remove-qr-code" name="remove_qr_code" value="1">
                                    <label class="form-check-label" for="remove-qr-code">Remove current QR code</label>
                                </div>
                            <?php endif; ?>
                            <label class="form-label" for="qr-code-image">Upload QR code image</label>
                            <input class="form-control" id="qr-code-image" type="file" name="qr_code_image"
                                accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            <div class="form-text">JPG, PNG, WEBP or GIF. Leave empty to keep the current QR code.</div>
                        </fieldset>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Donate Page</button>
                            <a class="btn btn-secondary" href="donate.php">Back to Donations</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
