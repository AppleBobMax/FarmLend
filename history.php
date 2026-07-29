<?php
session_start();

require_once "db_connect.php";
include("header.php");


// Check user login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Logged-in user ID

$user_id = $_SESSION['user_id'];

// Get rental history

$sql = "SELECT * FROM rental_history 
        WHERE user_id='$user_id' 
        ORDER BY rental_date DESC";


$result = $conn->query($sql);



?>

<!-- Page Header -->

<div class="page-header">

<h1>

Rental History

</h1>


<p>

View all your previous equipment rentals.

</p>


</div>





<!-- Rental Table -->


<div class="table-wrapper">


<table class="data-table">



<thead>

<tr>

<th>Rental ID</th>
<th>Equipment</th>
<th>Rental Date</th>
<th>Return Date</th>
<th>Total Cost</th>
<th>Status</th>


</tr>

</thead>

<tbody>

<?php


if($result->num_rows > 0){

    while($row = $result->fetch_assoc()){

?>

<tr>
<td>

R<?php echo $row['id']; ?>

</td>


<td>
<?php echo htmlspecialchars($row['equipment_name']); ?>
</td>

<td>
<?php echo $row['rental_date']; ?>
</td>

<td>
<?php echo $row['return_date']; ?>
</td>


<td>
Rs. <?php echo number_format($row['total_cost'],2); ?>
</td>


<td>


<?php
if($row['status']=="Returned"){


echo "

<span class='badge badge-returned'>
Returned
</span>

";


}

elseif($row['status']=="Completed"){


echo "

<span class='badge badge-confirmed'>
Completed
</span>

";


}

else{


echo "

<span class='badge badge-pending'>
Pending Return
</span>

";

}

?>
</td>
</tr>


<?php


    }


}

else{


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
