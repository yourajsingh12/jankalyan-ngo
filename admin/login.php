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
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            width: 320px;
        }
        input {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #0078D7;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background: #005cbf;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center;">Admin Login</h2>
        <form action="verify_login.php" method="POST">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
