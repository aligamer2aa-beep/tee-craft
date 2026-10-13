<?php
require_once 'functions.php';
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}
$cart_items = [];
$total_amount = 0;
foreach ($_SESSION['cart'] as $key => $item) {
    $key_parts  = explode('_', (string)$key);
    $product_id = intval($key_parts[0]);
    if (is_array($item)) {
        $qty   = intval($item['quantity']);
        $size  = $item['size'] ?? '';
        $color = $item['color'] ?? '';
    } else {
        $qty   = intval($item);
        $size  = '';
        $color = '';
    }
    if ($product_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $product  = $res->fetch_assoc();
            $subtotal = $product['price'] * $qty;
            $total_amount += $subtotal;
            $cart_items[] = [
                'product_id' => $product_id,
                'name'       => $product['name'],
                'price'      => $product['price'],
                'quantity'   => $qty,
                'subtotal'   => $subtotal,
                'size'       => $size,
                'color'      => $color
            ];
        }
        $stmt->close();
    }
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name    = sanitize($_POST['customer_name']);
    $email   = sanitize($_POST['email']);
    $phone   = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city    = sanitize($_POST['city']);
    $payment = sanitize($_POST['payment_method']);
    if (!empty($name) && !empty($phone) && !empty($address) && !empty($city)) {
        $stmt_order = $conn->prepare("INSERT INTO orders (customer_name, email, phone, address, city, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_order->bind_param("sssssds", $name, $email, $phone, $address, $city, $total_amount, $payment);
        if ($stmt_order->execute()) {
            $order_id = $stmt_order->insert_id;
            $stmt_order->close();
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt_item->bind_param("iiidss", $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size'], $item['color']);
                $stmt_item->execute();
            }
            $stmt_item->close();
            unset($_SESSION['cart']);
            header("Location: order-success.php?id=" . $order_id);
            exit();
        } else {
            $error = "Order place karne mein masla hua. Dobara koshish karein.";
        }
    } else {
        $error = "Tamam zaroori fields ko fill karein.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="checkout-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .checkout-body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0;
            padding: 0;
        }
        .checkout-navbar { 
            background: #fff; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            padding: 1rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .checkout-logo { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: #1e293b; 
            text-decoration: none; 
        }
        .checkout-nav-links { 
            display: flex; 
            list-style: none; 
            gap: 1.5rem; 
            margin: 0; 
            padding: 0; 
        }
        .checkout-nav-link { 
            text-decoration: none; 
            color: #475569; 
            font-weight: 600; 
        }
        .checkout-nav-link-active { 
            text-decoration: none; 
            color: #0d6efd; 
            font-weight: 600; 
        }
        .checkout-container { 
            max-width: 1000px; 
            margin: 2rem auto; 
            padding: 0 1rem; 
        }
        .checkout-heading-main { 
            color: #0f172a; 
            margin-bottom: 1.5rem; 
        }
        .checkout-error-box { 
            background: #f8d7da; 
            color: #842029; 
            padding: 0.8rem; 
            border-radius: 6px; 
            margin-bottom: 1rem; 
        }
        .checkout-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 2rem; 
        }
        .checkout-card { 
            background: #fff; 
            padding: 1.8rem; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .checkout-card-summary { 
            background: #fff; 
            padding: 1.8rem; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            height: fit-content; 
        }
        .checkout-heading-sub { 
            margin-top: 0; 
            color: #0f172a; 
            margin-bottom: 1.2rem; 
        }
        .checkout-form-group { 
            margin-bottom: 1rem; 
        }
        .checkout-label { 
            display: block; 
            font-size: 0.85rem; 
            font-weight: bold; 
            margin-bottom: 0.3rem; 
        }
        .checkout-input { 
            width: 100%; 
            padding: 0.6rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }
        .checkout-select { 
            width: 100%; 
            padding: 0.6rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            background: #fff; 
            box-sizing: border-box; 
        }
        .checkout-textarea { 
            width: 100%; 
            padding: 0.6rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }
        .checkout-form-row { 
            display: flex; 
            gap: 1rem; 
            margin-bottom: 1rem; 
        }
        .checkout-form-col { 
            flex: 1; 
        }
        .checkout-submit-btn { 
            width: 100%; 
            background: #000; 
            color: #fff; 
            border: none; 
            padding: 0.8rem; 
            border-radius: 6px; 
            font-weight: bold; 
            font-size: 1rem; 
            cursor: pointer; 
        }
        .checkout-summary-items { 
            border-bottom: 1px solid #e2e8f0; 
            padding-bottom: 1rem; 
            margin-bottom: 1rem; 
        }
        .checkout-summary-item-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 0.8rem; 
            font-size: 0.9rem; 
        }
        .checkout-summary-item-meta { 
            color: #64748b; 
            font-size: 0.8rem; 
        }
        .checkout-summary-item-price { 
            font-weight: bold; 
            color: #0f172a; 
        }
        .checkout-summary-line { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 0.5rem; 
            color: #64748b; 
            font-size: 0.9rem; 
        }
        .checkout-summary-delivery { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 1rem; 
            color: #64748b; 
            font-size: 0.9rem; 
        }
        .checkout-summary-free { 
            color: #198754; 
            font-weight: bold; 
        }
        .checkout-summary-total { 
            display: flex; 
            justify-content: space-between; 
            border-top: 2px dashed #cbd5e1; 
            padding-top: 1rem; 
            font-size: 1.2rem; 
            font-weight: bold; 
            color: #0d6efd; 
        }
    </style>
