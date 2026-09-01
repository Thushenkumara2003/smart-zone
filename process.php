<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'smartzone_db';

$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$dbSql = "CREATE DATABASE IF NOT EXISTS `$database`";
if (!$conn->query($dbSql)) {
    die('Database creation failed: ' . $conn->error);
}

$conn->select_db($database);

$tableSql = "CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `product_type` VARCHAR(255) NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($tableSql)) {
    die('Table creation failed: ' . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $productType = trim($_POST['product_type'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');

    if ($customerName === '' || $customerEmail === '' || $productType === '' || $paymentMethod === '') {
        echo "<script>alert('Please fill in all fields.'); window.location.href='sell.html';</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO `orders` (`customer_name`, `customer_email`, `product_type`, `payment_method`, `order_date`) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param('ssss', $customerName, $customerEmail, $productType, $paymentMethod);

    if ($stmt->execute()) {
        echo "<script>alert('Order Placed Successfully!'); window.location.href='sell.html';</script>";
    } else {
        echo "<script>alert('Order failed. Please try again.'); window.location.href='sell.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
