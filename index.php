<?php
require_once 'functions.php';
$categories = get_categories();
$products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 8");
$reviews = $conn->query("SELECT r.*, p.name as product_name FROM reviews r LEFT JOIN products p ON r.product_id = p.id ORDER BY r.id DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeeCraft | Premium Streetwear & Graphic Tees</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>      
        * {
            box-sizing: border-box;
        }
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
            color: #000000;
            text-decoration: none;
            letter-spacing: -0.5px;
            z-index: 1000;
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
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .nav-links a:hover {
            color: #000;
        }
        .cart-btn {
            background: #000;
            color: #fff;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('interface.jpg') center/cover no-repeat;
            height: 75vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 0 1rem;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #e2e8f0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary {
            background: #fff;
            color: #000;
            padding: 0.9rem 2.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #000;
            color: #fff;
        }
        .features-bar {
            background: #fff;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            border-bottom: 1px solid #e2e8f0;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        .feature-box h4 {
            margin: 0.5rem 0 0.2rem;
            font-size: 1.05rem;
        }
        .feature-box p {
            margin: 0;
            color: #64748b;
            font-size: 0.85rem;
        }
        .section-title {
            text-align: center;
            margin: 3.5rem 0 2rem;
        }
        .section-title h2 {
            font-size: 2.2rem;
            font-weight: 800;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }
        .section-title p {
            color: #64748b;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .cat-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 260px;
            text-decoration: none;
        }
        .cat-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .cat-card:hover img {
            transform: scale(1.08);
        }
        .cat-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
            color: #fff;
        }
        .cat-overlay h3 {
            margin: 0;
            font-size: 1.3rem;
            text-transform: uppercase;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: 0.3s;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .product-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }
        .product-info {
            padding: 1.2rem;
        }
        .product-cat {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .product-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0.4rem 0;
            color: #0f172a;
            text-decoration: none;
            display: block;
        }
        .product-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }
        .price {
            font-size: 1.2rem;
            font-weight: 800;
            color: #000;
        }
        .btn-cart {
            background: #f1f5f9;
            color: #0f172a;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .btn-cart:hover {
            background: #000;
            color: #fff;
        }
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .review-card {
            background: #fff;
            padding: 1.8rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .stars {
            color: #f59e0b;
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .faq-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.2rem;
            margin-bottom: 1rem;
        }
        .faq-item h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.05rem;
            color: #0f172a;
        }
        .faq-item p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .order-link {
            color: #0d6efd;
            font-weight: bold;
        }
        .logout-link {
            color: #dc2626;
        }
        .feature-icon {
            font-size: 1.8rem;
        }
        .review-comment {
            font-style: italic;
            color: #334155;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        .review-user {
            display: block;
            color: #0f172a;
            font-size: 0.9rem;
        }
        .review-product {
            font-size: 0.8rem;
            color: #64748b;
        }
        .footer-description {
            font-size: 0.88rem;
            line-height: 1.6;
        }
        .footer-bottom {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #64748b;
            font-size: 0.85rem;
        }
        .admin-link {
            color: #fff;
            text-decoration: none;
        }
        footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 4rem 2rem 2rem;
            margin-top: 5rem;
        }
        .footer-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
        }
        .footer-col h4 {
            color: #fff;
            margin-top: 0;
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
        }
        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-col ul li {
            margin-bottom: 0.8rem;
        }
        .footer-col ul li a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .footer-bottom {
            text-align: center;
            border-top: 1px solid #1e293b;
            margin-top: 3rem;
            padding-top: 1.5rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>  
    <header class="navbar">
        <a href="index.php" class="logo" style= "color: black;" > 👕 TEECRAFT </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Shop All T-Shirts</a></li>
            <li><a href="products.php?category=1">Streetwear</a></li>
            <li><a href="products.php?category=2">Anime & Graphic</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="my-orders.php" class="order-link">My Orders</a></li>
                <li><a href="logout.php" class="logout-link">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
        <a href="cart.php" class="cart-btn">Cart (<?php echo get_cart_count(); ?>)</a>
    </header>
    <section class="hero">
        <div class="hero-content">
            <h1>URBAN HEAVYWEIGHT TEES</h1>
            <p>Crafted for comfort, styled for impact. Discover 240 GSM oversized dropped-shoulder t-shirts built to last.</p>
            <a href="products.php" class="btn-primary">Explore Collection &rarr;</a>
        </div>
    </section>
    <div class="features-bar">
        <div class="feature-box">
            <div class="feature-icon">📦</div>
            <h4>Fast Delivery</h4>
            <p>Nationwide shipping in 2-4 days</p>
        </div>
        <div class="feature-box">
            <div class="feature-icon">👕</div>
            <h4>240 GSM Fabric</h4>
            <p>Ultra-dense combed 100% cotton</p>
        </div>
        <div class="feature-box">
            <div class="feature-icon">💵</div>
            <h4>Cash on Delivery</h4>
            <p>Pay safely at your doorstep</p>
        </div>
        <div class="feature-box">
            <div class="feature-icon">🔄</div>
            <h4>Easy Returns</h4>
            <p>7-day hassle-free exchanges</p>
        </div>
    </div>
    <section>
        <div class="section-title">
            <h2>Shop By Category</h2>
            <p>Explore our curated streetwear fits designed for everyday flex</p>
        </div>
        <div class="category-grid">
            <a href="products.php?category=1" class="cat-card">
                <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600" alt="Oversized Streetwear">
                <div class="cat-overlay">
                    <h3>Oversized Streetwear</h3>
                </div>
            </a>
            <a href="products.php?category=2" class="cat-card">
                <img src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=600" alt="Graphic & Anime">
                <div class="cat-overlay">
                    <h3>Graphic & Anime</h3>
                </div>
            </a>
            <a href="products.php?category=3" class="cat-card">
                <img src="https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=600" alt="Minimalist Basics">
                <div class="cat-overlay">
                    <h3>Minimalist Basics</h3>
                </div>
            </a>
        </div>
    </section>
    <section>
        <div class="section-title">
            <h2>Trending Drops</h2>
            <p>Our top-rated & most wanted t-shirts of the season</p>
        </div>
        <div class="products-grid">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($p = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="product-img" alt="Product Image">
                        <div class="product-info">
                            <span class="product-cat"><?php echo htmlspecialchars($p['category_name'] ?? 'T-Shirt'); ?></span>
                            <a href="single-product.php?id=<?php echo $p['id']; ?>" class="product-title"><?php echo htmlspecialchars($p['name']); ?></a>
                            <div class="product-bottom">
                                <span class="price">$<?php echo number_format($p['price'], 2); ?></span>
                                <a href="single-product.php?id=<?php echo $p['id']; ?>" class="btn-cart">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
    <section>
        <div class="section-title">
            <h2>Loved By Thousands</h2>
            <p>Real feedback from our verified streetwear community</p>
        </div>
        <div class="reviews-grid">
            <?php if ($reviews && $reviews->num_rows > 0): ?>
                <?php while ($r = $reviews->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-comment">"<?php echo htmlspecialchars($r['comment']); ?>"</p>
                        <strong class="review-user"><?php echo htmlspecialchars($r['user_name']); ?></strong>
                        <span class="review-product">Bought <?php echo htmlspecialchars($r['product_name'] ?? 'TeeCraft T-Shirt'); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section> 
    <section>
        <div class="section-title">
            <h2>Frequently Asked Questions</h2>
            <p>Everything you need to know about our quality and fitting</p>
        </div>
        <div class="faq-container">
            <div class="faq-item">
                <h4>What fabric quality do you use?</h4>
                <p>We strictly use 200 GSM to 240 GSM 100% combed ringspun cotton. It provides a heavy, structured look while remaining breathable.</p>
            </div>
            <div class="faq-item">
                <h4>How does the oversized fit work?</h4>
                <p>Our oversized tees feature dropped shoulders and a wider chest cut. We recommend ordering your true size for an intentional relaxed oversized fit.</p>
            </div>
            <div class="faq-item">
                <h4>What are the wash care instructions?</h4>
                <p>Wash inside-out in cold water and air dry in shade. Avoid direct iron on screen prints.</p>
            </div>
        </div>
    </section>
    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4>👕 TEECRAFT</h4>
                <p class="footer-description">Premium streetwear brand focused on high-density cotton, aesthetic drops, and relaxed fits.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="products.php">All T-Shirts</a></li>
                    <li><a href="cart.php">Shopping Cart</a></li>
                    <li><a href="admin/index.php">Admin Panel</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Customer Support</h4>
                <ul>
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; TeeCraft. All rights reserved. | 
            <a href="admin/index.php" class="admin-link">Admin Panel</a>
        </div>
    </footer>
</body>
</html>