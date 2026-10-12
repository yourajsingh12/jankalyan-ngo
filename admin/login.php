<?php
session_start();

// If already logged in, go to index
if (isset($_SESSION['admin_email'])) {
    header("Location: index.php");
    exit();
}
?> 
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jan Kalyan Sangh Foundation | Admin Login</title>
    <link rel="icon" href="../assets/img/logo/study.png" type="image/png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #eef7f6 0%, #f4f8fb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #fff;
            padding: 32px 28px 24px;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
            width: 360px;
            border: 1px solid rgba(15, 118, 110, 0.12);
        }
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 18px;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
        }
        .login-brand img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
        }
        .login-brand span {
            display: block;
            font-size: 14px;
            line-height: 1.4;
        }
        h2 {
            text-align: center;
            margin: 18px 0 20px;
            color: #0f172a;
            font-size: 28px;
        }
        input {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px 14px;
            font-size: 14px;
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            box-sizing: border-box;
        }
        input:focus {
            outline: 2px solid rgba(15, 118, 110, 0.18);
            border-color: #0f766e;
        }
        button {
            width: 100%;
            padding: 12px 14px;
            background: linear-gradient(135deg, #0f766e, #0f172a);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        button:hover {
            opacity: 0.96;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-brand">
            <img src="../assets/img/logo/study.png" alt="Jan Kalyan Sangh Foundation logo">
            <span>Jan Kalyan Sangh Foundation</span>
        </div>
        <h2>Admin Login</h2>
        <form action="verify_login.php" method="POST">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
