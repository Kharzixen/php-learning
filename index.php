<?php
session_start();
include 'config.php';

// Check user role
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders
$sql = "SELECT id, customer_name, order_date, semaphore_status FROM orders ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php include 'menu.php'; ?>
        <div class="main-content">
           <div class="table-container">
           <h1>Orders</h1>
            <table border="1">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Semaphore</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $semaphoreClass = $row['semaphore_status'] == 'green' ? 'semaphore-green' : 'semaphore-red';
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['customer_name']}</td>
                                <td>{$row['order_date']}</td>
                                <td><span class='semaphore {$semaphoreClass}' data-id='{$row['id']}'></span></td>
                                <td><a href='order_details.php?id={$row['id']}'>View Items</a>";
                        if ($_SESSION['role'] != 'viewer') {
                            echo " | <a href='edit_order.php?id={$row['id']}'>Edit</a>";
                        }
                    echo "</td>
                        </tr>";
                        }
                } else {
                    echo "<tr><td colspan='5'>No orders found</td></tr>";
                }
                ?>
            </table>
            <br>
            <div class="button-row">
                <?php if ($_SESSION['role'] != 'viewer'): ?>
                    <a class="custom-button" href="add_order.php">Add New Order</a>
                <?php endif; ?>
            </div>


           </div>
    </div>
    <script>
        document.querySelectorAll('.semaphore').forEach(span => {
            span.addEventListener('click', function() {
                if (<?php echo json_encode($_SESSION['role'] === 'admin' || $_SESSION['role'] == "editor"); ?>) {
                    const semaphore = this;
                    const orderId = semaphore.getAttribute('data-id');
                    const currentStatus = semaphore.classList.contains('semaphore-green') ? 'green' : 'red';
                    const newStatus = currentStatus === 'green' ? 'red' : 'green';

                    fetch('update_semaphore.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${orderId}&semaphore_status=${newStatus}`
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.includes('updated successfully')) {
                            semaphore.classList.toggle('semaphore-red');
                            semaphore.classList.toggle('semaphore-green');
                        } else {
                            alert('Error updating semaphore status');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                } else {
                    alert('You do not have permission to change the semaphore status.');
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
