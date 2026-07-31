<?php
session_start();
require_once("db_connect.php");
include("header.php");

// Check user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logged-in user ID
$user_id = $_SESSION['user_id'];

// Get booking history
$sql = "SELECT * FROM bookings
        WHERE user_id = '$user_id'
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Rental History</h1>
    <p>View all your previous equipment rentals.</p>
</div>

<!-- Rental Table -->
<div class="table-wrapper">
    <table class="data-table">

        <thead>
            <tr>
                <th>Rental ID</th>
                <th>Equipment ID</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Total Cost</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

            <tr>

                <td>R<?php echo $row['id']; ?></td>

                <td><?php echo $row['equipment_id']; ?></td>

                <td><?php echo $row['start_date']; ?></td>

                <td><?php echo $row['end_date']; ?></td>

                <td>Rs. <?php echo number_format($row['total_cost'], 2); ?></td>

                <td>

                    <?php
                    if ($row['booking_status'] == "Completed") {
                        echo "<span class='badge badge-confirmed'>Completed</span>";
                    } elseif ($row['booking_status'] == "Returned") {
                        echo "<span class='badge badge-returned'>Returned</span>";
                    } elseif ($row['booking_status'] == "Pending") {
                        echo "<span class='badge badge-pending'>Pending</span>";
                    } else {
                        echo "<span class='badge'>" . $row['booking_status'] . "</span>";
                    }
                    ?>

                </td>

            </tr>

        <?php
            }

        } else {
        ?>

            <tr>
                <td colspan="6" style="text-align:center;">
                    No rental history found.
                </td>
            </tr>

        <?php
        }
        ?>

        </tbody>

    </table>
</div>

<?php
include("footer.php");
?>
