<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('participate.php');
}

require_csrf();

$fields = [
    'full_name', 'mobile', 'email', 'password', 'confirm_password',
    'dob_day', 'dob_month', 'dob_year', 'gender', 'marital_status',
    'guardian_name', 'aadhar', 'category', 'blood_group', 'occupation',
    'address', 'pincode', 'state', 'district', 'designation',
];
$form = [];
foreach ($fields as $field) {
    $form[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
}

$genderOptions = ['Male', 'Female', 'Transgender', 'Other'];
$maritalOptions = ['Single', 'Married', 'Widowed', 'Divorced'];
$categoryOptions = ['General', 'OBC', 'SC', 'ST', 'Minority', 'Other'];
$bloodGroupOptions = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$occupationOptions = ['Student', 'Private Job', 'Government Job', 'Business', 'Farmer', 'Self Employed', 'Homemaker', 'Retired', 'Other'];
$stateOptions = ['Delhi', 'Haryana', 'Uttar Pradesh', 'Punjab', 'Rajasthan', 'Other'];
$districtOptions = ['Central Delhi', 'East Delhi', 'West Delhi', 'North Delhi', 'South Delhi', 'Other'];
$designationOptions = ['Member', 'Volunteer', 'Coordinator', 'District Lead', 'State Manager', 'Advisor', 'Supporter'];

foreach (array_diff($fields, ['blood_group']) as $field) {
    if ($form[$field] === '') {
        redirect('participate.php?error=1');
    }
}

if (
    mb_strlen($form['full_name']) > 255
    || preg_match('/^[0-9]{10}$/', $form['mobile']) !== 1
    || mb_strlen($form['email']) > 190
    || filter_var($form['email'], FILTER_VALIDATE_EMAIL) === false
    || mb_strlen($form['guardian_name']) > 255
    || preg_match('/^[0-9]{12}$/', $form['aadhar']) !== 1
    || mb_strlen($form['address']) > 2000
    || preg_match('/^[0-9]{6}$/', $form['pincode']) !== 1
    || !in_array($form['gender'], $genderOptions, true)
    || !in_array($form['marital_status'], $maritalOptions, true)
    || !in_array($form['category'], $categoryOptions, true)
    || ($form['blood_group'] !== '' && !in_array($form['blood_group'], $bloodGroupOptions, true))
    || !in_array($form['occupation'], $occupationOptions, true)
    || !in_array($form['state'], $stateOptions, true)
    || !in_array($form['district'], $districtOptions, true)
    || !in_array($form['designation'], $designationOptions, true)
    || ($_POST['terms'] ?? '') !== 'on'
) {
    redirect('participate.php?error=4');
}

if ($form['password'] !== $form['confirm_password']) {
    redirect('participate.php?error=2');
}
if (mb_strlen($form['password']) < 6 || mb_strlen($form['password']) > 255) {
    redirect('participate.php?error=3');
}

if (
    preg_match('/^[0-9]{4}$/', $form['dob_year']) !== 1
    || preg_match('/^[0-9]{1,2}$/', $form['dob_month']) !== 1
    || preg_match('/^[0-9]{1,2}$/', $form['dob_day']) !== 1
    || !checkdate((int) $form['dob_month'], (int) $form['dob_day'], (int) $form['dob_year'])
    || (int) $form['dob_year'] < 1950
    || (int) $form['dob_year'] > min(2008, (int) date('Y'))
    || sprintf('%04d-%02d-%02d', (int) $form['dob_year'], (int) $form['dob_month'], (int) $form['dob_day']) > date('Y-m-d')
) {
    redirect('participate.php?error=4');
}

try {
    ensure_participants_table();
} catch (PDOException $exception) {
    error_log('Participant table setup failed: ' . $exception->getMessage());
    redirect('participate.php?error=6');
}

try {
    $profilePhoto = upload_image(is_array($_FILES['profile_photo'] ?? null) ? $_FILES['profile_photo'] : null, 'participants');
} catch (RuntimeException $exception) {
    redirect('participate.php?error=5');
}

try {
    q(
        'INSERT INTO participants (
            full_name, mobile, email, password_hash, dob, gender, marital_status,
            guardian_name, aadhar, category, blood_group, occupation, address,
            pincode, state, district, designation, profile_photo, status
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)',
        [
            $form['full_name'],
            $form['mobile'],
            $form['email'],
            password_hash($form['password'], PASSWORD_DEFAULT),
            sprintf('%04d-%02d-%02d', (int) $form['dob_year'], (int) $form['dob_month'], (int) $form['dob_day']),
            $form['gender'],
            $form['marital_status'],
            $form['guardian_name'],
            $form['aadhar'],
            $form['category'],
            $form['blood_group'] !== '' ? $form['blood_group'] : null,
            $form['occupation'],
            $form['address'],
            $form['pincode'],
            $form['state'],
            $form['district'],
            $form['designation'],
            $profilePhoto,
        ]
    );
} catch (PDOException $exception) {
    if ($profilePhoto !== null) {
        delete_upload($profilePhoto);
    }
    error_log('Participant registration failed: ' . $exception->getMessage());
    redirect('participate.php?error=6');
}

redirect('participate.php?success=1');
