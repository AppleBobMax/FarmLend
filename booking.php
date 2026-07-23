<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$equipment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the equipment being booked
$stmt = $conn->prepare("SELECT * FROM equipment WHERE id = ?");
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$equipment = $stmt->get_result()->fetch_assoc();

if (!$equipment) {
    include 'header.php';
    echo '<div class="empty-state"><h3>Equipment not found</h3></div>';
    include 'footer.php';
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (strtotime($end_date) <= strtotime($start_date)) {
        $error = "End date must be after start date.";
    } else {
        // Check for overlapping bookings on this equipment
        $overlap_stmt = $conn->prepare(
            "SELECT id FROM bookings 
             WHERE equipment_id = ? 
             AND booking_status IN ('pending', 'approved')
             AND NOT (end_date < ? OR start_date > ?)"
        );
        $overlap_stmt->bind_param("iss", $equipment_id, $start_date, $end_date);
        $overlap_stmt->execute();
        $overlap = $overlap_stmt->get_result();

        if ($overlap->num_rows > 0) {
            $error = "This equipment is already booked for part of that date range.";
        } else {
            $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
            $total_cost = $days * $equipment['daily_rate'];

            $insert_stmt = $conn->prepare(
                "INSERT INTO bookings (user_id, equipment_id, start_date, end_date, total_cost, booking_status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            );
            $user_id = $_SESSION['user_id'];
            $insert_stmt->bind_param("iissd", $user_id, $equipment_id, $start_date, $end_date, $total_cost);

            if ($insert_stmt->execute()) {
                $success = "Booking submitted! Total cost: Rs. " . number_format($total_cost, 2);
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include 'header.php';
?>

<div class="page-header">
    <h1>Book Equipment</h1>
    <p>Reserve <?php echo htmlspecialchars($equipment['name']); ?> for your selected dates.</p>
</div>

<?php if ($error): ?>
    <p class="form-error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="text-center"><?php echo htmlspecialchars($success); ?></p>
<?php else: ?>

<div class="container-narrow">
    <div class="card">
        <div class="card-body">
            <h3><?php echo htmlspecialchars($equipment['name']); ?></h3>
            <p><?php echo htmlspecialchars($equipment['description']); ?></p>
            <p class="price">Rs. <?php echo number_format($equipment['daily_rate'], 2); ?> <span class="price-unit">/ day</span></p>
        </div>
    </div>

    <form method="POST" class="mt-20">
        <div class="form-row">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
    </form>
</div>

<?php endif; ?>

<?php
$conn->close();
include 'footer.php';
?>