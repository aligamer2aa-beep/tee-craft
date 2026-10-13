<?php
require_once 'functions.php';
check_user_login();
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed - TeeCraft</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin-top: 4rem;
            text-align: center;
        }
        .confirmation-box {
            background: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .confirmation-title {
            color: #198754;
            margin-bottom: 0.5rem;
        }
        .confirmation-order-id {
            color: #6c757d;
        }
        .confirmation-message {
            margin-top: 1rem;
        }
        .confirmation-buttons {
            margin-top: 2rem;
        }
        .continue-shopping {
            background: #6c757d;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="container confirmation-container">
        <div class="confirmation-box">
            <h1 class="confirmation-title">🎉 Order Placed Successfully!</h1>
            <p class="confirmation-order-id">Your Order ID is<strong>#<?php echo $order_id; ?></strong></p>
            <p class="confirmation-message">
                Thank you for shopping with TeeCraft!
                We will process and ship your T-Shirt order soon.
            </p>
            <div class="confirmation-buttons">
                <a href="my-orders.php" class="btn btn-primary"> View My Orders</a>
                <a href="products.php" class="btn continue-shopping"> Continue Shopping</a>
            </div>
        </div>
    </main>
</body>
</html>
