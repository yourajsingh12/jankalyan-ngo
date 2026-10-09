<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($email === '' || $password === '') {
    echo "<script>alert('Please enter email and password'); window.location='login.php';</script>";
    exit();
}

$staticEmail = 'admin@jankalyansanghfoundation.com';
$staticPassword = 'admin123';

if ($email === $staticEmail && $password === $staticPassword) {
    $_SESSION['admin_email'] = $email;
    header("Location: index.php");
    exit();
}

echo "<script>alert('Incorrect Email or Password'); window.location='login.php';</script>";
exit();
?>
