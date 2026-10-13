<?php
require_once 'functions.php';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$size = isset($_GET['size']) ? sanitize($_GET['size']) : '';
$color = isset($_GET['color']) ? sanitize($_GET['color']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}
if ($cat_id > 0) {
    $sql .= " AND p.category_id = $cat_id";
}
if (!empty($size)) {
    $sql .= " AND p.size = '$size'";
}
if (!empty($color)) {
    $sql .= " AND p.color LIKE '%$color%'";
}
if ($sort === 'price_low') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort === 'price_high') {
    $sql .= " ORDER BY p.price DESC";
} else {
    $sql .= " ORDER BY p.id DESC";
}
$products = $conn->query($sql);
$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop T-Shirts - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #fafafa;
            color: #1e293b;
        }
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 1.6rem;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
        }
        .orders-link {
            color: #0d6efd !important;
            font-weight: bold;
        }
        .logout-link {
            color: #dc2626 !important;
        }
        .cart-btn {
            background: #000;
            color: #fff !important;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .layout {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }
        .sidebar {
            width: 260px;
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            height: fit-content;
        }
        .sidebar h3 {
            margin-top: 0;
            font-size: 1.1rem;
            color: #0f172a;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.5rem;
        }
        .filter-group {
            margin-bottom: 1.2rem;
        }
        .filter-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 0.4rem;
            color: #475569;
        }
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn-filter {
            background: #000;
            color: #fff;
            border: none;
            width: 100%;
            padding: 0.7rem;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-reset {
            display: block;
            text-align: center;
            margin-top: 0.8rem;
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .products-area {
            flex: 1;
        }
        .products-title {
            margin-top: 0;
            color: #0f172a;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .product-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: 0.3s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }
        .product-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        .product-info {
            padding: 1rem;
        }
        .product-cat {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .product-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0.3rem 0;
            color: #0f172a;
            text-decoration: none;
            display: block;
        }
        .product-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.8rem;
        }
        .price {
            font-size: 1.1rem;
            font-weight: 800;
            color: #000;
        }
        .btn-view {
            background: #f1f5f9;
            color: #0f172a;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .btn-view:hover {
            background: #000;
            color: #fff;
        }
        .no-products {
            grid-column: 1 / -1;
            background: #fff;
            padding: 3rem;
            text-align: center;
            border-radius: 10px;
            color: #64748b;
        }
        .no-products h3 {
            margin-top: 0;
        }
        @media (max-width: 850px) {
            .layout {
                flex-direction: column;
            }
            .sidebar {
                width: auto;
            }
            .nav-links {
                gap: 1rem;
            }
        }
        @media (max-width: 600px) {
            .navbar {
                padding: 1rem;
                flex-wrap: wrap;
                gap: 1rem;
            }
            .nav-links {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.3rem;
            }
            .layout {
                margin: 1.5rem auto;
                padding: 0 0.8rem;
            }
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"> 👕 TEECRAFT</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Shop All T-Shirts</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>
                    <a href="my-orders.php" class="orders-link"> My Orders</a>
                </li>
                <li>
                    <a href="logout.php" class="logout-link">Logout</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php">Login</a>
                </li>
                <li>
                    <a href="register.php">Register</a>
                </li>
            <?php endif; ?>
        </ul>
        <a href="cart.php" class="cart-btn">Cart (<?php echo get_cart_count(); ?>)</a>
    </header>
    <div class="layout">
        <aside class="sidebar">
            <h3>Filter Products</h3>
            <form action="products.php" method="GET">
                <div class="filter-group">
                    <label> Search Keyword</label>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="e.g. Cyberpunk, Anime..."
                    >
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="0">All Categories</option>
                        <?php
                        $categories->data_seek(0);
                        while ($cat = $categories->fetch_assoc()):
                        ?>
                            <option
                                value="<?php echo $cat['id']; ?>"
                                <?php echo ($cat_id == $cat['id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Size</label>
                    <select name="size">
                        <option value="">All Sizes</option>
                        <option value="S" <?php echo ($size === 'S') ? 'selected' : ''; ?>> Small (S) </option>
                        <option value="M" <?php echo ($size === 'M') ? 'selected' : ''; ?>> Medium (M)</option>
                        <option value="L" <?php echo ($size === 'L') ? 'selected' : ''; ?>> Large (L)</option>
                        <option value="XL" <?php echo ($size === 'XL') ? 'selected' : ''; ?>>Extra Large (XL)</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label> Sort By</label>
                    <select name="sort">
                        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>> Newest First</option>
                        <option value="price_low" <?php echo ($sort === 'price_low') ? 'selected' : ''; ?>> Price: Low to High</option>
                        <option value="price_high" <?php echo ($sort === 'price_high') ? 'selected' : ''; ?>> Price: High to Low</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="products.php" class="btn-reset"> Reset Filters</a>
            </form>
        </aside>
        <main class="products-area">
            <h2 class="products-title">
                All T-Shirts
                (<?php echo $products ? $products->num_rows : 0; ?>)
            </h2>
            <div class="products-grid">
                <?php if ($products && $products->num_rows > 0): ?>
                    <?php while ($p = $products->fetch_assoc()): ?>
                        <div class="product-card">
                            <img
                                src="<?php echo htmlspecialchars($p['image_url']); ?>"
                                class="product-img"
                                alt="<?php echo htmlspecialchars($p['name']); ?>"
                            >
                            <div class="product-info">
                                <span class="product-cat">
                                    <?php echo htmlspecialchars($p['category_name'] ?? 'T-Shirt'); ?>
                                </span>
                                <a
                                    href="product.php?id=<?php echo $p['id']; ?>"
                                    class="product-title"
                                >
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </a>
                                <div class="product-bottom">
                                    <span class="price">
                                        $<?php echo number_format($p['price'], 2); ?>
                                    </span>
                                    <a
                                        href="single-product.php?id=<?php echo $p['id']; ?>"
                                        class="btn-view"
                                    >
                                        View Tee
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-products">
                        <h3> Koi product nahi mila!</h3>
                        <p> Meharbani karke apne filter keywords badal kar dobara search karein.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

