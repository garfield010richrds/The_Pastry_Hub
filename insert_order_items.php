<?php
include 'db_connection.php'; // Ensure database connection is established

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $user_id = intval($_POST['user_id']);
    $cart_items = json_decode($_POST['cart_items'], true);

    // Validate input
    if (!$order_id || !$user_id || empty($cart_items)) {
        echo json_encode(["status" => "error", "message" => "Invalid order data."]);
        exit;
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    // Loop through cart items and insert into order_items table
    foreach ($cart_items as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);

        if ($product_id) {
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode(["status" => "success", "message" => "Order items inserted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
