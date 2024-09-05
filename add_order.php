<?php
session_start();
include 'config.php'; // Include database connection

$title = 'Add Order'; // Page title
?>

<?php
include 'config.php';

if ($_SESSION['role'] != 'editor' && $_SESSION['role'] != 'admin' ){
    header("Location: index.php");
    exit;
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $customer_name = $_POST['customer_name'];
        $order_date = $_POST['order_date'];
        $product_names = $_POST['product_name'];
        $quantities = $_POST['quantity'];
        $prices = $_POST['price'];

        $sql = "INSERT INTO orders (customer_name, order_date) VALUES ('$customer_name', '$order_date')";
        if ($conn->query($sql) === TRUE) {
            $order_id = $conn->insert_id;

            for ($i = 0; $i < count($product_names); $i++) {
                $product_name = $product_names[$i];
                $quantity = $quantities[$i];
                $price = $prices[$i];
                $sql = "INSERT INTO orderItems (order_id, product_name, quantity, price) VALUES ($order_id, '$product_name', $quantity, $price)";
                $conn->query($sql);
            }

            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php include 'menu.php'; ?>
        <div class="main-content">
            <h1>Add New Order</h1>
            <form action="add_order.php" method="POST">
                <label for="customer_name">Customer Name:</label><br>
                <input type="text" id="customer_name" name="customer_name" required><br><br>

                <label for="order_date">Order Date:</label><br>
                <input type="date" id="order_date" name="order_date" required><br><br>

                <h3>Order Items</h3>
                <div id="orderItems">
                    <div class="order-item">
                        <label>Product Name:</label>
                        <input type="text" name="product_name[]" required>
                        <label>Quantity:</label>
                        <input type="number" name="quantity[]" min="1" required>
                        <label>Price:</label>
                        <input type="number" name="price[]" step="0.01" required>
                        <button type="button" onclick="removeItem(this)">Remove Item</button><br><br>
                    </div>
                </div>

                <button type="button" onclick="addItem()">Add Another Item</button><br><br>
                <input type="submit" value="Add Order">
            </form>

            <script>
                function addItem() {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'order-item';
                    itemDiv.innerHTML = `
                        <label>Product Name:</label>
                        <input type="text" name="product_name[]" required>
                        <label>Quantity:</label>
                        <input type="number" name="quantity[]" min="1" required>
                        <label>Price:</label>
                        <input type="number" name="price[]" step="0.01" required>
                        <button type="button" onclick="removeItem(this)">Remove Item</button><br><br>
                    `;
                    document.getElementById('orderItems').appendChild(itemDiv);
                }

                function removeItem(button) {
                    const itemDiv = button.parentNode;
                    itemDiv.remove();
                }
            </script>
        </div>
    </div>
</body>
</html>
