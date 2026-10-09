<?php
session_start();

$requiredFields = [
    'full_name', 'mobile', 'email', 'password', 'confirm_password',
    'dob_day', 'dob_month', 'dob_year', 'gender', 'marital_status',
    'guardian_name', 'aadhar', 'category', 'occupation', 'address',
    'pincode', 'state', 'district', 'designation'
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
        header('Location: participate.php?error=1');
        exit();
    }
}

if ($_POST['password'] !== $_POST['confirm_password']) {
    header('Location: participate.php?error=2');
    exit();
}

if (strlen($_POST['password']) < 6) {
    header('Location: participate.php?error=3');
    exit();
}

$dob = $_POST['dob_year'] . '-' . $_POST['dob_month'] . '-' . $_POST['dob_day'];

$_SESSION['participate_form'] = [
    'full_name' => $_POST['full_name'],
    'mobile' => $_POST['mobile'],
    'email' => $_POST['email'],
    'dob' => $dob,
    'gender' => $_POST['gender'],
    'marital_status' => $_POST['marital_status'],
    'guardian_name' => $_POST['guardian_name'],
    'aadhar' => $_POST['aadhar'],
    'category' => $_POST['category'],
    'blood_group' => $_POST['blood_group'] ?? '',
    'occupation' => $_POST['occupation'],
    'address' => $_POST['address'],
    'pincode' => $_POST['pincode'],
    'state' => $_POST['state'],
    'district' => $_POST['district'],
    'designation' => $_POST['designation'],
    'submitted_at' => date('Y-m-d H:i:s')
];

header('Location: participate.php?success=1');
exit();
