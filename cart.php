<?php
require_once 'functions.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($action === 'add' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            $product = $result->fetch_assoc();
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'quantity' => 1
            ];
        }
    }
    $stmt->close();
    header("Location: cart.php");
    exit();
}
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $qty) {
            $pid = intval($pid);
            $qty = intval($qty);
            if ($qty > 0 && isset($_SESSION['cart'][$pid])) {
                $_SESSION['cart'][$pid]['quantity'] = $qty;
            } elseif ($qty <= 0) {
                unset($_SESSION['cart'][$pid]);
            }
        }
    }
    header("Location: cart.php");
    exit();
}
if ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="cart-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TeeCraft</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
         .btn { 
            background: #0f172a; 
            color: #fff; 
            padding: 0.8rem 1.5rem; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: bold; 
            border: none; 
            cursor: pointer; 
            display: inline-block; 
            font-size: 0.95rem; 
        }
        .btn:hover { 
            background: #1e293b; 
        }
        .btn-update { 
            background: #475569; 
            margin-right: 10px; 
        }
        .btn-update:hover { 
            background: #334155; 
        }
        .btn-remove { 
            color: #dc2626; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 0.9rem; 
        }
        .btn-back { 
            display: inline-block; 
            margin-bottom: 1rem; 
            color: #64748b; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem; 
        }
        .cart-body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f8fafc; 
            margin: 0; 
            padding: 20px; 
            color: #1e293b; 
        }
        .cart-container { 
            max-width: 850px;
            margin: 30px auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .cart-heading-main { 
            margin-top: 0;
            color: #0f172a;
            margin-bottom: 1.5rem; 
        }
        .cart-table {  
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem; 
        }
        .cart-th, .cart-td { 
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
        }
        .cart-th { 
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
        }
        .cart-product-img { 
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .cart-input-number {
            width: 60px;
            padding: 0.4rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
            font-size: 0.95rem;
        }
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .total-box { 
            font-size: 1.2rem; 
            font-weight: bold; 
            text-align: right; 
            margin-bottom: 1.5rem; 
        }
        .cart-empty-wrapper {
            text-align: center; 
            padding: 3rem; 
            color: #64748b;
        }
        .cart-heading-sub {
            margin: 0 0 0.5rem 0;
        }
        .cart-empty-text {
            margin: 0 0 1rem 0;
        }  
    </style>
</head>
<body class="cart-body">
    <div class="cart-container">
        <a href="index.php" class="btn-back">&larr; Continue Shopping</a>
        <h2 class="cart-heading-main">Your Shopping Cart 🛒</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <form action="cart.php?action=update" method="POST" class="cart-form">
                <table class="cart-table">
                    <thead class="cart-thead">
                        <tr class="cart-tr">
                            <th class="cart-th">Product</th>
                            <th class="cart-th">Price</th>
                            <th class="cart-th">Quantity</th>
                            <th class="cart-th">Subtotal</th>
                            <th class="cart-th">Action</th>
                        </tr>
                    </thead>
                    <tbody class="cart-tbody">
                        <?php 
                        $grand_total = 0;
                        foreach ($_SESSION['cart'] as $pid => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $grand_total += $subtotal;
                        ?>
                        <tr class="cart-tr">
                            <td class="cart-td cart-product-cell">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="" class="cart-product-img">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td class="cart-td">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="cart-td">
                                <input type="number" name="quantities[<?php echo $pid; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="cart-input-number">
                            </td>
                            <td class="cart-td">$<?php echo number_format($subtotal, 2); ?></td>
                            <td class="cart-td">
                                <a href="cart.php?action=remove&id=<?php echo $pid; ?>" class="btn-remove">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-box">
                    Grand Total: $<?php echo number_format($grand_total, 2); ?>
                </div>
                <div class="actions-bar">
                    <button type="submit" class="btn btn-update">Update Cart</button>
                    <a href="checkout.php" class="btn">Proceed to Checkout &rarr;</a>
                </div>
            </form>
        <?php else: ?>
            <div class="cart-empty-wrapper">
                <h3 class="cart-heading-sub">Aapka cart khaali hai!</h3>
                <p class="cart-empty-text">Kuch behtareen t-shirts select karein aur cart mein add karein.</p><br>
                <a href="index.php" class="btn">Shop Now</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>