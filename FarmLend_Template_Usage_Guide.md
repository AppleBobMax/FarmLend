# FarmLend Template Usage Guide

**For All Team Members, Group 87, CS2001, 2026**

This guide explains exactly how to use the three shared template files: `style.css`, `header.php`, and `footer.php`. Every page you build (login.php, catalog.php, booking.php, history.php, admin.php, functionalities.php, help.php) must follow the rules in this document.

---

## Part 1: What Each File Does

**style.css** is the single global stylesheet for the entire application. It defines every color, font size, button style, form style, table style, and layout rule used across all pages. You never write your own CSS. You only apply the classes already defined here.

**header.php** starts the session, checks who is logged in, and outputs the opening HTML structure (DOCTYPE, head, meta tags, the link to style.css), followed by the navigation bar. It also opens the `<main class="content-wrapper">` tag, which wraps your page content.

**footer.php** closes the `<main>` tag that header.php opened, renders the site footer, and closes the `</body>` and `</html>` tags.

---

## Part 2: The Golden Rule

**Do not edit style.css, header.php, or footer.php.** These three files belong to the shared foundation of the project. If every member starts editing them individually, the whole application will break for everyone else. If you need a new style that does not exist yet (for example, a new badge color), post a message in the group chat and ask the leader to add it. The leader will update the file once, and everyone pulls the change.

---

## Part 3: The Required Page Skeleton

Every single page you build must follow this exact structure. Nothing goes above the opening PHP block, and nothing goes below the footer include.

```php
<?php
// Step 1: Start the session (only needed if you check login status
// before header.php runs, see Part 7 for the full pattern)
session_start();

// Step 2: Load the database connection (only if your page uses the database)
require_once 'db_connect.php';

// Step 3: Load the shared header (navbar, opens <main>)
include 'header.php';
?>

<!-- ==================================================== -->
<!-- YOUR PAGE CONTENT GOES HERE, BETWEEN HEADER AND FOOTER -->
<!-- ==================================================== -->

<div class="page-header">
    <h1>Page Title</h1>
    <p>A short description of what this page does.</p>
</div>

<!-- your HTML, PHP, and database logic -->

<?php
// Step 4: Load the shared footer (closes </main>, adds footer)
include 'footer.php';
?>
```

Notice that your visible content sits between the `header.php` include and the `footer.php` include. Do not add your own `<html>`, `<head>`, `<body>`, `<nav>`, `<main>`, or `<footer>` tags. Those already exist inside header.php and footer.php.

---

## Part 4: How the Include Statements Work

`include 'header.php';` tells PHP to open header.php, run any PHP code inside it, and insert the resulting output directly into your page at that exact position, as if you had typed it there yourself. The same applies to `include 'footer.php';` at the bottom.

Both files must be in the same folder as your page (the main FarmLend project folder). Do not move header.php or footer.php into a subfolder, and do not rename them, or every page's include statement will fail with a "file not found" warning.

---

## Part 5: Session Variables You Can Use

After `include 'header.php';` runs, three variables become available to you for the rest of your page:

| Variable | Type | Meaning |
|----------|------|---------|
| `$is_logged_in` | true or false | Whether a user is currently logged in |
| `$user_role` | string | Either `'admin'` or `'user'`, empty string if not logged in |
| `$username` | string | The logged-in user's username, empty string if not logged in |

You can use these directly in your own page. For example:

```php
<?php if ($is_logged_in && $user_role === 'admin'): ?>
    <p>Welcome, administrator <?php echo htmlspecialchars($username); ?>.</p>
<?php endif; ?>
```

Always wrap any variable containing text that came from a user or the database in `htmlspecialchars()` before printing it. This prevents malicious code from being injected into your page.

Remember that these three variables come from `$_SESSION['user_id']`, `$_SESSION['role']`, and `$_SESSION['username']`. When Dilshani builds login.php, her code must set exactly these three session keys after a successful login, using these exact names, or the header will not detect the logged-in state correctly.

---

## Part 6: The Complete style.css Class Reference

Below is every class you are allowed to use, grouped by purpose, with a short usage example for each group.

### 6.1 Layout Classes

