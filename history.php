<?php

session_start();
require_once "db_connect.php";

// Block guests before any output is produced.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$flash   = '';

// ---- Handle a cancel request (Post / Redirect / Get) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancel_id = (int)$_POST['cancel_booking_id'];

    // Only cancel a booking that belongs to THIS user and is still pending.
    $cancel = $conn->prepare(
        "UPDATE bookings
         SET booking_status = 'cancelled'
         WHERE id = ? AND user_id = ? AND booking_status = 'pending'"
    );
    $cancel->bind_param("ii", $cancel_id, $user_id);
    $cancel->execute();

    $_SESSION['flash'] = ($cancel->affected_rows > 0)
        ? 'Your booking was cancelled.'
        : 'That booking could not be cancelled.';

    header("Location: history.php");
    exit();
}

// Pick up a one-time flash message (after the redirect above).
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// ---- Fetch this user's bookings, with the equipment name ----
$stmt = $conn->prepare(
    "SELECT b.id, b.start_date, b.end_date, b.total_cost, b.booking_status,
            e.name AS equipment_name
     FROM bookings b
     LEFT JOIN equipment e ON b.equipment_id = e.id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include "header.php";
?>

<!-- Page Header -->
<div class="page-header">
    <h1>My Rentals</h1>
    <p>View your bookings and cancel any that are still pending.</p>
</div>

<?php if ($flash !== ''): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<?php if ($result->num_rows === 0): ?>

    <!-- Empty state -->
    <div class="empty-state">
        <div class="empty-state-icon">&#x1F4CB;</div>
        <h3>No Rentals Yet</h3>
        <p>You have not booked any equipment. Browse the catalog to make your first booking.</p>
        <a href="catalog.php" class="btn btn-primary">Browse Catalog</a>
    </div>

<?php else: ?>

    <!-- Rental Table -->
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Equipment</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>R<?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['equipment_name'] ?? 'Removed equipment'); ?></td>
                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                    <td>Rs. <?php echo number_format($row['total_cost'], 2); ?></td>
                    <td>
                        <?php
                        // Map the database status to a label and a badge colour.
                        $status = $row['booking_status'];
                        $badges = [
                            'pending'   => ['badge-pending',   'Pending'],
                            'approved'  => ['badge-approved',  'Approved'],
                            'completed' => ['badge-completed', 'Completed'],
                            'cancelled' => ['badge-cancelled', 'Cancelled'],
                        ];
                        if (isset($badges[$status])) {
                            echo '<span class="badge ' . $badges[$status][0] . '">'
                                 . $badges[$status][1] . '</span>';
                        } else {
                            echo '<span class="badge">' . htmlspecialchars(ucfirst($status)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($status === 'pending'): ?>
                            <form method="POST" action="history.php"
                                  onsubmit="return confirm('Cancel this booking?');">
                                <input type="hidden" name="cancel_booking_id"
                                       value="<?php echo (int)$row['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
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

<?php
$conn->close();
include "footer.php";
?>
