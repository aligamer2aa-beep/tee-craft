<?php
require_once 'functions.php';
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name     = sanitize($_POST['full_name']);
    $email    = sanitize($_POST['email']);
    $phone    = sanitize($_POST['phone']);
    $password = $_POST['password'];
    if (!empty($name) && !empty($email) && !empty($password)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows > 0) {
            $error = "Yeh email pehle se registered hai! Login karein.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, phone, password)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $phone,
                $hashed_password
            );
            if ($stmt->execute()) {
                $success = "Account successfully create ho gaya! Ab aap login kar sakte hain.";
            } else {
                $error = "Registration mein error aaya. Dobara koshish karein.";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $error = "Tamam zaroori details fill karein.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .register-card {
            max-width: 420px;
            margin: 4rem auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .register-title {
            margin-top: 0;
            color: #0f172a;
            text-align: center;
        }
        .register-group {
            margin-bottom: 1.2rem;
        }
        .register-label {
            display: block;
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            color: #475569;
        }
        .register-input {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .register-button {
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
        .register-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .register-success {
            background: #dcfce7;
            color: #166534;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }
        .register-footer a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2 class="register-title">Register Account</h2>
        <?php if (!empty($error)): ?>
            <div class="register-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="register-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="register-group">
                <label class="register-label">Full Name *</label>
                <input
                    type="text"
                    name="full_name"
                    class="register-input"
                    required
                    placeholder="e.g. Ali Raza"
                >
            </div>
            <div class="register-group">
                <label class="register-label">Email Address *</label>
                <input
                    type="email"
                    name="email"
                    class="register-input"
                    required
                    placeholder="name@example.com"
                >
            </div>
            <div class="register-group">
                <label class="register-label">Phone Number</label>
                <input type="text" name="phone"class="register-input"placeholder="03001234567">
            </div>
            <div class="register-group">
                <label class="register-label">Password *</label>
                <input type="password" name="password" class="register-input" required placeholder="••••••••">
            </div>
            <button  type="submit" name="register" class="register-button">Create Account &rarr;</button>
        </form>
        <div class="register-footer">Pehle se account hai?<a href="login.php">Sign In karein</a>
        </div>
        <div class="register-footer">--Back to home--<a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>