```html
<div class="page-header">
    <h1>Equipment Catalog</h1>
    <p>Browse available agricultural machinery.</p>
</div>

<div class="section">
    <!-- groups a block of related content with spacing below it -->
</div>

<div class="container-narrow">
    <!-- centers content and limits width to 600px, good for single forms -->
</div>
```

### 6.2 Buttons

```html
<button class="btn btn-primary">Book Now</button>
<button class="btn btn-secondary">Cancel</button>
<button class="btn btn-danger">Delete</button>
<button class="btn btn-warning">Reject</button>
<button class="btn btn-info">View Details</button>

<!-- Size modifiers, add alongside a color class -->
<button class="btn btn-primary btn-sm">Small Button</button>
<button class="btn btn-primary btn-lg">Large Button</button>

<!-- Full width button, useful in forms -->
<button class="btn btn-primary btn-block">Submit</button>
```

Use `.btn-primary` for the main positive action on a page (Book, Save, Submit, Approve). Use `.btn-secondary` for a less important action (Cancel, Back, View). Use `.btn-danger` for destructive actions (Delete, Reject, Cancel Booking). Use `.btn-warning` for cautionary actions. Use `.btn-info` for informational actions.

### 6.3 Forms

```html
<div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username">
</div>

<div class="form-group">
    <label for="equipment_type">Equipment Type</label>
    <select class="form-control" id="equipment_type" name="equipment_type">
        <option value="">All Types</option>
        <option value="Tractor">Tractor</option>
        <option value="Harvester">Harvester</option>
    </select>
</div>

<div class="form-group">
    <label for="notes">Notes</label>
    <textarea class="form-control" id="notes" name="notes"></textarea>
</div>

<!-- Two fields side by side, used for start date and end date -->
<div class="form-row">
    <div class="form-group">
        <label for="start_date">Start Date</label>
        <input type="date" class="form-control" id="start_date" name="start_date">
    </div>
    <div class="form-group">
        <label for="end_date">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date">
    </div>
</div>

<!-- Error state, add is-invalid when validation fails -->
<input type="text" class="form-control is-invalid" name="field">
<span class="form-error">This field is required.</span>

<!-- Hint text below a field -->
<span class="form-hint">Enter dates in YYYY-MM-DD format.</span>
```

### 6.4 Cards (for the Equipment Catalog)

```html
<div class="card-grid">
    <div class="card">
        <div class="card-img-placeholder">&#x1F69C;</div>
        <div class="card-body">
            <span class="tag">Tractor</span>
            <h3>Kubota L3901 Compact Tractor</h3>
            <p>4WD compact tractor ideal for paddy fields.</p>
        </div>
        <div class="card-footer">
            <span class="price">Rs. 3,500 <span class="price-unit">/ day</span></span>
            <a href="booking.php?id=1" class="btn btn-primary btn-sm">Book Now</a>
        </div>
    </div>
    <!-- repeat .card for each piece of equipment -->
</div>
```

If you have an actual photo for a piece of equipment, replace `.card-img-placeholder` with an actual image tag using the `.card-img` class:

```html
<img src="img/tractor-kubota.jpg" class="card-img" alt="Kubota L3901 Tractor">
```

### 6.5 Tables (for Bookings, Users, Equipment Lists)

```html
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Equipment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#1024</td>
                <td>Kubota L3901 Tractor</td>
                <td><span class="badge badge-confirmed">Confirmed</span></td>
                <td><button class="btn btn-secondary btn-sm">View</button></td>
            </tr>
        </tbody>
    </table>
</div>
```

Always wrap your `<table>` inside a `<div class="table-wrapper">`. This allows the table to scroll horizontally on small phone screens instead of breaking the page layout.

### 6.6 Alerts (Feedback Messages)

```html
<div class="alert alert-success">Booking confirmed successfully.</div>
<div class="alert alert-danger">Login failed. Please check your credentials.</div>
<div class="alert alert-warning">This equipment has limited availability.</div>
<div class="alert alert-info">Tip: You can filter equipment by type.</div>
```

Use these right after a form submission to tell the user what happened. Place the alert at the top of your page content, right after `<div class="page-header">`.

### 6.7 Badges (Status Labels)

