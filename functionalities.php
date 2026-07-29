<?php
// ============================================================
// FARMLEND - FUNCTIONALITIES.PHP
// ============================================================
// Public page — no login required.
// Lists all system features / capabilities of FarmLend.
// Uses style.css classes: hero, page-header, feature-list,
// feature-item, section, alert-info, btn, etc.
// ============================================================

include 'header.php';
?>

<div style="padding: 0 20px;">

<!-- Hero Banner -->
<section class="hero">
    <h1>&#x2699; System Functionalities</h1>
    <p>Discover everything FarmLend can do for you. Our platform is packed with powerful features designed to make agricultural equipment rental effortless.</p>
</section>

<!-- Info tip -->
<div class="alert alert-info mb-30">
    &#x1F4A1; FarmLend is a complete end-to-end equipment rental management system. Below you will find every feature the platform offers, from browsing machinery to generating revenue reports.
</div>

<!-- Feature List Grid -->
<div class="section">
    <div class="page-header">
        <h2>Core Features</h2>
        <p>The essential building blocks that power the FarmLend experience.</p>
    </div>

    <div class="feature-list">

        <!-- Feature 1 -->
        <div class="feature-item">
            <h3>&#x1F512; Secure Authentication</h3>
            <p>Role-based login system that correctly separates system administrators from ordinary farmers, complete with session management and secure logouts. Your account stays protected at every step.</p>
        </div>

        <!-- Feature 2 -->
        <div class="feature-item">
            <h3>&#x1F69C; Dynamic Equipment Catalog</h3>
            <p>A visual browsing interface allowing users to view available agricultural machinery, including images, descriptions, and daily rental rates. Find the right equipment at a glance.</p>
        </div>

        <!-- Feature 3 -->
        <div class="feature-item">
            <h3>&#x1F50D; Search &amp; Filtering</h3>
            <p>The ability to search the equipment inventory by keyword or filter items by specific categories such as Tractors, Harvesters, and more. Locate what you need in seconds.</p>
        </div>

        <!-- Feature 4 -->
        <div class="feature-item">
            <h3>&#x1F4C5; Smart Booking Engine</h3>
            <p>A reservation system that handles start and end date selection, automatically calculates the total rental cost based on the duration, and tracks booking statuses including Pending, Confirmed, and Returned.</p>
        </div>

        <!-- Feature 5 -->
        <div class="feature-item">
            <h3>&#x1F6AB; Conflict Prevention</h3>
            <p>Advanced overlap detection that prevents double-booking by ensuring a piece of equipment cannot be reserved if the requested dates intersect with an existing confirmed booking.</p>
        </div>

        <!-- Feature 6 -->
        <div class="feature-item">
            <h3>&#x1F4CB; Personalized Rental History</h3>
            <p>A dedicated dashboard for logged-in farmers to track their active rentals and view their past booking history. Stay on top of every reservation you have ever made.</p>
        </div>

    </div>
</div>

<hr class="divider">

<!-- Admin Features -->
<div class="section">
    <div class="page-header">
        <h2>Administration &amp; Reporting</h2>
        <p>Powerful tools reserved for system administrators to manage the platform.</p>
    </div>

    <div class="feature-list">

        <!-- Feature 7 -->
        <div class="feature-item">
            <h3>&#x1F6E0; Administrator Control Panel</h3>
            <p>A secure, restricted area where admins can execute full CRUD (Create, Read, Update, Delete) operations on user accounts and equipment records. Complete control at your fingertips.</p>
        </div>

        <!-- Feature 8 -->
        <div class="feature-item">
            <h3>&#x2705; Booking Management</h3>
            <p>Tools for administrators to review pending reservations and approve or reject them. Ensure every booking is verified before equipment leaves the yard.</p>
        </div>

        <!-- Feature 9 -->
        <div class="feature-item">
            <h3>&#x1F4CA; Statistical Reporting</h3>
            <p>Automated admin reports displaying system metrics, such as revenue generated, most-rented items, and active user counts. Make data-driven decisions with ease.</p>
        </div>

    </div>
</div>

<!-- CTA Section -->
<div class="text-center mt-30 mb-30">
    <p class="text-muted">Ready to experience these features first-hand?</p>
    <a href="login.php" class="btn btn-primary btn-lg">Log In to Get Started</a>
    &nbsp;
    <a href="help.php" class="btn btn-secondary btn-lg">Read the User Guide</a>
</div>

</div><!-- end .page-padding -->

<?php include 'footer.php'; ?>