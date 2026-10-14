<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';
ensure_participants_table();

function participant_admin_redirect(string $suffix = ''): never
{
    redirect('participants.php' . $suffix);
}

function remove_unused_participant_photo(string $photo): void
{
    if ((int) val('SELECT COUNT(*) FROM participants WHERE profile_photo = ?', [$photo], 0) === 0) {
        delete_upload($photo);
    }
}

function participant_admin_options(array $options, string $selected): void
{
    foreach ($options as $option) {
        echo '<option value="' . e($option) . '"' . ($selected === $option ? ' selected' : '') . '>' . e($option) . '</option>';
    }
}

$genderOptions = ['Male', 'Female', 'Transgender', 'Other'];
$maritalOptions = ['Single', 'Married', 'Widowed', 'Divorced'];
$categoryOptions = ['General', 'OBC', 'SC', 'ST', 'Minority', 'Other'];
$bloodGroupOptions = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$occupationOptions = ['Student', 'Private Job', 'Government Job', 'Business', 'Farmer', 'Self Employed', 'Homemaker', 'Retired', 'Other'];
$stateOptions = ['Delhi', 'Haryana', 'Uttar Pradesh', 'Punjab', 'Rajasthan', 'Other'];
$districtOptions = ['Central Delhi', 'East Delhi', 'West Delhi', 'North Delhi', 'South Delhi', 'Other'];
$designationOptions = ['Member', 'Volunteer', 'Coordinator', 'District Lead', 'State Manager', 'Advisor', 'Supporter'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
    $idInput = is_string($_POST['id'] ?? null) ? trim($_POST['id']) : '';
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete' || $action === 'toggle-approval') {
        if ($id === false || $id === null || $id < 1) {
            flash('That participant could not be found.', 'error');
            participant_admin_redirect();
        }

        $participant = row('SELECT id, profile_photo, status FROM participants WHERE id = ?', [$id]);
        if ($participant === null) {
            flash('That participant could not be found.', 'error');
            participant_admin_redirect();
        }

        if ($action === 'delete') {
            q('DELETE FROM participants WHERE id = ?', [$id]);
            if (!empty($participant['profile_photo'])) {
                remove_unused_participant_photo((string) $participant['profile_photo']);
            }
            flash('Participant deleted successfully.');
        } else {
            $newStatus = (int) $participant['status'] === 1 ? 0 : 1;
            q('UPDATE participants SET status = ? WHERE id = ?', [$newStatus, $id]);
            flash($newStatus === 1 ? 'Participant approved successfully.' : 'Participant set to pending.');
        }

        participant_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        participant_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, profile_photo FROM participants WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That participant could not be found.', 'error');
        participant_admin_redirect();
    }

    $fields = [
        'full_name', 'mobile', 'email', 'dob', 'gender', 'marital_status',
        'guardian_name', 'aadhar', 'category', 'blood_group', 'occupation',
        'address', 'pincode', 'state', 'district', 'designation',
    ];
    $participantData = [];
    foreach ($fields as $field) {
        $participantData[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }
    $sortStatus = is_string($_POST['status'] ?? null) ? $_POST['status'] : '';
    $dateParts = explode('-', $participantData['dob']);
    $validDate = count($dateParts) === 3
        && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $participantData['dob']) === 1
        && checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0])
        && $participantData['dob'] <= date('Y-m-d');

    if (
        $participantData['full_name'] === ''
        || mb_strlen($participantData['full_name']) > 255
        || preg_match('/^[0-9]{10}$/', $participantData['mobile']) !== 1
        || mb_strlen($participantData['email']) > 190
        || filter_var($participantData['email'], FILTER_VALIDATE_EMAIL) === false
        || !$validDate
        || !in_array($participantData['gender'], $genderOptions, true)
        || !in_array($participantData['marital_status'], $maritalOptions, true)
        || $participantData['guardian_name'] === ''
        || mb_strlen($participantData['guardian_name']) > 255
        || preg_match('/^[0-9]{12}$/', $participantData['aadhar']) !== 1
        || !in_array($participantData['category'], $categoryOptions, true)
        || ($participantData['blood_group'] !== '' && !in_array($participantData['blood_group'], $bloodGroupOptions, true))
        || !in_array($participantData['occupation'], $occupationOptions, true)
        || $participantData['address'] === ''
        || mb_strlen($participantData['address']) > 2000
        || preg_match('/^[0-9]{6}$/', $participantData['pincode']) !== 1
        || !in_array($participantData['state'], $stateOptions, true)
        || !in_array($participantData['district'], $districtOptions, true)
        || !in_array($participantData['designation'], $designationOptions, true)
        || !in_array($sortStatus, ['0', '1'], true)
    ) {
        flash('Please enter valid participant details and approval status.', 'error');
        participant_admin_redirect($id === null ? '#participant-form' : '?edit=' . $id . '#participant-form');
    }

    try {
        $newPhoto = upload_image($_FILES['profile_photo'] ?? null, 'participants');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        participant_admin_redirect($id === null ? '#participant-form' : '?edit=' . $id . '#participant-form');
    }

    $profilePhoto = $newPhoto ?? (string) ($existing['profile_photo'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO participants (
                    full_name, mobile, email, dob, gender, marital_status, guardian_name,
                    aadhar, category, blood_group, occupation, address, pincode, state,
                    district, designation, profile_photo, status
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $participantData['full_name'], $participantData['mobile'], $participantData['email'],
                    $participantData['dob'], $participantData['gender'], $participantData['marital_status'],
                    $participantData['guardian_name'], $participantData['aadhar'], $participantData['category'],
                    $participantData['blood_group'] !== '' ? $participantData['blood_group'] : null,
                    $participantData['occupation'], $participantData['address'], $participantData['pincode'],
                    $participantData['state'], $participantData['district'], $participantData['designation'],
                    $profilePhoto !== '' ? $profilePhoto : null, (int) $sortStatus,
                ]
            );
            flash('Participant added successfully.');
        } else {
            q(
                'UPDATE participants
                    SET full_name = ?, mobile = ?, email = ?, dob = ?, gender = ?, marital_status = ?,
                        guardian_name = ?, aadhar = ?, category = ?, blood_group = ?, occupation = ?,
                        address = ?, pincode = ?, state = ?, district = ?, designation = ?,
                        profile_photo = ?, status = ?
                  WHERE id = ?',
                [
                    $participantData['full_name'], $participantData['mobile'], $participantData['email'],
                    $participantData['dob'], $participantData['gender'], $participantData['marital_status'],
                    $participantData['guardian_name'], $participantData['aadhar'], $participantData['category'],
                    $participantData['blood_group'] !== '' ? $participantData['blood_group'] : null,
                    $participantData['occupation'], $participantData['address'], $participantData['pincode'],
                    $participantData['state'], $participantData['district'], $participantData['designation'],
                    $profilePhoto !== '' ? $profilePhoto : null, (int) $sortStatus, $id,
                ]
            );
            if ($newPhoto !== null && !empty($existing['profile_photo'])) {
                remove_unused_participant_photo((string) $existing['profile_photo']);
            }
            flash('Participant updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newPhoto !== null) {
            delete_upload($newPhoto);
        }
        error_log('Participant save failed: ' . $exception->getMessage());
        flash('The participant could not be saved. Please try again.', 'error');
    }

    participant_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM participants WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That participant could not be found.', 'error');
        participant_admin_redirect();
    }
}