</head>
<body class="checkout-body">
    <header class="checkout-navbar">
        <a href="index.php" class="checkout-logo">👕 TeeCraft</a>
        <ul class="checkout-nav-links">
            <li><a href="index.php" class="checkout-nav-link">Home</a></li>
            <li><a href="products.php" class="checkout-nav-link">Shop T-Shirts</a></li>
            <li><a href="cart.php" class="checkout-nav-link-active">Cart (<?php echo get_cart_count(); ?>)</a></li>
        </ul>
    </header>
    <div class="checkout-container">
        <h2 class="checkout-heading-main">Checkout & Delivery Details</h2>
        <?php if (!empty($error)): ?>
            <div class="checkout-error-box">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <div class="checkout-grid">
            <div class="checkout-card">
                <h3 class="checkout-heading-sub">Shipping Address</h3>                
                <form action="checkout.php" method="POST">
                    <div class="checkout-form-group">
                        <label class="checkout-label">Full Name *</label>
                        <input type="text" name="customer_name" required class="checkout-input">
                    </div>
                    <div class="checkout-form-row">
                        <div class="checkout-form-col">
                            <label class="checkout-label">Phone Number *</label>
                            <input type="text" name="phone" placeholder="03001234567" required class="checkout-input">
                        </div>
                        <div class="checkout-form-col">
                            <label class="checkout-label">Email</label>
                            <input type="email" name="email" class="checkout-input">
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <label class="checkout-label">City *</label>
                        <input type="text" name="city" required placeholder="Lahore, Karachi, Islamabad..." class="checkout-input">
                    </div>
                    <div class="checkout-form-group">
                        <label class="checkout-label">Complete Address *</label>
                        <textarea name="address" rows="3" required placeholder="House #, Street #, Sector/Area..." class="checkout-textarea"></textarea>
                    </div>
                    <div class="checkout-form-group" style="margin-bottom: 1.5rem;">
                        <label class="checkout-label">Payment Method</label>
                        <select name="payment_method" class="checkout-select">
                            <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                            <option value="Bank Transfer">Bank Transfer / JazzCash</option>
                        </select>
                    </div>
                    <button type="submit" name="place_order" class="checkout-submit-btn">
                        Confirm & Place Order &rarr;
                    </button>
                </form>
            </div>
            <div class="checkout-card-summary">
                <h3 class="checkout-heading-sub">Order Summary</h3>               
                <div class="checkout-summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="checkout-summary-item-row">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <div class="checkout-summary-item-meta">
                                    Qty: <?php echo $item['quantity']; ?>
                                    <?php echo $item['size'] ? ' | Size: ' . $item['size'] : ''; ?>
                                </div>
                            </div>
                            <span class="checkout-summary-item-price">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="checkout-summary-line">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($total_amount, 2); ?></span>
                </div>
                <div class="checkout-summary-delivery">
                    <span>Delivery Charges:</span>
                    <span class="checkout-summary-free">FREE</span>
                </div>
                <div class="checkout-summary-total">
                    <span>Total Amount:</span>
                    <span>$<?php echo number_format($total_amount, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>