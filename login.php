<?php
session_start();
require_once 'db_connect.php';

$error = '';

// If already logged in, redirect to index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        
        // Default Spec user validation (uoc / uoc)
        if ($username === 'uoc' && $password === 'uoc') {
            $_SESSION['user_id'] = 999;
            $_SESSION['username'] = 'uoc';
            $_SESSION['role'] = 'user'; // Ordinary user
            header("Location: index.php");
            exit();
        }

        // Database Verification for other users/admins
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Password verification (Plain text or Hashed)
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // 'admin' or 'user'

                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error =  "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmLend - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 50%, #52b788 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-header h1 {
            color: #1b4332;
            font-size: 28px;
            font-weight: 700;
        }

        .brand-header p {
            color: #555;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d6a4f;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #52b788;
            outline: none;
            box-shadow: 0 0 8px rgba(82, 183, 136, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #2d6a4f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #1b4332;
        }

        .alert-error {
            background-color: #ffe5e5;
            color: #d90429;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffb3b3;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <h1>🌾 FarmLend</h1>
        <p>Agricultural Equipment Rental System</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn-submit">Login</button>
    </form>
</div>

</body>
</html>