```html
<span class="badge badge-pending">Pending</span>
<span class="badge badge-confirmed">Confirmed</span>
<span class="badge badge-returned">Returned</span>
<span class="badge badge-admin">Admin</span>
<span class="badge badge-user">User</span>
```

The three booking badges (`badge-pending`, `badge-confirmed`, `badge-returned`) match the exact three values allowed in the `status` column of the bookings table. The two role badges (`badge-admin`, `badge-user`) match the exact two values allowed in the `role` column of the users table.

### 6.8 Stat Cards (Dashboards)

```html
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">124</div>
        <div class="stat-label">Active Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">38</div>
        <div class="stat-label">Equipment Listed</div>
    </div>
</div>
```

### 6.9 Hero Banner (Guest Home Page)

```html
<section class="hero">
    <h1>Welcome to FarmLend</h1>
    <p>Connecting farmers with agricultural machinery.</p>
    <a href="login.php" class="btn">Log In to Get Started</a>
</section>
```

### 6.10 Login Card

```html
<div class="login-card">
    <h2>FarmLend</h2>
    <span class="login-subtitle">Sign in to manage your equipment rentals</span>

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <button type="submit" class="btn btn-primary btn-block">Log In</button>
</div>
```

### 6.11 Admin Panel Tabs

```html
<div class="admin-nav">
    <a href="admin.php?tab=users" class="active">Users</a>
    <a href="admin.php?tab=equipment">Equipment</a>
    <a href="admin.php?tab=bookings">Bookings</a>
    <a href="admin.php?tab=reports">Reports</a>
</div>
```

Add the `active` class to whichever tab link matches the current page section, similar to how the navbar highlights the active page.

### 6.12 Search and Filter Bar

```html
<form class="search-bar" method="get" action="catalog.php">
    <input type="text" class="form-control" name="search" placeholder="Search equipment by name...">
    <select class="form-control" name="type" style="max-width: 200px;">
        <option value="">All Types</option>
        <option value="Tractor">Tractor</option>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
</form>
```

### 6.13 Help Page Steps

```html
<div class="help-step">
    <div class="help-step-number">1</div>
    <div class="help-step-content">
        <h3>Log In to Your Account</h3>
        <p>Visit the login page and enter your username and password.</p>
    </div>
</div>
```

Increase the number inside `.help-step-number` for each subsequent step (2, 3, 4, and so on).

### 6.14 Functionalities Page Feature List

```html
<div class="feature-list">
    <div class="feature-item">
        <h3>Secure Authentication</h3>
        <p>Role-based login separating administrators and farmers.</p>
    </div>
    <div class="feature-item">
        <h3>Equipment Catalog</h3>
        <p>Browse and search available agricultural machinery.</p>
    </div>
</div>
```

### 6.15 Availability Indicator

```html
<span class="availability availability-available">
    <span class="availability-dot"></span> Available
</span>

<span class="availability availability-booked">
    <span class="availability-dot"></span> Booked until Jul 18
</span>
```

### 6.16 Empty State (No Data to Show)

```html
<div class="empty-state">
    <div class="empty-state-icon">&#x1F4CB;</div>
    <h3>No Bookings Yet</h3>
    <p>You have not made any equipment reservations.</p>
    <a href="catalog.php" class="btn btn-primary">Browse Catalog</a>
</div>
```

Use this pattern whenever a database query returns zero rows, instead of showing a blank page.

### 6.17 Utility Classes

These small classes add quick spacing or alignment without writing new CSS.

| Class | Effect |
|-------|--------|
| `.mt-10`, `.mt-20`, `.mt-30` | Adds margin above the element (10px, 20px, 30px) |
| `.mb-10`, `.mb-20`, `.mb-30` | Adds margin below the element |
| `.flex`, `.flex-between`, `.flex-center` | Arranges child elements horizontally |
| `.gap-10`, `.gap-20` | Adds spacing between flex children |
| `.divider` | A thin horizontal line to separate sections |
| `.text-center`, `.text-muted`, `.text-small` | Text alignment and coloring helpers |

Example:

```html
<div class="flex-between mb-20">
    <h2>All Users</h2>
    <button class="btn btn-primary btn-sm">Add New User</button>
</div>
```

---

## Part 7: Page Patterns Based on Access Level

