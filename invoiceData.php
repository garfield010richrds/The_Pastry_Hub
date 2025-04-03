<?php
include 'db_connection.php'; // Ensure database connection is established

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input: Check if required POST variables exist before using them
    if (!isset($_POST['order_id'], $_POST['user_id'], $_POST['total_price'], $_POST['subtotal'], $_POST['tax'], $_POST['discount'])) {
        echo json_encode(["status" => "error", "message" => "Missing required form data."]);
        exit;
    }

    // Retrieve form data safely
    $order_id = intval($_POST['order_id']);
    $user_id = intval($_POST['user_id']);
    $total_price = floatval($_POST['total_price']);
    $subtotal = floatval($_POST['subtotal']);
    $tax = floatval($_POST['tax']);
    $discount = floatval($_POST['discount']);

    // Debugging
    error_log("Received Order ID: $order_id");

    // Retrieve the most recent checkout_id for the given order_id
    $checkout_id = null;
    $sql = "SELECT checkout_id FROM checkout WHERE order_id = ? ORDER BY checkout_id DESC LIMIT 1";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $checkout_id = intval($row["checkout_id"]);
            error_log("Retrieved Checkout ID: $checkout_id");
        } else {
            error_log("No checkout record found for Order ID: $order_id");
            echo json_encode(["status" => "error", "message" => "No checkout record found for this order ID."]);
            exit;
        }

        $stmt->close();
    } else {
        error_log("SQL Error: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Database preparation error."]);
        exit;
    }

    // Ensure checkout_id is valid
    if (!$checkout_id) {
        echo json_encode(["status" => "error", "message" => "Invalid checkout_id."]);
        exit;
    }

    // Insert data into the invoices table
    $sql = "INSERT INTO invoices (order_id, user_id, total_price, checkout_id, subtotal, tax, discount) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiidddd", $order_id, $user_id, $total_price, $checkout_id, $subtotal, $tax, $discount);

        if ($stmt->execute()) {
            error_log("Invoice created successfully for Order ID: $order_id");
            echo json_encode(["status" => "success", "message" => "Invoice created successfully!", "order_id" => $order_id]);
        } else {
            error_log("Database error: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        error_log("SQL Preparation Error: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Database preparation error."]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
