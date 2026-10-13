<?php
require_once 'functions.php';
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    header("Location: index.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .order-container {
            max-width: 600px;
            margin: 4rem auto;
            padding: 0 1rem;
            text-align: center;
        }
        .order-box {
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .order-title {
            color: #198754;
            margin-bottom: 0.5rem;
        }
        .order-message {
            color: #64748b;
            margin-bottom: 2rem;
        }
        .order-details {
            background: #f8fafc;
            padding: 1.2rem;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.8;
        }
        .total-amount {
            font-weight: bold;
            color: #0d6efd;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }
        .shopping-button {
            display: inline-block;
            background: #000;
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: bold;
        }
        .shopping-button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <main class="container order-container">
        <div class="order-box">
            <div class="success-icon">🎉</div>
            <h1 class="order-title">Order Successfully Placed! </h1>
            <p class="order-message">
                Aapka order receive ho gaya hai.
                Hamari team jald hi delivery process shuru karegi.
            </p>
            <div class="order-details">
                <div>
                    <strong>Order ID:</strong>
                    #<?php echo $order['id']; ?>
                </div>
                <div>
                    <strong>Name:</strong>
                    <?php echo htmlspecialchars($order['customer_name']); ?>
                </div>
                <div>
                    <strong>Phone:</strong>
                    <?php echo htmlspecialchars($order['phone']); ?>
                </div>
                <div>
                    <strong>Delivery Address:</strong>
                    <?php echo htmlspecialchars($order['address'] . ', ' . $order['city']); ?>
                </div>
                <div>
                    <strong>Payment Method:</strong>
                    <?php echo htmlspecialchars($order['payment_method']); ?>
                </div>
                <div class="total-amount">
                    Total Payable:
                    $<?php echo number_format($order['total_amount'], 2); ?>
                </div>
            </div>
            <a href="products.php" class="shopping-button">Continue Shopping &rarr;</a>
        </div>
    </main>
</body>
</html>