$viewId = filter_input(INPUT_GET, 'view', FILTER_VALIDATE_INT);
$viewParticipant = null;
if ($viewId !== null && $viewId !== false) {
    $viewParticipant = row('SELECT * FROM participants WHERE id = ?', [$viewId]);
    if ($viewParticipant === null) {
        flash('That participant could not be found.', 'error');
        participant_admin_redirect();
    }
}

$idCardInput = is_string($_GET['id-card'] ?? null) ? $_GET['id-card'] : '';
$idCardId = $idCardInput === '' ? null : filter_var($idCardInput, FILTER_VALIDATE_INT);
$idCardParticipant = null;
if ($idCardInput !== '') {
    if ($idCardId === false || $idCardId < 1) {
        flash('That participant ID card could not be found.', 'error');
        participant_admin_redirect();
    }

    $idCardParticipant = row(
        'SELECT id, full_name, mobile, email, dob, designation, profile_photo, status
           FROM participants
          WHERE id = ?',
        [$idCardId]
    );
    if ($idCardParticipant === null || (int) $idCardParticipant['status'] !== 1) {
        flash('ID cards are available only for approved participants.', 'error');
        participant_admin_redirect();
    }
}

$participants = rows(
    'SELECT id, full_name, email, mobile, designation, submitted_at, status
       FROM participants
      ORDER BY submitted_at DESC, id DESC'
);
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <style>
        .participant-id-card {
            position: relative; container-type: inline-size; width: min(340px, 100%); aspect-ratio: 591 / 1004;
            overflow: hidden; box-shadow: 0 22px 55px rgba(30, 58, 138, .2); font-family: Arial, sans-serif;
        }
        .participant-id-card--front { order: 0; }
        .participant-id-card--back { order: 1; }
        .participant-id-card__template { position: absolute; z-index: 0; inset: 0; width: 100%; height: 100%; }
        .participant-id-card__photo, .participant-id-card__initial {
            position: absolute; z-index: 2; top: 16.8%; left: 27.6%; width: 44.3%; aspect-ratio: 1;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            border-radius: 50%; background: #e0f2fe; color: #123d70; font-size: 18cqw; font-weight: 800; object-fit: cover;
        }
        .participant-id-card__name {
            position: absolute; z-index: 2; top: 45%; left: 5%; width: 90%; text-align: center; overflow: hidden;
            color: #123d70; font-size: 6cqw; font-weight: 800; line-height: 1.3; text-overflow: ellipsis; white-space: nowrap;
        }
        .participant-id-card__designation {
            position: absolute; z-index: 2; top: 50.3%; left: 5%; width: 90%; text-align: center; overflow: hidden;
            color: #274b73; font-size: 4cqw; font-weight: 700; line-height: 1.3; text-overflow: ellipsis; white-space: nowrap;
        }
        .participant-id-card__details {
            position: absolute; z-index: 2; top: 59.5%; left: 24.5%; right: 4%; display: grid;
            grid-template-rows: repeat(4, 6cqw); color: #172554; font-size: 3.2cqw; font-weight: 600; line-height: 6cqw;
        }
        .participant-id-card__detail { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .participant-id-print-area {
            display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 24px;
            background: #eff6ff; border: 1px solid #c7d2fe; border-radius: 18px;
        }
        @media print {
            @page { size: 54mm 91.8mm; margin: 0; }
            html, body { width: 54mm; height: 91.8mm; margin: 0 !important; padding: 0 !important; background: #fff !important; }
            body * { visibility: hidden !important; }
            .participant-id-print-area, .participant-id-print-area * { visibility: visible !important; }
            .participant-id-print-area {
                position: absolute; inset: 0 auto auto 0; display: block; width: 54mm; margin: 0; padding: 0;
                border: 0; border-radius: 0; background: #fff;
            }
            .participant-id-card {
                width: 54mm; height: 91.8mm; aspect-ratio: auto; margin: 0; box-shadow: none;
                print-color-adjust: exact; -webkit-print-color-adjust: exact;
            }
            .participant-id-card--back { break-before: page; page-break-before: always; }
            .participant-id-print-actions, .participant-id-download-message { display: none !important; }
        }
    </style>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#0ea5e9); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(14,165,233,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Participants</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#e0f2fe; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-users"></i> Participation Panel
                </div>
            </div>

            <?php if ($idCardParticipant !== null): ?>
                <div class="participant-id-print-area mb-4" id="participant-id-card">
                    <div class="participant-id-card participant-id-card--front" data-card-side="front" data-card-number="JKSF-<?= e(str_pad((string) $idCardParticipant['id'], 6, '0', STR_PAD_LEFT)) ?>" aria-label="ID card with QR code and participant details for <?= e($idCardParticipant['full_name']) ?>">
                        <img class="participant-id-card__template" src="<?= e(base_url('assets/img/idcard/id_card.png?v=5')) ?>" alt="Jan Kalyan ID card template with QR code">
                        <?php if (!empty($idCardParticipant['profile_photo'])): ?>
                            <img class="participant-id-card__photo" src="<?= e(img_url((string) $idCardParticipant['profile_photo'])) ?>" alt="<?= e($idCardParticipant['full_name']) ?>">
                        <?php else: ?>
                            <div class="participant-id-card__initial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $idCardParticipant['full_name'], 0, 1))) ?></div>
                        <?php endif; ?>
                        <div class="participant-id-card__name"><?= e($idCardParticipant['full_name']) ?></div>
                        <div class="participant-id-card__designation"><?= e($idCardParticipant['designation']) ?></div>
                        <div class="participant-id-card__details">
                            <div class="participant-id-card__detail">JKSF-<?= e(str_pad((string) $idCardParticipant['id'], 6, '0', STR_PAD_LEFT)) ?></div>
                            <div class="participant-id-card__detail"><?= e($idCardParticipant['email']) ?></div>
                            <div class="participant-id-card__detail"><?= e($idCardParticipant['mobile']) ?></div>
                            <div class="participant-id-card__detail"><?= e(fmt_date((string) $idCardParticipant['dob'], 'd/m/Y')) ?></div>
                        </div>
                    </div>
                    <div class="participant-id-card participant-id-card--back" data-card-side="back" data-card-number="JKSF-<?= e(str_pad((string) $idCardParticipant['id'], 6, '0', STR_PAD_LEFT)) ?>" aria-label="ID card back">
                        <img class="participant-id-card__template" src="<?= e(base_url('assets/img/idcard/id_card2.png?v=5')) ?>" alt="Jan Kalyan ID card back template">
                    </div>
                    <div class="participant-id-print-actions d-flex flex-wrap justify-content-center gap-2">
                        <button type="button" class="btn btn-primary" data-download-id-card><i class="fas fa-download"></i> Download ID card</button>
                        <button type="button" class="btn btn-success" onclick="window.print()"><i class="fas fa-print"></i> Print ID card</button>
                        <a class="btn btn-outline-secondary" href="participants.php#participant-records"><i class="fas fa-arrow-left"></i> Back to records</a>
                    </div>
                    <div class="participant-id-download-message text-danger small text-center" role="status" aria-live="polite"></div>
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <?php if ($viewParticipant !== null): ?>
                <div class="white_card mb_30" id="participant-details" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                    <div class="white_card_body" style="padding:22px;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Participant Details</h4>
                            <a class="btn btn-secondary btn-sm" href="participants.php"><i class="fas fa-times"></i> Close</a>
                        </div>
                        <?php if (!empty($viewParticipant['profile_photo'])): ?>
                            <img src="<?= e(img_url($viewParticipant['profile_photo'])) ?>" alt="<?= e($viewParticipant['full_name']) ?>" style="max-width:140px; max-height:140px; border-radius:14px; object-fit:cover; margin-bottom:20px;">
                        <?php endif; ?>
                        <div class="row g-3">
                            <?php foreach ([
                                'Full name' => 'full_name', 'Mobile' => 'mobile', 'Email' => 'email',
                                'Date of birth' => 'dob', 'Gender' => 'gender', 'Marital status' => 'marital_status',
                                'Father / Husband name' => 'guardian_name', 'Aadhar number' => 'aadhar',
                                'Category' => 'category', 'Blood group' => 'blood_group', 'Occupation' => 'occupation',
                                'Address' => 'address', 'PIN code' => 'pincode', 'State' => 'state',
                                'District' => 'district', 'Designation' => 'designation',
                                'Submitted' => 'submitted_at',
                            ] as $label => $field): ?>
                                <div class="col-md-6"><strong><?= e($label) ?>:</strong> <?= e($viewParticipant[$field]) ?></div>
                            <?php endforeach; ?>
                            <div class="col-md-6"><strong>Status:</strong> <?= (int) $viewParticipant['status'] === 1 ? 'Approved' : 'Pending' ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="white_card mb_30" id="participant-form" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0"><?= $selected === null ? 'Add Participant' : 'Edit Participant' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="participants.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="participant-name" class="form-label">Full name *</label>
                                <input id="participant-name" class="form-control" name="full_name" maxlength="255" value="<?= e($selected['full_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-mobile" class="form-label">Mobile *</label>
                                <input id="participant-mobile" class="form-control" name="mobile" type="tel" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" value="<?= e($selected['mobile'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-email" class="form-label">Email *</label>
                                <input id="participant-email" class="form-control" name="email" type="email" maxlength="190" value="<?= e($selected['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-dob" class="form-label">Date of birth *</label>
                                <input id="participant-dob" class="form-control" name="dob" type="date" max="<?= e(date('Y-m-d')) ?>" value="<?= e($selected['dob'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-gender" class="form-label">Gender *</label>
                                <select id="participant-gender" class="form-select" name="gender" required>
                                    <option value="">Select gender</option>
                                    <?php participant_admin_options($genderOptions, (string) ($selected['gender'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-marital" class="form-label">Marital status *</label>
                                <select id="participant-marital" class="form-select" name="marital_status" required>
                                    <option value="">Select marital status</option>
                                    <?php participant_admin_options($maritalOptions, (string) ($selected['marital_status'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-guardian" class="form-label">Father / Husband name *</label>
                                <input id="participant-guardian" class="form-control" name="guardian_name" maxlength="255" value="<?= e($selected['guardian_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-aadhar" class="form-label">Aadhar number *</label>
                                <input id="participant-aadhar" class="form-control" name="aadhar" maxlength="12" pattern="[0-9]{12}" inputmode="numeric" value="<?= e($selected['aadhar'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-category" class="form-label">Category *</label>
                                <select id="participant-category" class="form-select" name="category" required>
                                    <option value="">Select category</option>
                                    <?php participant_admin_options($categoryOptions, (string) ($selected['category'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-blood" class="form-label">Blood group</label>
                                <select id="participant-blood" class="form-select" name="blood_group">
                                    <option value="">Select blood group</option>
                                    <?php participant_admin_options($bloodGroupOptions, (string) ($selected['blood_group'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-occupation" class="form-label">Occupation *</label>
                                <select id="participant-occupation" class="form-select" name="occupation" required>
                                    <option value="">Select occupation</option>
                                    <?php participant_admin_options($occupationOptions, (string) ($selected['occupation'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-designation" class="form-label">Designation *</label>
                                <select id="participant-designation" class="form-select" name="designation" required>
                                    <option value="">Select designation</option>
                                    <?php participant_admin_options($designationOptions, (string) ($selected['designation'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="participant-address" class="form-label">Address *</label>
                                <textarea id="participant-address" class="form-control" name="address" maxlength="2000" rows="3" required><?= e($selected['address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-pincode" class="form-label">PIN code *</label>
                                <input id="participant-pincode" class="form-control" name="pincode" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" value="<?= e($selected['pincode'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-state" class="form-label">State *</label>
                                <select id="participant-state" class="form-select" name="state" required>
                                    <option value="">Select state</option>
                                    <?php participant_admin_options($stateOptions, (string) ($selected['state'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-district" class="form-label">District *</label>
                                <select id="participant-district" class="form-select" name="district" required>
                                    <option value="">Select district</option>
                                    <?php participant_admin_options($districtOptions, (string) ($selected['district'] ?? '')); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="participant-status" class="form-label">Approval status</label>
                                <select id="participant-status" class="form-select" name="status">
                                    <option value="0" <?= (string) ($selected['status'] ?? '0') === '0' ? 'selected' : '' ?>>Pending</option>
                                    <option value="1" <?= (string) ($selected['status'] ?? '') === '1' ? 'selected' : '' ?>>Approved</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="participant-photo" class="form-label">Profile photo (optional)</label>
                                <input id="participant-photo" class="form-control" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <?php if ($selected !== null && !empty($selected['profile_photo'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Current photo</label><br>
                                    <img src="<?= e(img_url($selected['profile_photo'])) ?>" alt="<?= e($selected['full_name']) ?>" style="max-width:140px; max-height:100px; border-radius:10px; object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Participant' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" id="participant-records" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Participation Records</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:1100px;">
                            <thead style="background:#f1f5f9; color:#0f172a;">
                                <tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Designation</th><th>Submitted</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($participants === []): ?>
                                    <tr><td colspan="8" class="text-center text-muted p-4">No participants have registered yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($participants as $person): ?>
                                    <?php $approved = (int) $person['status'] === 1; ?>
                                    <tr>
                                        <td><?= e($person['id']) ?></td>
                                        <td><?= e($person['full_name']) ?></td>
                                        <td><?= e($person['email']) ?></td>
                                        <td><?= e($person['mobile']) ?></td>
                                        <td><?= e($person['designation']) ?></td>
                                        <td><?= e(fmt_date((string) $person['submitted_at'], 'd M Y')) ?></td>
                                        <td>
                                            <span class="badge" style="background:<?= $approved ? '#dcfce7' : '#fef3c7' ?>; color:<?= $approved ? '#166534' : '#b45309' ?>;">
                                                <?= $approved ? 'Approved' : 'Pending' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="participants.php?view=<?= e($person['id']) ?>#participant-details" class="btn btn-sm btn-info" title="Show participant details" aria-label="Show participant details" style="border:none; border-radius:8px; color:#fff;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="participants.php?edit=<?= e($person['id']) ?>#participant-form" class="btn btn-sm btn-primary" title="Edit" aria-label="Edit participant" style="background:#0f766e; border:none; border-radius:8px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($approved): ?>
                                                    <a href="participants.php?id-card=<?= e($person['id']) ?>#participant-id-card" class="btn btn-sm btn-light" title="Generate ID card" aria-label="Generate ID card for <?= e($person['full_name']) ?>" style="border:1px solid #99f6e4; border-radius:8px; color:#0f766e;">
                                                        <i class="fas fa-id-card"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <form method="post">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="toggle-approval">
                                                    <input type="hidden" name="id" value="<?= e($person['id']) ?>">
                                                    <button type="submit" class="btn btn-sm <?= $approved ? 'btn-warning' : 'btn-success' ?>" title="<?= $approved ? 'Set pending' : 'Approve participant' ?>" aria-label="<?= $approved ? 'Set participant pending' : 'Approve participant' ?>" style="border-radius:8px;">
                                                        <i class="fas <?= $approved ? 'fa-clock' : 'fa-check' ?>"></i>
                                                    </button>
                                                </form>
                                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this participant?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($person['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete participant" style="background:#dc2626; border:none; border-radius:8px; color:#fff;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

    <?php if ($idCardParticipant !== null): ?>
        <script>
            (() => {
                const downloadButtons = document.querySelectorAll('[data-download-id-card]');
                const message = document.querySelector('.participant-id-download-message');

                if (downloadButtons.length === 0 || !message) {
                    return;
                }

                const loadImage = (source) => new Promise((resolve, reject) => {
                    const image = new Image();
                    image.onload = () => resolve(image);
                    image.onerror = () => reject(new Error('An image on the ID card could not be loaded.'));
                    image.src = source;
                });

                downloadButtons.forEach((downloadButton) => {
                    downloadButton.addEventListener('click', async () => {
                        downloadButton.disabled = true;
                        message.textContent = '';

                        try {
                            const card = document.querySelector('.participant-id-card[data-card-side="front"]');
                            const backCard = document.querySelector('.participant-id-card[data-card-side="back"]');
                            const templateElement = card?.querySelector('.participant-id-card__template');
                            const backTemplateElement = backCard?.querySelector('.participant-id-card__template');
                            if (!card || !backCard || !templateElement || !backTemplateElement) {
                                throw new Error('The ID card could not be found.');
                            }

                            const scale = 3;
                            const cardWidth = 591 * scale;
                            const cardHeight = 1004 * scale;
                            const gap = 30 * scale;
                            const canvas = document.createElement('canvas');
                            canvas.width = cardWidth * 2 + gap;
                            canvas.height = cardHeight;
                            const context = canvas.getContext('2d');

                            if (!context) {
                                throw new Error('Your browser could not prepare the ID card image.');
                            }

                            context.fillStyle = '#ffffff';
                            context.fillRect(0, 0, canvas.width, canvas.height);

                            const [template, backTemplate] = await Promise.all([
                                loadImage(templateElement.src),
                                loadImage(backTemplateElement.src),
                            ]);
                            context.drawImage(template, 0, 0, cardWidth, cardHeight);
                            context.drawImage(backTemplate, cardWidth + gap, 0, cardWidth, cardHeight);

                            const cx = 294 * scale, cy = 300 * scale, radius = 131 * scale;
                            const photo = card.querySelector('.participant-id-card__photo');
                            context.save();
                            context.beginPath();
                            context.arc(cx, cy, radius, 0, Math.PI * 2);
                            context.clip();

                            if (photo instanceof HTMLImageElement) {
                                const participantPhoto = await loadImage(photo.src);
                                const size = radius * 2;
                                const imageScale = Math.max(size / participantPhoto.width, size / participantPhoto.height);
                                const w = participantPhoto.width * imageScale;
                                const h = participantPhoto.height * imageScale;
                                context.drawImage(participantPhoto, cx - w / 2, cy - h / 2, w, h);
                            } else {
                                context.fillStyle = '#e0f2fe';
                                context.fillRect(cx - radius, cy - radius, radius * 2, radius * 2);
                                context.fillStyle = '#123d70';
                                context.font = `800 ${106 * scale}px Arial`;
                                context.textAlign = 'center';
                                context.textBaseline = 'middle';
                                context.fillText(card.querySelector('.participant-id-card__initial')?.textContent?.trim() ?? '', cx, cy);
                            }
                            context.restore();

                            context.textBaseline = 'middle';
                            context.textAlign = 'center';
                            context.fillStyle = '#123d70';
                            context.font = `800 ${35 * scale}px Arial`;
                            context.fillText(card.querySelector('.participant-id-card__name')?.textContent?.trim() ?? '', 295 * scale, 475 * scale, 530 * scale);
                            context.fillStyle = '#274b73';
                            context.font = `700 ${24 * scale}px Arial`;
                            context.fillText(card.querySelector('.participant-id-card__designation')?.textContent?.trim() ?? '', 295 * scale, 520 * scale, 530 * scale);

                            context.textAlign = 'left';
                            context.fillStyle = '#172554';
                            context.font = `600 ${19 * scale}px Arial`;
                            [...card.querySelectorAll('.participant-id-card__detail')].forEach((detail, index) => {
                                context.fillText(detail.textContent.trim(), 145 * scale, (615 + index * 35.5) * scale, 420 * scale);
                            });

                            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
                            if (!blob) {
                                throw new Error('The ID card image could not be generated. Please try again.');
                            }

                            const downloadUrl = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = downloadUrl;
                            link.download = `${card.dataset.cardNumber}.png`;
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            window.setTimeout(() => URL.revokeObjectURL(downloadUrl), 1000);
                        } catch (error) {
                            console.error('ID card download failed:', error);
                            message.textContent = error instanceof Error ? error.message : 'The ID card could not be downloaded. Please try again.';
                        } finally {
                            downloadButton.disabled = false;
                        }
                    });
                });
            })();
        </script>
    <?php endif; ?>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>