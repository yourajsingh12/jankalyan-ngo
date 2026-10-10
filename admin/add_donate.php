<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function donation_form_redirect(): never
{
    header('Location: donate.php');
    exit;
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$donation = null;
if ($editId !== null && $editId !== false) {
    $donation = row('SELECT * FROM donation_records WHERE id = ?', [$editId]);
    if ($donation === null) {
        flash('That donation could not be found.', 'error');
        donation_form_redirect();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);
    $name = trim((string) ($_POST['donor_name'] ?? ''));
    $mobile = trim((string) ($_POST['donor_mobile'] ?? ''));
    $amountInput = trim((string) ($_POST['amount'] ?? ''));
    $date = trim((string) ($_POST['donation_date'] ?? ''));
    $method = trim((string) ($_POST['payment_method'] ?? ''));
    $purpose = trim((string) ($_POST['purpose'] ?? ''));
    $status = (string) ($_POST['status'] ?? '');
    $dateValue = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $dateValid = $dateValue !== false && $dateValue->format('Y-m-d') === $date;
    $amountValid = preg_match('/^\d{1,10}(?:\.\d{1,2})?$/D', $amountInput) === 1
        && is_finite((float) $amountInput)
        && (float) $amountInput > 0
        && (float) $amountInput <= 9999999999.99;
    $validMethods = ['', 'Bank Transfer', 'Online Payment', 'Cheque', 'Cash', 'Card', 'UPI', 'Other'];

    if (
        ($id !== null && ($id === false || $id < 1))
        || $name === ''
        || mb_strlen($name) > 160
        || mb_strlen($mobile) > 20
        || ($mobile !== '' && preg_match('/^\+?[0-9]{10,15}$/D', $mobile) !== 1)
        || !$amountValid
        || !$dateValid
        || !in_array($method, $validMethods, true)
        || mb_strlen($purpose) > 255
        || !in_array($status, ['Confirmed', 'Pending', 'Cancelled'], true)
    ) {
        flash('Enter a donor name, valid positive amount and date, and valid payment details.', 'error');
        donation_form_redirect();
    }

    if ($id !== null && row('SELECT id FROM donation_records WHERE id = ?', [$id]) === null) {
        flash('That donation could not be found.', 'error');
        donation_form_redirect();
    }

    try {
        if ($id === null) {
            q(
                'INSERT INTO donation_records (donor_name, donor_mobile, amount, donation_date, payment_method, purpose, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$name, $mobile !== '' ? $mobile : null, $amountInput, $date, $method !== '' ? $method : null, $purpose !== '' ? $purpose : null, $status]
            );
            flash('Donation record added successfully.');
        } else {
            q(
                'UPDATE donation_records
                    SET donor_name = ?, donor_mobile = ?, amount = ?, donation_date = ?, payment_method = ?, purpose = ?, status = ?
                  WHERE id = ?',
                [$name, $mobile !== '' ? $mobile : null, $amountInput, $date, $method !== '' ? $method : null, $purpose !== '' ? $purpose : null, $status, $id]
            );
            flash('Donation record updated successfully.');
        }
    } catch (PDOException $exception) {
        error_log('Donation record save failed: ' . $exception->getMessage());
        flash('The donation record could not be saved. Please try again.', 'error');
    }

    donation_form_redirect();
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
                style="background:linear-gradient(135deg,#0f172a,#f59e0b); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(245,158,11,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;"><?= $donation === null ? 'Add Donation' : 'Edit Donation' ?></h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fff7ed; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-gift"></i> Donation Record
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>"
                    role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                        <div class="white_card_body" style="padding:30px;">
                            <form method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e($donation['id'] ?? '') ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="donor-name">Donor name *</label>
                                        <input class="form-control" id="donor-name" type="text" name="donor_name" maxlength="160" value="<?= e($donation['donor_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="donor-mobile">Donor mobile number</label>
                                        <input class="form-control" id="donor-mobile" type="tel" name="donor_mobile" maxlength="20" pattern="\+?[0-9]{10,15}" value="<?= e($donation['donor_mobile'] ?? '') ?>" placeholder="e.g. +919876543210">
                                        <div class="form-text">Optional. Enter 10–15 digits, with an optional leading +.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="donation-amount">Amount (₹) *</label>
                                        <input class="form-control" id="donation-amount" type="number" name="amount" min="0.01" max="9999999999.99" step="0.01" value="<?= e($donation['amount'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="donation-date">Donation date *</label>
                                        <input class="form-control" id="donation-date" type="date" name="donation_date" value="<?= e($donation['donation_date'] ?? date('Y-m-d')) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="payment-method">Payment method</label>
                                        <select class="form-select" id="payment-method" name="payment_method">
                                            <?php foreach (['' => 'Select method', 'Bank Transfer' => 'Bank Transfer', 'Online Payment' => 'Online Payment', 'UPI' => 'UPI', 'Cheque' => 'Cheque', 'Cash' => 'Cash', 'Card' => 'Card', 'Other' => 'Other'] as $value => $label): ?>
                                                <option value="<?= e($value) ?>" <?= ($donation['payment_method'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="donation-purpose">Purpose / program</label>
                                        <input class="form-control" id="donation-purpose" type="text" name="purpose" maxlength="255" value="<?= e($donation['purpose'] ?? '') ?>" placeholder="e.g. Education program">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="donation-status">Status</label>
                                        <select class="form-select" id="donation-status" name="status" required>
                                            <?php foreach (['Confirmed', 'Pending', 'Cancelled'] as $status): ?>
                                                <option value="<?= e($status) ?>" <?= ($donation['status'] ?? 'Confirmed') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex flex-wrap gap-2 mt-4">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> <?= $donation === null ? 'Save Donation' : 'Update Donation' ?></button>
                                        <a href="donate.php" class="btn btn-secondary">Back to Donations</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
