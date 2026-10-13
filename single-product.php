<?php
require_once 'functions.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "
        <div class='product-not-found'>
            <h2>Product nahi mila!</h2>
            <a href='index.php'>Wapas jayein</a>
        </div>
    ";
    exit();
}
$product = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($product['name']); ?> - TeeCraft
    </title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }
        .product-page {
            max-width: 900px;
            margin: 0 auto;
        }
        .product-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .product-back:hover {
            color: #0f172a;
        }
        .product-card {
            max-width: 900px;
            margin: 20px auto 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            overflow: hidden;
        }
        .product-image {
            flex: 1;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .product-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .product-info {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .product-category {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        .product-title {
            margin: 0 0 1rem;
            font-size: 1.8rem;
            color: #0f172a;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 1rem;
        }
        .product-description {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .product-meta {
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 1.5rem;
        }
        .product-meta div {
            margin-bottom: 0.4rem;
        }
        .product-meta span {
            font-weight: bold;
        }
        .cart-button {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 0.9rem 1.5rem;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }
        .cart-button:hover {
            background: #1e293b;
        }
        .product-not-found {
            text-align: center;
            margin-top: 50px;
            font-family: sans-serif;
        }
        .product-not-found h2 {
            margin-bottom: 10px;
        }
        .product-not-found a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="product-page">
        <a href="index.php" class="product-back">  &larr; Back to Shop </a>
    </div>
    <div class="product-card">
        <div class="product-image">
            <img
                src="<?php echo htmlspecialchars($product['image_url']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
            >
        </div>
        <div class="product-info">
            <div class="product-category">
                <?php echo htmlspecialchars($product['category_name'] ?? 'T-Shirt'); ?>
            </div>
            <h1 class="product-title">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>
            <div class="product-price">
                $<?php echo number_format($product['price'], 2); ?>
            </div>
            <div class="product-description">
                <?php
                echo nl2br(
                    htmlspecialchars(
                        $product['description'] ?? 'No description available.'
                    )
                );
                ?>
            </div>
            <div class="product-meta">
                <div> Size:
                    <span><?php echo htmlspecialchars($product['size']); ?></span>
                </div>
                <div>Color:
                    <span><?php echo htmlspecialchars($product['color']); ?></span>
                </div>
                <div>Stock:
                    <span> <?php echo $product['stock_quantity']; ?> available</span>
                </div>
            </div>
            <a
                href="cart.php?action=add&id=<?php echo $product['id']; ?>"
                class="cart-button"
            > Add to Cart 🛒 </a>
         </div>
    </div>
</body>
</html>

