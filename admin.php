<?php
// ============================================================
// FARMLEND - ADMIN.PHP
// ============================================================
// The admin control panel. This is the ONLY place where admin
// or owner accounts are created and where equipment is added,
// so it acts as the curator of the whole catalogue.
//
// The page is split into four tabs, selected with a ?tab= value
// in the URL (users, equipment, bookings, reports). Only the
// active tab is rendered on each load.
//
// Every action (add, edit, delete, approve, reject, complete)
// follows the Post / Redirect / Get pattern: the POST is
// processed, a one-time message is stored in $_SESSION['flash'],
// then the browser is redirected back with header('Location: ...').
// This is the same pattern used in history.php.
//
// Security notes:
//   - Access control runs before any output, so redirects work.
//   - Every query that takes input uses an object-oriented mysqli
//     prepared statement with bind_param.
//   - Passwords are stored as bcrypt hashes (password_hash),
//     never as plain text.
//   - All output is escaped with htmlspecialchars().
// ============================================================

session_start();
require_once 'db_connect.php';

// 1. ACCESS CONTROL (the gatekeeper, runs before any HTML)
// Block anyone who is not logged in, and block any logged-in
// user whose role is not 'admin'. Both cases go to the login page.
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

$admin_id = (int)$_SESSION['user_id'];

// ------------------------------------------------------------
// Helper functions
// ------------------------------------------------------------

// Store a flash message and redirect back to a tab, then stop.
// $edit lets an edit form re-open on validation errors so the
// admin returns to the record they were working on.
function redirect_admin(string $tab, string $message, string $type = 'success', int $edit = 0) {
    $_SESSION['flash']      = $message;
    $_SESSION['flash_type'] = $type; // success, danger, or info
    $url = 'admin.php?tab=' . urlencode($tab);
    if ($edit > 0) {
        $url .= '&edit=' . (int)$edit;
    }
    header('Location: ' . $url);
    exit();
}

// Turn a user role into a coloured badge, reusing existing classes.
function role_badge(string $role) {
    $map = [
        'admin'  => ['badge-admin',    'Admin'],
        'owner'  => ['badge-approved', 'Owner'],
        'farmer' => ['badge-user',     'Farmer'],
    ];
    if (isset($map[$role])) {
        return '<span class="badge ' . $map[$role][0] . '">' . $map[$role][1] . '</span>';
    }
    return '<span class="badge">' . htmlspecialchars(ucfirst($role)) . '</span>';
}

// Turn an equipment status into a coloured badge.
function equipment_status_badge(string $status) {
    $map = [
        'available'   => ['badge-approved',  'Available'],
        'rented'      => ['badge-pending',   'Rented'],
        'maintenance' => ['badge-cancelled', 'Maintenance'],
    ];
    if (isset($map[$status])) {
        return '<span class="badge ' . $map[$status][0] . '">' . $map[$status][1] . '</span>';
    }
    return '<span class="badge">' . htmlspecialchars(ucfirst($status)) . '</span>';
}

// Turn a booking status into a coloured badge (same mapping as history.php).
function booking_status_badge(string $status) {
    $map = [
        'pending'   => ['badge-pending',   'Pending'],
        'approved'  => ['badge-approved',  'Approved'],
        'completed' => ['badge-completed', 'Completed'],
        'cancelled' => ['badge-cancelled', 'Cancelled'],
    ];
    if (isset($map[$status])) {
        return '<span class="badge ' . $map[$status][0] . '">' . $map[$status][1] . '</span>';
    }
    return '<span class="badge">' . htmlspecialchars(ucfirst($status)) . '</span>';
}

