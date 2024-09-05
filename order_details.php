<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'];

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();

// Fetch order items
$items_sql = "SELECT * FROM orderItems WHERE order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$orderItems = [];
while ($row = $items_result->fetch_assoc()) {
    $orderItems[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php include 'menu.php'; ?>
        <div class="main-content">
            <h1>Order Details</h1>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            <h2>Order Items</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Item ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                    <?php
                    if (count($orderItems) > 0) {
                        foreach ($orderItems as $item) {
                            echo "<tr>
                                    <td>{$item['id']}</td>
                                    <td>{$item['product_name']}</td>
                                    <td>{$item['quantity']}</td>
                                    <td>{$item['price']}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No items found</td></tr>";
                    }
                    ?>
                </table>
            </div>
            
            <br>
            <div class="button-row">
            <?php if ($_SESSION['role'] != 'viewer'): ?>
                <a class="custom-button" href="edit_order.php?id=<?php echo htmlspecialchars($order['id']); ?>">Edit Order</a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] != 'viewer'): ?>
                <a class="custom-button" href="edit_order.php?id=<?php echo htmlspecialchars($order['id']); ?>">Edit Order</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$order_stmt->close();
$items_stmt->close();
$conn->close();
?>
