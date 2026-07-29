<?php
session_start();

require_once "db_connect.php";
include("header.php");
?>


<!-- Hero Section -->

<section class="hero">

<img src="4to/1.jpg" alt="Farm Background">

<h1>
WELCOME TO FARM LEND - YOUR Agricultural Equipment Rental Platform
</h1>

<p>
Rent high-quality agricultural equipment from trusted suppliers.
Save money and improve your farming productivity with FarmLend.
</p>

<a href="Equipment.php" class="btn">
Browse Equipment
</a>

</section>


<!-- Features -->

<section class="section">

<div class="page-header">

<h2>
Why Choose FarmLend?
</h2>

<p>
Affordable agricultural equipment rental services for farmers.
</p>

</div>


<div class="feature-list">


<div class="feature-item">

<h3>
🚜 Wide Equipment Collection
</h3>

<p>
Tractors, harvesters, plough machines and modern farming tools available.
</p>

</div>



<div class="feature-item">

<h3>
💰 Affordable Rental Prices
</h3>

<p>
Rent equipment without buying expensive machinery.
</p>

</div>



<div class="feature-item">

<h3>
📍 Easy Booking System
</h3>

<p>
Find nearby equipment and reserve it quickly online.
</p>

</div>


</div>

</section>



<!-- Equipment Cards -->

<section class="section">

<div class="page-header">

<h2>
Popular Rental Equipment
</h2>

<p>
Choose the right machine for your farming needs.
</p>

</div>


<div class="card-grid">

<?php

$sql = "SELECT * FROM equipment";

$result = $conn->query($sql);


while($row = $result->fetch_assoc()){

?>

<div class="card">


<div class="card-img-placeholder">

<img src="4to/<?php echo $row['image_url']; ?>" 
alt="Equipment">

</div>


<div class="card-body">

<h3>
<?php echo $row['name']; ?>
</h3>


<p>
<?php echo $row['description']; ?>
</p>


<div class="price">

Rs. <?php echo $row['daliy_rate']; ?>

<span class="price-unit">
/ day
</span>

<a href="Equipment.php" class="btn btn-primary btn-sm">
Rent Now
</a> 

</div>
</div>
</div>


<?php

}

?>

</div>

</section>


<!-- Statistics -->

<section class="section">

<div class="stats-grid">


<div class="stat-card">

<div class="stat-number">
500+
</div>

<div class="stat-label">
Equipment
</div>

</div>



<div class="stat-card">

<div class="stat-number">
1200+
</div>

<div class="stat-label">
Farmers
</div>

</div>



<div class="stat-card">

<div class="stat-number">
3000+
</div>

<div class="stat-label">
Successful Rentals
</div>

</div>



<div class="stat-card">

<div class="stat-number">
24/7
</div>

<div class="stat-label">
Support
</div>

</div>


</div>

</section>



<?php
include("footer.php");
?>
