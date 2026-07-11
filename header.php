<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FarmLend - Online Agricultural Equipment Rental Management System">
    <title>FarmLend - Agricultural Equipment Rental</title>
    <link rel="stylesheet" href="/FarmLend/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">&#x1F33E; FarmLend</a>
        <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu" id="navMenu">
            <li>
                <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">Home</a>
            </li>
            <?php if ($is_logged_in): ?>
                <li>
                    <a href="catalog.php" class="<?php echo ($current_page === 'catalog.php') ? 'active' : ''; ?>">Catalog</a>
                </li>
                <li>
                    <a href="booking.php" class="<?php echo ($current_page === 'booking.php') ? 'active' : ''; ?>">Book Equipment</a>
                </li>
                <li>
                    <a href="history.php" class="<?php echo ($current_page === 'history.php') ? 'active' : ''; ?>">My Rentals</a>
                </li>
                <?php if ($user_role === 'admin'): ?>
                    <li>
                        <a href="admin.php" class="<?php echo ($current_page === 'admin.php') ? 'active' : ''; ?>">Admin Panel</a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <li>
                <a href="functionalities.php" class="<?php echo ($current_page === 'functionalities.php') ? 'active' : ''; ?>">Features</a>
            </li>
            <li>
                <a href="help.php" class="<?php echo ($current_page === 'help.php') ? 'active' : ''; ?>">Help</a>
            </li>
            <?php if ($is_logged_in): ?>
                <li>
                    <a href="logout.php" class="btn-logout">Logout (<?php echo htmlspecialchars($username); ?>)</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" class="btn-nav-login <?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
