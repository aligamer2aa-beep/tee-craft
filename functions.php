<?php
require_once __DIR__ . '/db.php';
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($input)));
}
function check_user_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
function check_admin_login() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../admin/login.php");
        exit();
    }
}
function get_categories() {
    global $conn;
    return $conn->query("SELECT * FROM categories ORDER BY name ASC");
}
function get_cart_count() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    }
    return 0;
}
?>