<?php
include 'config.php';

$order_id = $_GET['id'];

// Fetch the order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id";
$order_result = $conn->query($order_sql);
$order = $order_result->fetch_assoc();

// Fetch the order items
$items_sql = "SELECT * FROM orderItems WHERE order_id = $order_id";
$items_result = $conn->query($items_sql);
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
    <title>Edit Order</title>
</head>
<body>
    <h1>Edit Order</h1>
    <form action="edit_order.php?id=<?php echo $order_id; ?>" method="POST">
        <label for="customer_name">Customer Name:</label><br>
        <input type="text" id="customer_name" name="customer_name" value="<?php echo $order['customer_name']; ?>" required><br><br>

        <label for="order_date">Order Date:</label><br>
        <input type="date" id="order_date" name="order_date" value="<?php echo $order['order_date']; ?>" required><br><br>

        <h3>Order Items</h3>
        <div id="orderItems">
            <?php foreach ($orderItems as $index => $item): ?>
                <div class="order-item">
                    <input type="hidden" name="item_id[]" value="<?php echo $item['id']; ?>">
                    <label>Product Name:</label>
                    <input type="text" name="product_name[]" value="<?php echo $item['product_name']; ?>" required>
                    <label>Quantity:</label>
                    <input type="number" name="quantity[]" value="<?php echo $item['quantity']; ?>" min="1" required>
                    <label>Price:</label>
                    <input type="number" name="price[]" value="<?php echo $item['price']; ?>" step="0.01" required>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" onclick="addItem()">Add Another Item</button><br><br>
        <input type="submit" value="Update Order">
    </form>

    <script>
        function addItem() {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'order-item';
            itemDiv.innerHTML = `
                <input type="hidden" name="item_id[]" value="">
                <label>Product Name:</label>
                <input type="text" name="product_name[]" required>
                <label>Quantity:</label>
                <input type="number" name="quantity[]" min="1" required>
                <label>Price:</label>
                <input type="number" name="price[]" step="0.01" required>
            `;
            document.getElementById('orderItems').appendChild(itemDiv);
        }
    </script>
</body>
</html>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $order_date = $_POST['order_date'];
    $item_ids = $_POST['item_id'];
    $product_names = $_POST['product_name'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];

    // Update the order
    $sql = "UPDATE orders SET customer_name = '$customer_name', order_date = '$order_date' WHERE id = $order_id";
    if ($conn->query($sql) === TRUE) {
        // Update or insert each item
        for ($i = 0; $i < count($product_names); $i++) {
            $item_id = $item_ids[$i];
            $product_name = $product_names[$i];
            $quantity = $quantities[$i];
            $price = $prices[$i];

            if (!empty($item_id)) {
                // Update existing item
                $sql = "UPDATE orderItems SET product_name = '$product_name', quantity = $quantity, price = $price WHERE id = $item_id";
            } else {
                // Insert new item
                $sql = "INSERT INTO orderItems (order_id, product_name, quantity, price) VALUES ($order_id, '$product_name', $quantity, $price)";
            }
            $conn->query($sql);
        }

        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>