Different pages in FarmLend need different levels of protection. Use the correct pattern below depending on your assigned page.

### Pattern A: Public Page (functionalities.php, help.php)

No login required. Anyone can view these pages.

```php
<?php
require_once 'db_connect.php'; // only if you need the database
include 'header.php';
?>

<!-- your content -->

<?php include 'footer.php'; ?>
```

### Pattern B: Protected Page (catalog.php, booking.php, history.php)

Requires the user to be logged in. Guests are redirected to login.php.

```php
<?php
session_start();
require_once 'db_connect.php';

// Block guests before any HTML is generated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'header.php';
?>

<!-- your content -->

<?php include 'footer.php'; ?>
```

### Pattern C: Admin-Only Page (admin.php)

Requires the user to be logged in AND have the admin role.

```php
<?php
session_start();
require_once 'db_connect.php';

// Block guests and ordinary users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'header.php';
?>

<!-- your content -->

<?php include 'footer.php'; ?>
```

Notice that in Patterns B and C, `session_start()` is called manually at the very top, before the access check. This is required because you need to read `$_SESSION` and potentially redirect the user before any HTML is sent to the browser. Once your check passes, `include 'header.php';` runs safely. Header.php checks whether a session already exists before trying to start one, so there is no conflict.

The `exit;` statement after `header('Location: login.php');` is required every time. Without it, PHP will continue running the rest of your page even after telling the browser to redirect, which can cause partial page content to load before the redirect happens.

---

## Part 8: Common Mistakes to Avoid

1. **Adding inline styles.** Never write `style="color: red;"` directly in your HTML. Use the classes listed in Part 6 instead. If nothing fits, ask the leader to add a new class to style.css.

2. **Editing header.php, footer.php, or style.css.** These are shared files. Changes you make will be overwritten or will conflict with everyone else's work.

3. **Forgetting htmlspecialchars().** Any time you print a variable that came from a database or a form, wrap it: `<?php echo htmlspecialchars($variable); ?>`. This protects the application from malicious script injection.

4. **Printing HTML before checking session or redirecting.** If you call `header('Location: ...')` after any HTML, whitespace, or `include 'header.php';` has already been output, PHP will throw a "headers already sent" error. Always do your access checks first, before including header.php.

5. **Adding a space or blank line before the opening `<?php` tag.** Even a single space before your first `<?php` tag counts as output and can trigger the same "headers already sent" error. Your file must start with `<?php` on the very first line, very first character.

6. **Forgetting the exit statement after a redirect.** Always follow `header('Location: ...');` with `exit;` on the next line.

7. **Manually adding your own `<main>`, `<nav>`, or `<footer>` tags.** These already exist inside header.php and footer.php. Adding your own creates duplicate, broken HTML.

8. **Hardcoding table or column names incorrectly.** Always match the exact schema: `users`, `equipment`, `bookings` tables with the exact column names given in the project instructions.

---

## Part 9: A Complete Worked Example

Here is a minimal, fully correct example of a protected page from start to finish, showing every rule in this guide applied together.

```php
<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'header.php';

// Fetch all equipment from the database
$sql = "SELECT id, name, type, daily_rate, description FROM equipment";
$result = $conn->query($sql);
?>

<div class="page-header">
    <h1>Equipment Catalog</h1>
    <p>Browse available agricultural machinery for rent.</p>
</div>

<div class="card-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <div class="card-img-placeholder">&#x1F69C;</div>
            <div class="card-body">
                <span class="tag"><?php echo htmlspecialchars($row['type']); ?></span>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <div class="card-footer">
                <span class="price">
                    Rs. <?php echo number_format($row['daily_rate'], 2); ?>
                    <span class="price-unit">/ day</span>
                </span>
                <a href="booking.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
$conn->close();
include 'footer.php';
?>
```

Study this example carefully. It shows the correct order of operations: session check first, database connection second, header include third, then your content, then the footer include last. Every dynamic value is escaped with `htmlspecialchars()`, every class is taken directly from Part 6, and no custom CSS or inline styles appear anywhere.

---

Keep this guide open in a separate tab while you build your assigned page. If you are unsure whether a class exists for something you need, check Part 6 first before writing new CSS.
