<?php

session_start();
require_once 'db_connect.php';

// If already logged in, go to the home page.
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Show a one-time success message passed from register.php.
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both your username and password.';
    } else {
        // Look the user up by username.
        $stmt = $conn->prepare(
            "SELECT id, username, full_name, password_hash, role
             FROM users
             WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the entered password against the stored bcrypt hash.
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = 'Incorrect username or password.';
            }
        } else {
            // Same message whether the user is missing or the password is
            // wrong, so we do not reveal which usernames exist.
            $error = 'Incorrect username or password.';
        }
    }
}

include 'header.php';
?>

<div class="login-wrapper">
    <div class="login-card">
        <h2>&#x1F33E; FarmLend Login</h2>
        <span class="login-subtitle">Sign in to manage your equipment rentals</span>

        <?php if ($flash !== ''): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       placeholder="e.g. uoc" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>

        <p class="text-center text-small mt-20">
            Don't have an account? <a href="register.php">Register here</a>.
        </p>
    </div>
</div>

<?php
$conn->close();
include 'footer.php';
?>