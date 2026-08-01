<?php

require_once "db_connect.php";
include "header.php";

$is_logged_in = isset($_SESSION['user_id']);
$display_name = $is_logged_in
    ? ($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Farmer')
    : '';
?>

<!-- Hero Section -->
<section class="hero">
    <?php if ($is_logged_in): ?>
        <h1>Welcome back, <?php echo htmlspecialchars($display_name); ?>!</h1>
        <p>Browse the latest agricultural machinery and manage your rentals, all in one place.</p>
        <a href="catalog.php" class="btn">Browse Equipment</a>
    <?php else: ?>
        <h1>Welcome to FarmLend</h1>
        <p>Rent high-quality agricultural equipment from trusted owners. Save money and boost your farming productivity with FarmLend.</p>
        <a href="login.php" class="btn">Log In to Get Started</a>
    <?php endif; ?>
</section>

<!-- Features -->
<section class="section">
    <div class="page-header">
        <h2>Why Choose FarmLend?</h2>
        <p>Affordable agricultural equipment rental services for farmers.</p>
    </div>

    <div class="feature-list">
        <div class="feature-item">
            <h3>&#x1F69C; Wide Equipment Collection</h3>
            <p>Tractors, harvesters, plough machines, and modern farming tools, all in one catalog.</p>
        </div>
        <div class="feature-item">
            <h3>&#x1F4B0; Affordable Rental Prices</h3>
            <p>Rent what you need for the days you need it, without buying expensive machinery.</p>
        </div>
        <div class="feature-item">
            <h3>&#x1F4C5; Easy Booking System</h3>
            <p>Find available equipment and reserve it online in a few clicks.</p>
        </div>
    </div>
</section>

<!-- Equipment Cards -->
<section class="section">
    <div class="page-header">
        <h2>Popular Rental Equipment</h2>
        <p>Choose the right machine for your farming needs.</p>
    </div>

    <div class="card-grid">
    <?php
    // Only show equipment that is currently available (newest first, up to 6).
    $sql = "SELECT id, name, description, daily_rate, image_url
            FROM equipment
            WHERE status = 'available'
            ORDER BY created_at DESC
            LIMIT 6";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="card">
            <?php if (!empty($row['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                     class="card-img"
                     alt="<?php echo htmlspecialchars($row['name']); ?>">
            <?php else: ?>
                <div class="card-img-placeholder">&#x1F69C;</div>
            <?php endif; ?>

            <div class="card-body">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>

            <div class="card-footer">
                <span class="price">
                    Rs. <?php echo number_format($row['daily_rate'], 2); ?>
                    <span class="price-unit">/ day</span>
                </span>
                <a href="booking.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-sm">
                    Rent Now
                </a>
            </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <p class="text-muted">No equipment has been listed yet. Please check back soon.</p>
    <?php endif; ?>
    </div>
</section>

<!-- Statistics -->
<section class="section">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">500+</div>
            <div class="stat-label">Equipment</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">1200+</div>
            <div class="stat-label">Farmers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">3000+</div>
            <div class="stat-label">Successful Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support</div>
        </div>
    </div>
</section>

<?php
$conn->close();
include "footer.php";
?>