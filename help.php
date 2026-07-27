<?php
// ============================================================
// FARMLEND - HELP.PHP
// ============================================================
// Public page — no login required.
// Provides step-by-step instructions on how to use FarmLend.
// Uses style.css classes: hero, page-header, section,
// help-step, help-step-number, help-step-content, alert, etc.
// ============================================================

include 'header.php';
?>

<div style="padding: 0 20px;">

<!-- Hero Banner -->
<section class="hero">
    <h1>&#x2753; Help &amp; User Guide</h1>
    <p>Everything you need to know about using FarmLend. Follow the step-by-step instructions below to get the most out of the platform.</p>
</section>

<!-- Quick Navigation -->
<div class="alert alert-info mb-30">
    &#x1F4D6; <strong>Jump to a section:</strong>
    <a href="#getting-started">Getting Started</a> &nbsp;|&nbsp;
    <a href="#renting-equipment">Renting Equipment</a> &nbsp;|&nbsp;
    <a href="#managing-rentals">Managing Your Rentals</a> &nbsp;|&nbsp;
    <a href="#admin-tasks">Administrator Tasks</a> &nbsp;|&nbsp;
    <a href="#troubleshooting">Troubleshooting &amp; FAQs</a>
</div>


<!-- ====================================================== -->
<!-- SECTION 1: GETTING STARTED                             -->
<!-- ====================================================== -->
<div class="section" id="getting-started">
    <div class="page-header">
        <h2>&#x1F680; Getting Started</h2>
        <p>New to FarmLend? Start here to set up your account and learn the basics.</p>
    </div>

    <!-- Step 1 -->
    <div class="help-step">
        <div class="help-step-number">1</div>
        <div class="help-step-content">
            <h3>How to Create an Account</h3>
            <p>Navigate to the <strong>Login</strong> page using the link in the top navigation bar. If you do not already have an account, look for the <strong>"Register"</strong> or <strong>"Sign Up"</strong> link below the login form. Fill in your desired username and password, then click <strong>Register</strong>. Once your account is created, you will be redirected to the login page where you can sign in immediately.</p>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="help-step">
        <div class="help-step-number">2</div>
        <div class="help-step-content">
            <h3>How to Log In</h3>
            <p>Click the <strong>Login</strong> button in the navigation bar. Enter the username and password you registered with, then click <strong>Log In</strong>. If your credentials are correct, you will be taken to the home page with full access to the catalog, booking system, and your rental history.</p>
        </div>
    </div>

    <!-- Step 3 -->
    <div class="help-step">
        <div class="help-step-number">3</div>
        <div class="help-step-content">
            <h3>How to Log Out</h3>
            <p>When you are finished, click the <strong>Logout</strong> button that appears in the navigation bar (it shows your username). You will be signed out securely and your session will be terminated. Always log out when using a shared or public computer.</p>
        </div>
    </div>
</div>

<hr class="divider">


<!-- ====================================================== -->
<!-- SECTION 2: RENTING EQUIPMENT                           -->
<!-- ====================================================== -->
<div class="section" id="renting-equipment">
    <div class="page-header">
        <h2>&#x1F69C; Renting Equipment</h2>
        <p>Learn how to browse the catalog, search for machinery, and make a booking.</p>
    </div>

    <!-- Step 4 -->
    <div class="help-step">
        <div class="help-step-number">4</div>
        <div class="help-step-content">
            <h3>How to Browse the Equipment Catalog</h3>
            <p>After logging in, click <strong>Catalog</strong> in the navigation bar. You will see a grid of available agricultural machinery, each displayed as a card with an image, name, category tag, description, and daily rental rate. Scroll through the catalog to explore what is available.</p>
        </div>
    </div>

    <!-- Step 5 -->
    <div class="help-step">
        <div class="help-step-number">5</div>
        <div class="help-step-content">
            <h3>How to Search and Filter Equipment</h3>
            <p>At the top of the Catalog page, you will find a <strong>search bar</strong>. Type a keyword (e.g., "Kubota" or "harvester") to search by name. You can also use the <strong>category dropdown</strong> to filter by equipment type, such as Tractors or Harvesters. Click <strong>Search</strong> to apply your filters. To reset, clear the search field and select "All Types."</p>
        </div>
    </div>

    <!-- Step 6 -->
    <div class="help-step">
        <div class="help-step-number">6</div>
        <div class="help-step-content">
            <h3>How to Book a Piece of Equipment</h3>
            <p>On any equipment card in the catalog, click the <strong>Book Now</strong> button. You will be taken to the booking page where the equipment details and daily rate are displayed. Select your <strong>Start Date</strong> and <strong>End Date</strong> using the date pickers. The system will automatically calculate the total cost. Click <strong>Confirm Booking</strong> to submit your reservation. Your booking will be set to <strong>Pending</strong> status until an administrator approves it.</p>
        </div>
    </div>

    <!-- Step 7 -->
    <div class="help-step">
        <div class="help-step-number">7</div>
        <div class="help-step-content">
            <h3>What If the Equipment Is Already Booked?</h3>
            <p>FarmLend includes <strong>conflict prevention</strong>. If the dates you selected overlap with an existing confirmed booking for the same equipment, the system will display an error message: <em>"This equipment is already booked for part of that date range."</em> Simply choose different dates and try again.</p>
        </div>
    </div>
