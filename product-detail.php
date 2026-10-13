<?php
require_once 'functions.php';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";
if ($category_filter > 0) {
    $sql .= " AND p.category_id = $category_filter";
}
if (!empty($search_query)) {
    $sql .= " AND p.name LIKE '%$search_query%'";
}
$sql .= " ORDER BY p.id DESC";
$products = $conn->query($sql);
$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All T-Shirts - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .product-filter {
            display: flex;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .product-search {
            padding: 0.6rem;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .category-select {
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .product-card-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 6px;
        }
        .product-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .product-price {
            font-weight: bold;
            color: #0d6efd;
        }
        .product-button {
            display: block;
            text-align: center;
        }
        .no-products {
            color: #6c757d;
            padding: 1rem 0;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"> 👕 TeeCraft</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Shop T-Shirts</a></li>
            <li>
                <a href="cart.php">Cart (<?php echo get_cart_count(); ?>)</a>
            </li>
        </ul>
    </header>
    <main class="container">
        <h2>Shop All T-Shirts</h2>
        <form method="GET" action="products.php" class="product-filter">
            <input
                type="text"
                name="search"
                class="product-search"
                placeholder="Search T-Shirts..."
                value="<?php echo htmlspecialchars($search_query); ?>"
            >
            <select name="category" class="category-select">
                <option value="0">
                    All Categories
                </option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option
                        value="<?php echo $cat['id']; ?>"
                        <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary">
                Filter
            </button>
        </form>
        <div class="card-grid">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($p = $products->fetch_assoc()): ?>
                    <div class="card">
                        <img
                            src="<?php echo htmlspecialchars($p['image_url'] ? $p['image_url'] : 'assets/images/default.jpg'); ?>"
                            class="product-card-image"
                            alt="<?php echo htmlspecialchars($p['name']); ?>"
                        >
                        <h3>
                            <?php echo htmlspecialchars($p['name']); ?>
                        </h3>
                        <p class="product-info">
                            Size: <?php echo htmlspecialchars($p['size']); ?>
                            |
                            Color: <?php echo htmlspecialchars($p['color']); ?>
                        </p>
                        <p class="product-price">
                            $<?php echo number_format($p['price'], 2); ?>
                        </p>
                        <a
                            href="product-detail.php?id=<?php echo $p['id']; ?>"
                            class="btn btn-primary product-button"
                        >
                            View Details
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-products">
                    No T-Shirts found matching your criteria.
                </p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

