<?php
// ============================================================
// FARMLEND - HEADER.PHP
// ============================================================
// Include this file at the top of EVERY page:
//     include 'header.php'; 
//
// This file provides:
//   - Session initialization
//   - The full HTML opening (DOCTYPE, <head>, meta tags, CSS)
//   - The navigation bar (session-aware, role-aware)
//   - The opening <main> content wrapper
//
// The matching closing tags are in footer.php.
// ============================================================

// Start the session ONLY if one has not already been started.
// This prevents "session already active" warnings if another 
// file (like a login handler) already called session_start().
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect the current page filename to highlight the active nav link.
$current_page = basename($_SERVER['PHP_SELF']);

// Read login status and role from the session.
$is_logged_in = isset($_SESSION['user_id']);
$user_role     = isset($_SESSION['role'])     ? $_SESSION['role']     : '';
$username      = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FarmLend - Online Agricultural Equipment Rental Management System">
    <title>FarmLend - Agricultural Equipment Rental</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="nav-container">

        <!-- Logo -->
        <a href="index.php" class="nav-logo">&#x1F33E; FarmLend</a>

        <!-- Mobile hamburger button -->
        <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigation links -->
        <ul class="nav-menu" id="navMenu">

            <!-- Home (visible to everyone) -->
            <li>
                <a href="index.php"
                   class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                   Home
                </a>
            </li>

            <?php if ($is_logged_in): ?>

                <!-- Catalog (logged-in users only) -->
                <li>
                    <a href="catalog.php"
                       class="<?php echo ($current_page === 'catalog.php') ? 'active' : ''; ?>">
                       Catalog
                    </a>
                </li>

                <!-- Book Equipment (logged-in users only) -->
                <li>
                    <a href="booking.php"
                       class="<?php echo ($current_page === 'booking.php') ? 'active' : ''; ?>">
                       Book Equipment
                    </a>
                </li>

                <!-- Rental History (logged-in users only) -->
                <li>
                    <a href="history.php"
                       class="<?php echo ($current_page === 'history.php') ? 'active' : ''; ?>">
                       My Rentals
                    </a>
                </li>

                <?php if ($user_role === 'admin'): ?>
                    <!-- Admin Panel (admins only) -->
                    <li>
                        <a href="admin.php"
                           class="<?php echo ($current_page === 'admin.php') ? 'active' : ''; ?>">
                           Admin Panel
                        </a>
                    </li>
                <?php endif; ?>

            <?php endif; ?>

            <!-- Features (visible to everyone) -->
            <li>
                <a href="functionalities.php"
                   class="<?php echo ($current_page === 'functionalities.php') ? 'active' : ''; ?>">
                   Features
                </a>
            </li>

            <!-- Help (visible to everyone) -->
            <li>
                <a href="help.php"
                   class="<?php echo ($current_page === 'help.php') ? 'active' : ''; ?>">
                   Help
                </a>
            </li>

            <?php if ($is_logged_in): ?>
                <!-- Logout (logged-in users only) -->
                <li>
                    <a href="logout.php" class="btn-logout">
                        Logout (<?php echo htmlspecialchars($username); ?>)
                    </a>
                </li>
            <?php else: ?>
                <!-- Login (guests only) -->
                <li>
                    <a href="login.php"
                       class="btn-nav-login <?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">
                       Login
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</nav>

<!-- Main content area (closed in footer.php) -->
<main class="content-wrapper">

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}
</script>