</div>

<hr class="divider">


<!-- ====================================================== -->
<!-- SECTION 3: MANAGING YOUR RENTALS                       -->
<!-- ====================================================== -->
<div class="section" id="managing-rentals">
    <div class="page-header">
        <h2>&#x1F4CB; Managing Your Rentals</h2>
        <p>Keep track of all your active and past equipment reservations.</p>
    </div>

    <!-- Step 8 -->
    <div class="help-step">
        <div class="help-step-number">8</div>
        <div class="help-step-content">
            <h3>How to View Your Rental History</h3>
            <p>Click <strong>My Rentals</strong> in the navigation bar. This page shows a table of all your bookings, including the equipment name, rental dates, total cost, and current status. You can see which bookings are <strong>Pending</strong> (awaiting admin approval), <strong>Confirmed</strong> (approved and active), or <strong>Returned</strong> (completed).</p>
        </div>
    </div>

    <!-- Step 9 -->
    <div class="help-step">
        <div class="help-step-number">9</div>
        <div class="help-step-content">
            <h3>Understanding Booking Statuses</h3>
            <p>Every booking goes through a lifecycle:</p>
            <p>
                <strong>&#x1F7E1; Pending</strong> — Your booking has been submitted and is waiting for an administrator to review it.<br>
                <strong>&#x1F7E2; Confirmed</strong> — An administrator has approved your booking. The equipment is reserved for your selected dates.<br>
                <strong>&#x26AA; Returned</strong> — The rental period is over and the equipment has been returned.
            </p>
        </div>
    </div>
</div>

<hr class="divider">


