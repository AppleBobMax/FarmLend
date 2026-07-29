<?php
// ============================================================
// catalog.php  —  Equipment Catalog page

// ============================================================

session_start();
require_once 'db_connect.php';

// Block guests 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Read search box + type filter 
$search      = isset($_GET['search'])      ? trim($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

//Get the list of categories for the dropdown 
$cat_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

//  Build the main search query safely 
// JOIN equipment to categories to get the category name for display.
$sql = "SELECT e.id, e.name, e.description, e.daily_rate, e.status, e.image_url,
               c.name AS category_name
        FROM equipment e
        LEFT JOIN categories c ON e.category_id = c.id
        WHERE 1=1";
$params = [];
$bind_types = "";

if ($search !== '') {
    $sql .= " AND e.name LIKE ?";
    $params[] = "%" . $search . "%";
    $bind_types .= "s";
}
if ($category_id > 0) {
    $sql .= " AND e.category_id = ?";
    $params[] = $category_id;
    $bind_types .= "i";
}
$sql .= " ORDER BY e.name ASC";

$stmt = $conn->prepare($sql);
if ($bind_types !== "") {
    $stmt->bind_param($bind_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

include 'header.php';
?>

<div class="page-header">
    <h1>Equipment Catalog</h1>
    <p>Browse available agricultural machinery for rent.</p>
</div>

<!-- Search + filter bar -->
<form class="search-bar" method="get" action="catalog.php">
    <input type="text" class="form-control" name="search"
           placeholder="Search equipment by name..."
           value="<?php echo htmlspecialchars($search); ?>">

    <select class="form-control" name="category_id" style="max-width: 200px;">
        <option value="">All Categories</option>
        <?php while ($row = $cat_result->fetch_assoc()): ?>
            <option value="<?php echo (int)$row['id']; ?>"
                <?php echo ($category_id === (int)$row['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($row['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit" class="btn btn-primary">Search</button>
</form>

<?php if ($result->num_rows === 0): ?>

    <!-- Empty state -->
    <div class="empty-state">
        <div class="empty-state-icon">&#x1F50D;</div>
        <h3>No Equipment Found</h3>
        <p>Try a different search term or category.</p>
        <a href="catalog.php" class="btn btn-primary">Reset Search</a>
    </div>

<?php else: ?>

    <!-- Equipment card grid -->
    <div class="card-grid mt-20">
        <?php while ($item = $result->fetch_assoc()): ?>
            <div class="card">

                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                         class="card-img"
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php else: ?>
                    <div class="card-img-placeholder">&#x1F69C;</div>
                <?php endif; ?>

                <div class="card-body">
                    <span class="tag"><?php echo htmlspecialchars($item['category_name'] ?? 'Uncategorized'); ?></span>
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>

                    <?php if ($item['status'] === 'available'): ?>
                        <span class="availability availability-available">
                            <span class="availability-dot"></span> Available
                        </span>
                    <?php else: ?>
                        <span class="availability availability-booked">
                            <span class="availability-dot"></span> <?php echo htmlspecialchars(ucfirst($item['status'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <span class="price">
                        Rs. <?php echo number_format($item['daily_rate'], 2); ?>
                        <span class="price-unit">/ day</span>
                    </span>
                    <?php if ($item['status'] === 'available'): ?>
                        <a href="booking.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-primary btn-sm">
                            Book Now
                        </a>
                    <?php else: ?>
                        <span class="btn btn-secondary btn-sm">Unavailable</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php endif; ?>

<?php
$conn->close();
include 'footer.php';
?>