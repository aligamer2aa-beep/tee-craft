
<?php
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fafafa;
            margin: 0;
            color: #1e293b;
        }
        .orders-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .orders-logo {
            font-size: 1.6rem;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
        }
        .logout-link {
            color: #dc2626;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .orders-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #0f172a;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .orders-title {
            color: #0f172a;
            margin-bottom: 1.5rem;
        }
        .order-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 0.8rem;
            margin-bottom: 1rem;
        }
        .order-id {
            font-weight: bold;
            color: #0f172a;
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .status-completed {
            background: #dcfce7;
            color: #15803d;
        }
        .order-details p {
            margin: 0.3rem 0;
            font-size: 0.9rem;
            color: #475569;
        }
        .empty-orders {
            background: #fff;
            padding: 3rem;
            text-align: center;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            color: #64748b;
        }
        .empty-orders h3 {
            margin-top: 0;
            color: #334155;
        }
        .shop-button {
            display: inline-block;
            margin-top: 1rem;
            background: #000;
            color: #fff;
            padding: 0.7rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="orders-navbar">
        <a href="index.php" class="orders-logo"> 👕 TEECRAFT</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </header>
    <main class="orders-container">
        <a href="index.php" class="back-link">&larr; Continue Shopping</a>
        <h2 class="orders-title">My Order History</h2>
        <?php if ($orders && $orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <section class="order-card">
                    <div class="order-header">
                        <span class="order-id">
                            Order #<?php echo $order['id']; ?>
                        </span>
                        <span class="status-badge <?php echo ($order['status'] === 'Completed') ? 'status-completed' : 'status-pending'; ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <p>
                            <strong>Total Amount:</strong>
                            $<?php echo number_format($order['total_price'], 2); ?>
                        </p>
                        <p>
                            <strong>Shipping Address:</strong>
                            <?php echo htmlspecialchars($order['address']); ?>
                        </p>
                        <p>
                            <strong>Phone:</strong>
                            <?php echo htmlspecialchars($order['phone']); ?>
                        </p>
                        <p>
                            <strong>Date:</strong>
                            <?php echo htmlspecialchars($order['created_at']); ?>
                        </p>
                    </div>
                </section>
            <?php endwhile; ?>
        <?php else: ?>
            <section class="empty-orders">
                <h3>Aapne abhi tak koi order place nahi kiya!</h3>
                <p> Hamari latest collection check karein aur apna pehla order banayein.</p>
                <a href="products.php" class="shop-button"> Shop Now</a>
            </section>
        <?php endif; ?>
    </main>

</body>
</html>