<!-- ====================================================== -->
<!-- SECTION 4: ADMINISTRATOR TASKS                         -->
<!-- ====================================================== -->
<div class="section" id="admin-tasks">
    <div class="page-header">
        <h2>&#x1F6E0; Administrator Tasks</h2>
        <p>These instructions apply only to users with the <strong>Admin</strong> role.</p>
    </div>

    <div class="alert alert-warning mb-20">
        &#x26A0; The Admin Panel is restricted. Only users with an admin account can access these features. Regular farmers will not see the Admin Panel link in the navigation bar.
    </div>

    <!-- Step 10 -->
    <div class="help-step">
        <div class="help-step-number">10</div>
        <div class="help-step-content">
            <h3>How to Access the Admin Panel</h3>
            <p>If you are logged in as an administrator, you will see an <strong>Admin Panel</strong> link in the navigation bar. Click it to access the management dashboard. The admin panel is organized into tabs: <strong>Users</strong>, <strong>Equipment</strong>, <strong>Bookings</strong>, and <strong>Reports</strong>.</p>
        </div>
    </div>

    <!-- Step 11 -->
    <div class="help-step">
        <div class="help-step-number">11</div>
        <div class="help-step-content">
            <h3>How to Manage Users</h3>
            <p>In the <strong>Users</strong> tab, you can view a table of all registered accounts, including their username, role, and registration date. From here you can <strong>add new users</strong>, <strong>edit existing accounts</strong> (change usernames, passwords, or roles), or <strong>delete</strong> accounts that are no longer needed.</p>
        </div>
    </div>

    <!-- Step 12 -->
    <div class="help-step">
        <div class="help-step-number">12</div>
        <div class="help-step-content">
            <h3>How to Manage Equipment</h3>
            <p>In the <strong>Equipment</strong> tab, you can view all listed machinery. You can <strong>add new equipment</strong> by filling out a form with the name, type, description, daily rate, and an optional image. You can also <strong>edit</strong> or <strong>delete</strong> existing equipment entries. Changes are reflected immediately in the public catalog.</p>
        </div>
    </div>

    <!-- Step 13 -->
    <div class="help-step">
        <div class="help-step-number">13</div>
        <div class="help-step-content">
            <h3>How to Approve or Reject Bookings</h3>
            <p>In the <strong>Bookings</strong> tab, you will see a list of all reservations across the system. Bookings with a <strong>Pending</strong> status require your action. Click <strong>Approve</strong> to confirm the booking or <strong>Reject</strong> to decline it. The farmer who made the booking will see the updated status on their My Rentals page.</p>
        </div>
    </div>

    <!-- Step 14 -->
    <div class="help-step">
        <div class="help-step-number">14</div>
        <div class="help-step-content">
            <h3>How to View Reports</h3>
            <p>In the <strong>Reports</strong> tab, you can view automated system metrics such as <strong>total revenue generated</strong>, the <strong>most-rented equipment</strong>, and <strong>active user counts</strong>. Use these insights to make informed decisions about inventory management and pricing.</p>
        </div>
    </div>
</div>

<hr class="divider">


<!-- ====================================================== -->
<!-- SECTION 5: TROUBLESHOOTING & FAQs                      -->
<!-- ====================================================== -->
<div class="section" id="troubleshooting">
    <div class="page-header">
        <h2>&#x2753; Troubleshooting &amp; FAQs</h2>
        <p>Answers to common questions and issues.</p>
    </div>

    <div class="help-step">
        <div class="help-step-number">?</div>
        <div class="help-step-content">
            <h3>I forgot my password. What do I do?</h3>
            <p>Please contact your system administrator. They can reset your password from the Admin Panel under the Users tab.</p>
        </div>
    </div>

    <div class="help-step">
        <div class="help-step-number">?</div>
        <div class="help-step-content">
            <h3>My booking was rejected. Why?</h3>
            <p>Bookings may be rejected if the equipment is under maintenance, the requested dates conflict with another reservation, or the administrator has another reason. Try selecting different dates or a different piece of equipment, or contact the administrator for more details.</p>
        </div>
    </div>

    <div class="help-step">
        <div class="help-step-number">?</div>
        <div class="help-step-content">
            <h3>I cannot see the Admin Panel link.</h3>
            <p>The Admin Panel is only visible to users with the <strong>admin</strong> role. If you are a regular farmer, you will not have access to this section. Contact your system administrator if you believe your role is incorrect.</p>
        </div>
    </div>

    <div class="help-step">
        <div class="help-step-number">?</div>
        <div class="help-step-content">
            <h3>The catalog is empty. Where is the equipment?</h3>
            <p>Equipment must be added by an administrator through the Admin Panel. If no equipment has been listed yet, the catalog will appear empty. Contact your administrator to have equipment added to the system.</p>
        </div>
    </div>

    <div class="help-step">
        <div class="help-step-number">?</div>
        <div class="help-step-content">
            <h3>How is the rental cost calculated?</h3>
            <p>The total cost is calculated automatically: <strong>Number of rental days &times; Daily rate</strong>. For example, if a tractor costs Rs. 3,500 per day and you book it for 5 days, the total will be Rs. 17,500.00.</p>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="text-center mt-30 mb-30">
    <p class="text-muted">Still have questions? Check out our features page or log in to get started.</p>
    <a href="functionalities.php" class="btn btn-secondary btn-lg">View All Features</a>
    &nbsp;
    <a href="login.php" class="btn btn-primary btn-lg">Log In</a>
</div>

</div><!-- end .page-padding -->

<?php include 'footer.php'; ?>