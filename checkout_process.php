<?php
include 'db_connection.php'; // Ensure database connection is established

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get order ID from JavaScript
    $order_id = intval($_POST['order_id']);

    // Retrieve form data
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $fullAddress = trim($_POST['address']);
    $cardNumber = trim($_POST['cardNumber']);
    $expiry = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    $ship_userID = trim($_POST['user_id']); // Taken from localStorage via AJAX

    error_log("Received Order ID: " . $order_id);

    // Insert data into checkout table
    $sql = "INSERT INTO checkout (order_id, ship_full_name, ship_email, ship_address, card_number, cvv, expiration_date, ship_userID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issssssi", $order_id, $fullName, $email, $fullAddress, $cardNumber, $cvv, $expiry, $ship_userID);

        if ($stmt->execute()) {
            // Get the most recent auto-incremented checkout_id
            $checkout_id = $conn->insert_id;

            error_log("Checkout record inserted. Checkout ID: " . $checkout_id);
        } else {
            error_log("MySQL Error: " . $stmt->error);
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
