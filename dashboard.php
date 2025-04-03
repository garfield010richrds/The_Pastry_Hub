<?php
include 'db_connection.php';

$message = "";
$order_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trn = isset($_POST['trn']) ? $_POST['trn'] : null;
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;

    if (!$trn && !$order_id) {
        $message = "❌ Please enter either Order ID or TRN.";
    } else {
        $query = "SELECT 
            c.ship_userID AS user_id, 
            u.first_name AS first_name, 
            u.last_name AS last_name, 
            u.trn, 
            o.order_id,

            c.ship_email, 
            c.ship_address, 
            c.ship_full_name,

            o.total_price, 
            o.order_status, 
            o.order_date,

            p.name AS product_name,
            oi.quantity, 
            oi.price, 

            i.invoice_date, 
            i.total_price AS invoice_total, 
            i.payment_status, 
            i.subtotal, 
            i.tax, 
            i.discount

        FROM checkout c
        JOIN orders o ON c.order_id = o.order_id AND c.ship_userID = o.user_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN invoices i ON o.order_id = i.order_id
        JOIN users u ON c.ship_userID = u.user_id";

        if ($trn) {
            $query .= " WHERE u.trn = ?";
        } elseif ($order_id) {
            $query .= " WHERE o.order_id = ?";
        }

        $stmt = $conn->prepare($query);

        if ($trn) {
            $stmt->bind_param("s", $trn);
        } elseif ($order_id) {
            $stmt->bind_param("i", $order_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_data = $result->fetch_all(MYSQLI_ASSOC);
            $message = "✅ Order(s) found.";
        } else {
            $message = "❌ No matching records found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pastry Hub - Invoice Search</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, rgb(255, 182, 193), rgb(255, 105, 180));
            margin: 0;
            padding: 20px;
            text-align: center;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .invoice-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .company-info h2 {
            color: #008080;
            margin-bottom: 10px;
        }

        .company-info p {
            margin: 5px 0;
            color: #666;
        }

        .search-form {
            margin-top: 20px;
        }

        input, button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }

        button {
            background-color: #ff4081;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #f50057;
        }

        #message {
            margin-top: 20px;
            font-weight: bold;
            color: <?= isset($message) && strpos($message, '✅') !== false ? 'green' : 'red'; ?>;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.9);
            color: black;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: linear-gradient(45deg, #4CAF50, #8bc34a);
            color: white;
        }

        td {
            background: #f9f9f9;
        }

        .back-button {
            background-color: #f44336;
            color: white;
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            margin-top: 30px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <h1>Admin User Invoice Display</h1>
        <div class="company-info">
            <h2>The Pastry Hub</h2>
            <p>237 Old Hope Road, Kingston</p>
            <p>Kingston, Jamaica, 876</p>
            <p>Phone: +1 (876) 000-0000</p>
            <p>Email: ThePastryHubja@gmail.com</p>
        </div>

        <div>

        <form class="search-form" method="POST" action="">
            <input type="text" name="trn" placeholder="Enter TRN (optional)">
            <input type="text" name="order_id" placeholder="Enter Order ID (optional)">
            <button type="submit">Search</button>

        </form>
    <a href="./product.php" class="back-button">Back to Product Page</a>
    <a href="./newProduct.php" class="back-button">Create a new Item</a>


        </div>
        <fieldset>
        <p id="message"><?= $message; ?></p>
        </fieldset>


        <fieldset> 
        <?php if (!empty($order_data)): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>TRN</th>
                    <th>Email</th>
                    <th>Shipping Address</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Invoice Date</th>
                    <th>Invoice Total</th>
                    <th>Payment Status</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Discount</th>
                </tr>
                <?php foreach ($order_data as $order): ?>
                    <tr>
                        <td><?= $order['order_id']; ?></td>
                        <td><?= $order['first_name'] . " " . $order['last_name']; ?></td>
                        <td><?= $order['trn']; ?></td>
                        <td><?= $order['ship_email']; ?></td>
                        <td><?= $order['ship_address']; ?></td>
                        <td><?= $order['order_status']; ?></td>
                        <td><?= $order['order_date']; ?></td>
                        <td><?= $order['product_name']; ?></td>
                        <td><?= $order['quantity']; ?></td>
                        <td>$<?= $order['price']; ?></td>
                        <td><?= $order['invoice_date']; ?></td>
                        <td>$<?= $order['invoice_total']; ?></td>
                        <td><?= $order['payment_status']; ?></td>
                        <td>$<?= $order['subtotal']; ?></td>
                        <td>$<?= $order['tax']; ?></td>
                        <td>$<?= $order['discount']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>
    </fieldset>

    <a href="./product.php" class="back-button">Back to Product Page</a>

</body>
</html>