// 2. POST HANDLING (all actions, each ends in a redirect)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ============ USERS ============

    if ($action === 'add_user') {
        $username    = trim($_POST['username'] ?? '');
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? '';
        $role        = $_POST['role'] ?? '';
        $valid_roles = ['admin', 'farmer', 'owner'];

        if ($username === '' || $full_name === '' || $email === '' || $password === '' || $role === '') {
            redirect_admin('users', 'Please fill in every field to add a user.', 'danger');
        }
        if (!in_array($role, $valid_roles, true)) {
            redirect_admin('users', 'That role is not valid.', 'danger');
        }
        if (strlen($password) < 6) {
            redirect_admin('users', 'The password must be at least 6 characters long.', 'danger');
        }

        // Username and email must both be unique.
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            redirect_admin('users', 'That username or email is already in use.', 'danger');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare(
            "INSERT INTO users (username, full_name, email, password_hash, role)
             VALUES (?, ?, ?, ?, ?)"
        );
        $insert->bind_param("sssss", $username, $full_name, $email, $hash, $role);

        if ($insert->execute()) {
            redirect_admin('users', 'New user "' . $username . '" was added.', 'success');
        }
        redirect_admin('users', 'The user could not be added. Please try again.', 'danger');
    }

    elseif ($action === 'edit_user') {
        $uid         = (int)($_POST['user_id'] ?? 0);
        $username    = trim($_POST['username'] ?? '');
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? ''; // optional on edit
        $role        = $_POST['role'] ?? '';
        $valid_roles = ['admin', 'farmer', 'owner'];

        if ($uid <= 0) {
            redirect_admin('users', 'That user could not be found.', 'danger');
        }
        if ($username === '' || $full_name === '' || $email === '' || $role === '') {
            redirect_admin('users', 'Username, full name, email and role are all required.', 'danger', $uid);
        }
        if (!in_array($role, $valid_roles, true)) {
            redirect_admin('users', 'That role is not valid.', 'danger', $uid);
        }

        // Read the current role so we can protect the last admin.
        $cur = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $cur->bind_param("i", $uid);
        $cur->execute();
        $cur_row = $cur->get_result()->fetch_assoc();
        if (!$cur_row) {
            redirect_admin('users', 'That user could not be found.', 'danger');
        }

        // Do not let the last remaining admin be demoted (that would lock everyone out).
        if ($cur_row['role'] === 'admin' && $role !== 'admin') {
            $admin_count = (int)$conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'")->fetch_assoc()['c'];
            if ($admin_count <= 1) {
                redirect_admin('users', 'You cannot change the role of the last remaining admin.', 'danger', $uid);
            }
        }

        // Username and email must stay unique, ignoring this same account.
        $check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $check->bind_param("ssi", $username, $email, $uid);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            redirect_admin('users', 'Another account already uses that username or email.', 'danger', $uid);
        }

        // Only re-hash the password if a new one was actually typed.
        if ($password !== '') {
            if (strlen($password) < 6) {
                redirect_admin('users', 'The new password must be at least 6 characters long.', 'danger', $uid);
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare(
                "UPDATE users
                 SET username = ?, full_name = ?, email = ?, role = ?, password_hash = ?
                 WHERE id = ?"
            );
            $update->bind_param("sssssi", $username, $full_name, $email, $role, $hash, $uid);
        } else {
            $update = $conn->prepare(
                "UPDATE users
                 SET username = ?, full_name = ?, email = ?, role = ?
                 WHERE id = ?"
            );
            $update->bind_param("ssssi", $username, $full_name, $email, $role, $uid);
        }

        if ($update->execute()) {
            // If the admin edited their own row, refresh the session values.
            // (If they demoted themselves, which is only allowed when another
            //  admin exists, the next page load will correctly bounce them out.)
            if ($uid === $admin_id) {
                $_SESSION['username']  = $username;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['role']      = $role;
            }
            redirect_admin('users', 'The user was updated.', 'success');
        }
        redirect_admin('users', 'The user could not be updated.', 'danger', $uid);
    }

    elseif ($action === 'delete_user') {
        $uid = (int)($_POST['user_id'] ?? 0);

        if ($uid <= 0) {
            redirect_admin('users', 'That user could not be found.', 'danger');
        }
        // You cannot delete your own account.
        if ($uid === $admin_id) {
            redirect_admin('users', 'You cannot delete your own account.', 'danger');
        }

        // You cannot delete the last remaining admin.
        $role_stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $role_stmt->bind_param("i", $uid);
        $role_stmt->execute();
        $target = $role_stmt->get_result()->fetch_assoc();
        if (!$target) {
            redirect_admin('users', 'That user could not be found.', 'danger');
        }
        if ($target['role'] === 'admin') {
            $admin_count = (int)$conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'")->fetch_assoc()['c'];
            if ($admin_count <= 1) {
                redirect_admin('users', 'You cannot delete the last remaining admin account.', 'danger');
            }
        }

        // Note: the schema deletes this user's equipment and bookings automatically
        // (ON DELETE CASCADE), so the confirm dialog warns about that.
        $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete->bind_param("i", $uid);
        if ($delete->execute() && $delete->affected_rows > 0) {
            redirect_admin('users', 'The user was deleted.', 'success');
        }
        redirect_admin('users', 'The user could not be deleted.', 'danger');
    }

    // EQUIPMENT 

    elseif ($action === 'add_equipment') {
        $name         = trim($_POST['name'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $daily_rate   = $_POST['daily_rate'] ?? '';
        $status       = $_POST['status'] ?? '';
        $image_url    = trim($_POST['image_url'] ?? '');
        // An empty category means "Uncategorized", stored as SQL NULL.
        $category_id  = (($_POST['category_id'] ?? '') !== '') ? (int)$_POST['category_id'] : null;
        $valid_status = ['available', 'rented', 'maintenance'];

        if ($name === '' || $description === '' || $daily_rate === '' || $status === '') {
            redirect_admin('equipment', 'Please fill in the name, description, daily rate and status.', 'danger');
        }
        if (!is_numeric($daily_rate) || (float)$daily_rate < 0) {
            redirect_admin('equipment', 'The daily rate must be a number of zero or more.', 'danger');
        }
        if (!in_array($status, $valid_status, true)) {
            redirect_admin('equipment', 'That status is not valid.', 'danger');
        }
        $rate = (float)$daily_rate;

        // owner_id defaults to the logged-in admin.
        $insert = $conn->prepare(
            "INSERT INTO equipment (owner_id, category_id, name, description, daily_rate, status, image_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->bind_param("iissdss", $admin_id, $category_id, $name, $description, $rate, $status, $image_url);

        if ($insert->execute()) {
            redirect_admin('equipment', 'New equipment "' . $name . '" was added.', 'success');
        }
        redirect_admin('equipment', 'The equipment could not be added.', 'danger');
    }

    elseif ($action === 'edit_equipment') {
        $eid          = (int)($_POST['equipment_id'] ?? 0);
        $name         = trim($_POST['name'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $daily_rate   = $_POST['daily_rate'] ?? '';
        $status       = $_POST['status'] ?? '';
        $image_url    = trim($_POST['image_url'] ?? '');
        $category_id  = (($_POST['category_id'] ?? '') !== '') ? (int)$_POST['category_id'] : null;
        $valid_status = ['available', 'rented', 'maintenance'];

        if ($eid <= 0) {
            redirect_admin('equipment', 'That equipment could not be found.', 'danger');
        }
        if ($name === '' || $description === '' || $daily_rate === '' || $status === '') {
            redirect_admin('equipment', 'Please fill in the name, description, daily rate and status.', 'danger', $eid);
        }
        if (!is_numeric($daily_rate) || (float)$daily_rate < 0) {
            redirect_admin('equipment', 'The daily rate must be a number of zero or more.', 'danger', $eid);
        }
        if (!in_array($status, $valid_status, true)) {
            redirect_admin('equipment', 'That status is not valid.', 'danger', $eid);
        }
        $rate = (float)$daily_rate;

        // owner_id is left unchanged on edit.
        $update = $conn->prepare(
            "UPDATE equipment
             SET category_id = ?, name = ?, description = ?, daily_rate = ?, status = ?, image_url = ?
             WHERE id = ?"
        );
        $update->bind_param("issdssi", $category_id, $name, $description, $rate, $status, $image_url, $eid);

        if ($update->execute()) {
            redirect_admin('equipment', 'The equipment was updated.', 'success');
        }
        redirect_admin('equipment', 'The equipment could not be updated.', 'danger', $eid);
    }

    elseif ($action === 'delete_equipment') {
        $eid = (int)($_POST['equipment_id'] ?? 0);

        if ($eid <= 0) {
            redirect_admin('equipment', 'That equipment could not be found.', 'danger');
        }
        // Deleting equipment also removes its bookings (ON DELETE CASCADE).
        $delete = $conn->prepare("DELETE FROM equipment WHERE id = ?");
        $delete->bind_param("i", $eid);
        if ($delete->execute() && $delete->affected_rows > 0) {
            redirect_admin('equipment', 'The equipment was deleted.', 'success');
        }
        redirect_admin('equipment', 'The equipment could not be deleted.', 'danger');
    }

    // BOOKING

    elseif ($action === 'approve_booking' || $action === 'reject_booking' || $action === 'complete_booking') {
        $bid = (int)($_POST['booking_id'] ?? 0);

        if ($bid <= 0) {
            redirect_admin('bookings', 'That booking could not be found.', 'danger');
        }

        if ($action === 'approve_booking') {
            // A booking can only be approved while it is still pending.
            $stmt = $conn->prepare(
                "UPDATE bookings SET booking_status = 'approved'
                 WHERE id = ? AND booking_status = 'pending'"
            );
            $stmt->bind_param("i", $bid);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                redirect_admin('bookings', 'Booking approved.', 'success');
            }
            redirect_admin('bookings', 'That booking could not be approved.', 'danger');
        }
        elseif ($action === 'reject_booking') {
            // Rejecting a pending booking marks it cancelled.
            $stmt = $conn->prepare(
                "UPDATE bookings SET booking_status = 'cancelled'
                 WHERE id = ? AND booking_status = 'pending'"
            );
            $stmt->bind_param("i", $bid);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                redirect_admin('bookings', 'Booking rejected.', 'info');
            }
            redirect_admin('bookings', 'That booking could not be rejected.', 'danger');
        }
        else { // complete_booking
            // Only an approved booking can be marked completed.
            $stmt = $conn->prepare(
                "UPDATE bookings SET booking_status = 'completed'
                 WHERE id = ? AND booking_status = 'approved'"
            );
            $stmt->bind_param("i", $bid);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                redirect_admin('bookings', 'Booking marked as completed.', 'success');
            }
            redirect_admin('bookings', 'That booking could not be completed.', 'danger');
        }
    }

    // UNKNOWN ACTION 
    else {
        header('Location: admin.php');
        exit();
    }
}

// 3. FLASH MESSAGE (read once, then clear)
$flash      = '';
$flash_type = 'info';
if (isset($_SESSION['flash'])) {
    $flash      = $_SESSION['flash'];
    $flash_type = $_SESSION['flash_type'] ?? 'info';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}


// 4. WHICH TAB IS ACTIVE
$allowed_tabs = ['users', 'equipment', 'bookings', 'reports'];
$tab = (isset($_GET['tab']) && in_array($_GET['tab'], $allowed_tabs, true)) ? $_GET['tab'] : 'users';

// A shared "edit this record" id, read by the users and equipment tabs.
$edit_id        = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_user      = null;
$edit_equipment = null;

// Give every per-tab variable a default value up front. Each one is only
// assigned inside its own branch below, so declaring them here keeps the
// code tidy and lets the editor's analyzer see that they always exist.
$users_result     = null;
$equipment_result = null;
$bookings_result  = null;
$categories       = [];
$total_users      = 0;
$total_equipment  = 0;
$total_bookings   = 0;
$pending_bookings = 0;
$total_revenue    = 0.0;
$most_rented      = null;

// 5. FETCH THE DATA THE ACTIVE TAB NEEDS (before header.php)
if ($tab === 'users') {

    if ($edit_id > 0) {
        $stmt = $conn->prepare("SELECT id, username, full_name, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_user = $stmt->get_result()->fetch_assoc();
    }

    $users_result = $conn->query(
        "SELECT id, username, full_name, email, role, created_at
         FROM users
         ORDER BY username ASC"
    );

} elseif ($tab === 'equipment') {

    // Categories drive the dropdown in the add / edit form.
    $categories = [];
    $cat_res = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
    while ($c = $cat_res->fetch_assoc()) {
        $categories[] = $c;
    }

    if ($edit_id > 0) {
        $stmt = $conn->prepare(
            "SELECT id, name, description, daily_rate, status, image_url, category_id
             FROM equipment WHERE id = ?"
        );
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_equipment = $stmt->get_result()->fetch_assoc();
    }

    // Join to categories and owners so the table can show names, not ids.
    $equipment_result = $conn->query(
        "SELECT e.id, e.name, e.daily_rate, e.status, e.image_url,
                c.name AS category_name,
                u.full_name AS owner_name, u.username AS owner_username
         FROM equipment e
         LEFT JOIN categories c ON e.category_id = c.id
         LEFT JOIN users u ON e.owner_id = u.id
         ORDER BY e.created_at DESC"
    );

} elseif ($tab === 'bookings') {

    // Join to users and equipment for the farmer name and equipment name.
    // Pending bookings are listed first so they are easy to act on.
    $bookings_result = $conn->query(
        "SELECT b.id, b.start_date, b.end_date, b.total_cost, b.booking_status,
                u.full_name AS farmer_name, u.username AS farmer_username,
                e.name AS equipment_name
         FROM bookings b
         LEFT JOIN users u ON b.user_id = u.id
         LEFT JOIN equipment e ON b.equipment_id = e.id
         ORDER BY (b.booking_status = 'pending') DESC, b.created_at DESC"
    );

} elseif ($tab === 'reports') {

    // All read-only, so plain queries (no user input) are fine here.
    $total_users      = (int)$conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
    $total_equipment  = (int)$conn->query("SELECT COUNT(*) AS c FROM equipment")->fetch_assoc()['c'];
    $total_bookings   = (int)$conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
    $pending_bookings = (int)$conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_status = 'pending'")->fetch_assoc()['c'];

    $total_revenue = (float)$conn->query(
        "SELECT COALESCE(SUM(total_cost), 0) AS revenue
         FROM bookings
         WHERE booking_status IN ('approved', 'completed')"
    )->fetch_assoc()['revenue'];

    $most_rented = $conn->query(
        "SELECT e.name, COUNT(b.id) AS booking_count
         FROM bookings b
         JOIN equipment e ON b.equipment_id = e.id
         GROUP BY b.equipment_id, e.name
         ORDER BY booking_count DESC
         LIMIT 5"
    );
}

include 'header.php';
?>

<div class="page-header">
    <h1>Admin Control Panel</h1>
    <p>Manage users, equipment and bookings, and view system reports for FarmLend 2026.</p>
</div>

<?php if ($flash !== ''): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?>">
        <?php echo htmlspecialchars($flash); ?>
    </div>
<?php endif; ?>

<!-- Tab navigation. Each link resets the edit state by carrying only ?tab=. -->
<nav class="admin-nav">
    <a href="admin.php?tab=users"     class="<?php echo ($tab === 'users')     ? 'active' : ''; ?>">Users</a>
    <a href="admin.php?tab=equipment" class="<?php echo ($tab === 'equipment') ? 'active' : ''; ?>">Equipment</a>
    <a href="admin.php?tab=bookings"  class="<?php echo ($tab === 'bookings')  ? 'active' : ''; ?>">Bookings</a>
    <a href="admin.php?tab=reports"   class="<?php echo ($tab === 'reports')   ? 'active' : ''; ?>">Reports</a>
</nav>


<?php if ($tab === 'users'): ?>
<!-- ============================================================
     USERS TAB
     ============================================================ -->

    <section class="section">
        <h2><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h2>
        <div class="card card-static">
            <div class="card-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="action" value="<?php echo $edit_user ? 'edit_user' : 'add_user'; ?>">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="user_id" value="<?php echo (int)$edit_user['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="u_username">Username</label>
                            <input type="text" class="form-control" id="u_username" name="username" required
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['username']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="u_full_name">Full Name</label>
                            <input type="text" class="form-control" id="u_full_name" name="full_name" required
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['full_name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="u_email">Email</label>
                            <input type="email" class="form-control" id="u_email" name="email" required
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="u_role">Role</label>
                            <select class="form-control" id="u_role" name="role" required>
                                <?php
                                $roles        = ['farmer' => 'Farmer (ordinary user)', 'owner' => 'Owner', 'admin' => 'Admin'];
                                $current_role = $edit_user ? $edit_user['role'] : 'farmer';
                                foreach ($roles as $value => $label) {
                                    $selected = ($current_role === $value) ? 'selected' : '';
                                    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="u_password">
                            <?php echo $edit_user ? 'Reset Password (leave blank to keep the current one)' : 'Password'; ?>
                        </label>
                        <input type="password" class="form-control" id="u_password" name="password"
                               <?php echo $edit_user ? '' : 'required'; ?>
                               placeholder="<?php echo $edit_user ? 'Leave blank to keep the current password' : 'At least 6 characters'; ?>">
                        <p class="form-hint">Passwords are stored as bcrypt hashes, never as plain text.</p>
                    </div>

                    <div class="flex gap-12">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_user ? 'Save Changes' : 'Add User'; ?></button>
                        <?php if ($edit_user): ?>
                            <a href="admin.php?tab=users" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>All Users</h2>
        <?php if ($users_result->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F465;</div>
                <h3>No Users Yet</h3>
                <p>Add the first account using the form above.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($u = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo role_badge($u['role']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($u['created_at']))); ?></td>
                            <td>
                                <div class="flex gap-8">
                                    <a href="admin.php?tab=users&edit=<?php echo (int)$u['id']; ?>"
                                       class="btn btn-secondary btn-sm">Edit</a>
                                    <?php if ((int)$u['id'] !== $admin_id): ?>
                                        <form method="POST" action="admin.php"
                                              onsubmit="return confirm('Delete this user? This also removes their equipment and bookings.');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted text-small">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>


<?php elseif ($tab === 'equipment'): ?>
<!-- ============================================================
     EQUIPMENT TAB
     ============================================================ -->

    <section class="section">
        <h2><?php echo $edit_equipment ? 'Edit Equipment' : 'Add New Equipment'; ?></h2>
        <div class="card card-static">
            <div class="card-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="action" value="<?php echo $edit_equipment ? 'edit_equipment' : 'add_equipment'; ?>">
                    <?php if ($edit_equipment): ?>
                        <input type="hidden" name="equipment_id" value="<?php echo (int)$edit_equipment['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="e_name">Name</label>
                            <input type="text" class="form-control" id="e_name" name="name" required
                                   value="<?php echo $edit_equipment ? htmlspecialchars($edit_equipment['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="e_category">Category</label>
                            <select class="form-control" id="e_category" name="category_id">
                                <option value="">Uncategorized</option>
                                <?php foreach ($categories as $cat):
                                    $selected = ($edit_equipment && (int)$edit_equipment['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>
                                    <option value="<?php echo (int)$cat['id']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="e_rate">Daily Rate (Rs.)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="e_rate" name="daily_rate" required
                                   value="<?php echo $edit_equipment ? htmlspecialchars($edit_equipment['daily_rate']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="e_status">Status</label>
                            <select class="form-control" id="e_status" name="status" required>
                                <?php
                                $statuses   = ['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance'];
                                $cur_status = $edit_equipment ? $edit_equipment['status'] : 'available';
                                foreach ($statuses as $value => $label) {
                                    $selected = ($cur_status === $value) ? 'selected' : '';
                                    echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="e_image">Image URL (optional)</label>
                        <input type="text" class="form-control" id="e_image" name="image_url"
                               placeholder="https://example.com/tractor.jpg"
                               value="<?php echo $edit_equipment ? htmlspecialchars($edit_equipment['image_url']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="e_desc">Description</label>
                        <textarea class="form-control" id="e_desc" name="description" required><?php echo $edit_equipment ? htmlspecialchars($edit_equipment['description']) : ''; ?></textarea>
                    </div>

                    <div class="flex gap-12">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_equipment ? 'Save Changes' : 'Add Equipment'; ?></button>
                        <?php if ($edit_equipment): ?>
                            <a href="admin.php?tab=equipment" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>All Equipment</h2>
        <?php if ($equipment_result->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F69C;</div>
                <h3>No Equipment Yet</h3>
                <p>Add the first machine using the form above.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Daily Rate</th>
                            <th>Status</th>
                            <th>Owner</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($e = $equipment_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['name']); ?></td>
                            <td><?php echo htmlspecialchars($e['category_name'] ?? 'Uncategorized'); ?></td>
                            <td>Rs. <?php echo number_format($e['daily_rate'], 2); ?></td>
                            <td><?php echo equipment_status_badge($e['status']); ?></td>
                            <td><?php echo htmlspecialchars($e['owner_name'] ?? 'Unknown'); ?></td>
                            <td>
                                <div class="flex gap-8">
                                    <a href="admin.php?tab=equipment&edit=<?php echo (int)$e['id']; ?>"
                                       class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="admin.php"
                                          onsubmit="return confirm('Delete this equipment? This also removes its bookings.');">
                                        <input type="hidden" name="action" value="delete_equipment">
                                        <input type="hidden" name="equipment_id" value="<?php echo (int)$e['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>


<?php elseif ($tab === 'bookings'): ?>
<!-- ============================================================
     BOOKINGS TAB
     ============================================================ -->

    <section class="section">
        <h2>All Bookings</h2>
        <p class="text-muted">Approve or reject pending requests. An approved rental can be marked completed once it is returned.</p>

        <?php if ($bookings_result->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">&#x1F4CB;</div>
                <h3>No Bookings Yet</h3>
                <p>Bookings made by farmers from the catalog will appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Farmer</th>
                            <th>Equipment</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($b = $bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td>R<?php echo (int)$b['id']; ?></td>
                            <td><?php echo htmlspecialchars($b['farmer_name'] ?? 'Unknown user'); ?></td>
                            <td><?php echo htmlspecialchars($b['equipment_name'] ?? 'Removed equipment'); ?></td>
                            <td><?php echo htmlspecialchars($b['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($b['end_date']); ?></td>
                            <td>Rs. <?php echo number_format($b['total_cost'], 2); ?></td>
                            <td><?php echo booking_status_badge($b['booking_status']); ?></td>
                            <td>
                                <?php if ($b['booking_status'] === 'pending'): ?>
                                    <div class="flex gap-8">
                                        <form method="POST" action="admin.php">
                                            <input type="hidden" name="action" value="approve_booking">
                                            <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                        </form>
                                        <form method="POST" action="admin.php"
                                              onsubmit="return confirm('Reject this booking?');">
                                            <input type="hidden" name="action" value="reject_booking">
                                            <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </div>
                                <?php elseif ($b['booking_status'] === 'approved'): ?>
                                    <form method="POST" action="admin.php"
                                          onsubmit="return confirm('Mark this booking as completed?');">
                                        <input type="hidden" name="action" value="complete_booking">
                                        <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm">Mark Completed</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>


<?php elseif ($tab === 'reports'): ?>
<!-- ============================================================
     REPORTS TAB (read-only statistics)
     ============================================================ -->

    <section class="section">
        <h2>System Reports</h2>

        <!-- Headline revenue figure -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">Rs. <?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Total Revenue (Approved and Completed)</div>
            </div>
        </div>

        <!-- Count cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_equipment; ?></div>
                <div class="stat-label">Equipment Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
        </div>

        <h3 class="mt-30">Most Rented Equipment</h3>
        <?php if ($most_rented->num_rows === 0): ?>
            <p class="text-muted">No bookings have been made yet, so there is nothing to rank.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Equipment</th>
                            <th>Times Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $rank = 1; while ($m = $most_rented->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($m['name']); ?></td>
                            <td><?php echo (int)$m['booking_count']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

<?php endif; ?>


<?php
$conn->close();
include 'footer.php';
?>