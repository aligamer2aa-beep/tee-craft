<?php
require_once 'functions.php';
check_user_login();
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, COUNT(oi.id) as total_items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .orders-container {
            max-width: 900px;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }
        .orders-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        .orders-table-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .orders-table th {
            padding: 1rem;
            background-color: #212529;
            color: #fff;
        }
        .orders-table td {
            padding: 1rem;
        }
        .orders-table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        .order-id {
            font-weight: bold;
        }
        .order-total {
            font-weight: bold;
            color: #0d6efd;
        }
        .order-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .status-badge {
            color: #fff;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending {
            background-color: #0d6efd;
        }
        .status-processing {
            background-color: #ffc107;
        }
        .status-shipped {
            background-color: #0dcaf0;
        }
        .status-delivered {
            background-color: #198754;
        }
        .status-cancelled {
            background-color: #dc3545;
        }
        .empty-orders {
            background: #fff;
            padding: 3rem;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .empty-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .explore-button {
            padding: 0.7rem 1.5rem;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"> 👕 TeeCraft</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Shop T-Shirts</a></li>
            <li><a href="cart.php">Cart (<?php echo get_cart_count(); ?>)</a></li>
            <li><a href="my-orders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </header>
    <main class="container orders-container">
        <h2>📦 My Order History</h2>
        <p class="orders-description">Aapke tamam past orders aur unki delivery status yahan dekhein.</p>
        <?php if ($orders && $orders->num_rows > 0): ?>
            <div class="orders-table-box">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <?php
                            $status = $order['status'];
                            $status_class = 'status-pending';

                            if ($status === 'Processing') {
                                $status_class = 'status-processing';
                            } elseif ($status === 'Shipped') {
                                $status_class = 'status-shipped';
                            } elseif ($status === 'Delivered') {
                                $status_class = 'status-delivered';
                            } elseif ($status === 'Cancelled') {
                                $status_class = 'status-cancelled';
                            }
                            ?>
                            <tr>
                                <td class="order-id"> #<?php echo $order['id']; ?></td>
                                <td> <?php echo $order['total_items']; ?> Item(s)</td>

                                <td class="order-total"> $<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="order-date">
                                    <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-orders">
                <p class="empty-message"> Aapne abhi tak koi order place nahi kiya hai.</p>
                <a href="products.php" class="btn btn-primary explore-button">Explore T-Shirts</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

