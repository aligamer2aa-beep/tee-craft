<?php
require_once 'functions.php';
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = sanitize($_POST['email']);
    $password = $_POST['password'];
    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Ghalat password! Dobara koshish karein.";
            }
        } else {
            $error = "Is email par koi account nahi mila.";
        }
        $stmt->close();
    } else {
        $error = "Email aur Password dono fill karein.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        h2 { 
            margin-top: 0; 
            color: #0f172a; 
            text-align: center; 
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f8fafc; 
            margin: 0; 
            padding: 0; 
        }
        label { 
            display: block; 
            font-weight: bold; 
            font-size: 0.85rem; 
            margin-bottom: 0.4rem; 
            color: #475569; 
        }
        input { 
            width: 100%; 
            padding: 0.7rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }
        .forms-card { 
            max-width: 400px; 
            margin: 5rem auto; 
            background: #fff; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }
        .inputs-group { 
            margin-bottom: 1.2rem; 
        }
        .btn-submit { 
            width: 100%; 
            background: #000; 
            color: #fff; 
            border: none; 
            padding: 0.8rem; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 1rem; 
            margin-top: 0.5rem; 
        }
        .alert-error { 
            background: #fee2e2; 
            color: #991b1b; 
            padding: 0.8rem; 
            border-radius: 6px; 
            margin-bottom: 1rem; 
            font-size: 0.9rem; 
        }
        .footer-linkr { 
            text-align: center; 
            margin-top: 1.5rem; 
            font-size: 0.9rem; 
            color: #64748b; 
        }
        .footer-linkr a { 
            color: #000; 
            font-weight: bold; 
            text-decoration: none; 
        }
    </style>
</head>
<body>
    <div class="forms-card">
        <h2>Sign In</h2>
        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="inputs-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>
            <div class="inputs-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" name="login" class="btn-submit">Login &rarr;</button>
        </form>
        <div class="footer-linkr">
            Account nahi hai? <a href="register.php">Naya Account Banayein</a>
        </div>
        <div class="footer-linkr">
           Wapis jana ha? <a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>