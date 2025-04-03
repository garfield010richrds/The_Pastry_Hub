<?php
include 'db_connection.php'; // Ensure database connection is established

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $order_id = trim($_POST['order_id']);
    $user_id = trim($_POST['user_id']);
    $total_price = trim($_POST['total_price']);

    // Insert data into the orders table (order_date will be set automatically)
    $sql = "INSERT INTO orders (order_id, user_id, total_price) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $order_id, $user_id, $total_price);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Order placed successfully!", "order_id" => $order_id]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Database preparation error."]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
