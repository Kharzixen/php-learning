<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['id'];
    $semaphore_status = $_POST['semaphore_status'];

    // Update the semaphore status
    $sql = "UPDATE orders SET semaphore_status = '$semaphore_status' WHERE id = $order_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Semaphore status updated successfully.";
    } else {
        echo "Error updating semaphore status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
