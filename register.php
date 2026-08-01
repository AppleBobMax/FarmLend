<?php
// ============================================================
// FARMLEND - REGISTER.PHP  (NEW FILE)
// ------------------------------------------------------------
// Lets a new ordinary user (farmer) create an account.
// The password is stored as a bcrypt hash with password_hash().
// This is the sign-up flow referenced by the Help page and the
// project proposal. Authentication is Dilshani's slice, so she
// should review and own this file.
// ============================================================

session_start();
require_once 'db_connect.php';

// Logged-in users do not need to register.
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error     = '';
$full_name = '';
$username  = '';
$email     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'The two passwords do not match.';
    } else {
        // Make sure the username and email are not already taken.
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows > 0) {
            $error = 'That username or email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare(
                "INSERT INTO users (username, full_name, email, password_hash, role)
                 VALUES (?, ?, ?, ?, 'farmer')"
            );
            $insert->bind_param("ssss", $username, $full_name, $email, $hash);

            if ($insert->execute()) {
                $_SESSION['flash'] = 'Account created. You can now log in.';
                header("Location: login.php");
                exit();
            } else {
                $error = 'Something went wrong while creating your account. Please try again.';
            }
        }
    }
}

include 'header.php';
?>

<div class="login-wrapper">
    <div class="login-card">
        <h2>&#x1F33E; Create Your Account</h2>
        <span class="login-subtitle">Register to start renting equipment</span>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name"
                       value="<?php echo htmlspecialchars($full_name); ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="At least 6 characters" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password"
                       name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p class="text-center text-small mt-20">
            Already have an account? <a href="login.php">Log in here</a>.
        </p>
    </div>
</div>

<?php
$conn->close();
include 'footer.php';
?>