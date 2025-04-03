<?php
include 'db_connection.php';

$message = "";
$order_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;

    if (!$order_id) {
        $message = "❌ No order ID provided.";
    } else {
        $query = "SELECT 
            c.ship_userID AS user_id, 
            u.first_name AS first_name, 
            u.last_name AS last_name, 
            u.trn, 
            o.order_id,

            -- Shipping Info
            c.checkout_id, 
            c.ship_email, 
            c.ship_address, 
            c.card_number, 
            c.cvv, 
            c.expiration_date, 
            c.ship_full_name,

            -- Order Details
            o.total_price, 
            o.order_status, 
            o.order_date,

            -- Purchased Items
            p.name,
            oi.product_id, 
            oi.quantity, 
            oi.price, 
            oi.order_item_id,

            -- Invoice Details
            i.invoice_date, 
            i.total_price AS invoice_total, 
            i.payment_status, 
            i.checkout_id AS invoice_checkout_id, 
            i.subtotal, 
            i.tax, 
            i.discount

        FROM checkout c
        JOIN orders o ON c.order_id = o.order_id AND c.ship_userID = o.user_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.id

        JOIN invoices i ON o.order_id = i.order_id
        JOIN users u ON c.ship_userID = u.user_id


        WHERE c.ship_userID = ? AND o.order_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_data = $result->fetch_all(MYSQLI_ASSOC);
            $message = "✅ Order found.";
        } else {
            $message = "❌ Order not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Order ID</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background-color: lightgray;
        }
        input, button {
            padding: 10px;
            margin: 5px;
        }
        #message {
            margin-top: 20px;
            font-weight: bold;
            color: <?= isset($message) && strpos($message, '✅') !== false ? 'green' : 'red'; ?>;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
            body {
                font-family:'Times New Roman', Times, serif;
                background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
                color: #333;
                padding: 20px;
            }
    

    
            h1 {
                text-align: center;
                color: #007BFF;
                margin-bottom: 20px;
            }
    
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
    
            .company-info {
                margin-bottom: 40px;
                text-align: center;
            }
    
            .company-info h2 {
                color: #008080;
                margin-bottom: 10px;
            }

    
            button {
                display: block;
                width: 100%;
                margin: 20px 0;
                padding: 10px;
                background: #007BFF;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
    
            button:hover {
                background: #0056b3;
            }
    
            .button-group {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }
    
            .button-group button {
                width: calc(33.33% - 10px);
            }
    

    
            label {
                font-size: 1.1em;
                margin-bottom: 8px;
                display: block;
                color: #555;
            }
    
            .email-container {
                margin-top: 20px;
            }
    
            input[type="email"] {
                width: 100%;
                padding: 10px;
                margin: 5px 0;
                border: 1px solid #007BFF;
                border-radius: 5px;
            }
    
            @media (max-width: 768px) {
                .invoice-info {
                    flex-direction: column;
                }
    
                .button-group {
                    flex-direction: column;
                }
    
                button {
                    width: 100%;
                }
            }
    </style>
</head>
<body>
<div class="company-info">
        <h2>The Pastry Hub</h2>
        <p>237 Old Hope Road, Kingston</p>
        <p>Kingston, Jamaica, 876</p>
        <p>Phone:  +1 (876) 000-0000</p>
        <p>Email: ThePastryHub@gmail.com</p>
    </div>

    <h2>Check Order ID</h2>
    <p><b>Hello, <span id="userName">Guest</span> 🙋🏾‍♂️</b></p>
    <p><b>User ID: <span id="user_id"></span> 👌👌</b></p>

    <form method="POST" action="">
        <input type="hidden" id="hidden_user_id" name="user_id">
        <input type="text" name="order_id" placeholder="Enter Order ID">
        <button type="submit">Check</button>
    </form>

    </div>
                            <div>
                                    <button onclick = "window.print()"> Download</button>
                                    <div>
                                    <button onclick="window.location.href = 'product.php';">Back to Products</button>

                            </div>
                            </div>

                <p id="message" style="color: <?= isset($message) && strpos($message, '✅') !== false ? 'green' : 'red'; ?>"><?= $message; ?></p>

    <!-- Add a wrapper around the invoice details -->
    <div id="invoice-wrapper">
       <!-- 
         <?php if (!empty($order_data)): ?>
            <h3>Order Details</h3>
            <table>
                <tr><th>Order ID</th><td><?= $order_data[0]['order_id']; ?></td></tr>
                <tr><th>Total Price</th><td>$<?= number_format($order_data[0]['total_price'], 2); ?></td></tr>
                <tr><th>Order Status</th><td><?= $order_data[0]['order_status']; ?></td></tr>
                <tr><th>Order Date</th><td><?= $order_data[0]['order_date']; ?></td></tr>
            </table>

        <?php endif; ?>
        -->

    </div>

    <script>
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        const fullName = `${capitalize(currentUser.first_name)} ${capitalize(currentUser.last_name)}`;
        
        document.getElementById('userName').textContent = fullName;
        document.getElementById('user_id').textContent = currentUser.user_id;
        document.getElementById('hidden_user_id').value = currentUser.user_id;
     


    </script>

    <?php if (!empty($order_data)): ?>
        <h3>Order Details</h3>
        <table>
            <tr><th>Order ID</th><td><?= $order_data[0]['order_id']; ?></td></tr>
            <tr><th>Total Price</th><td>$<?= number_format($order_data[0]['total_price'], 2); ?></td></tr>
            <tr><th>Order Status</th><td><?= $order_data[0]['order_status']; ?></td></tr>
            <tr><th>Order Date</th><td><?= $order_data[0]['order_date']; ?></td></tr>
        </table>

        <h3>Shipping Information</h3>
        <table>
            <tr><th>Full Name</th><td><?= $order_data[0]['ship_full_name']; ?></td></tr>
            <tr><th>Address</th><td><?= $order_data[0]['ship_address']; ?></td></tr>
            <tr><th>Email</th><td><?= $order_data[0]['ship_email']; ?></td></tr>
        </table>

        <h3>Card Information</h3>
        <table>
            <tr><th>Card Number</th><td>**** **** **** <?= substr($order_data[0]['card_number'], -4); ?></td></tr>
            <tr><th>Expiration Date</th><td><?= $order_data[0]['expiration_date']; ?></td></tr>
        </table>

        <h3>Invoice Details</h3>
        <table>
            <tr><th>Invoice Date</th><td><?= $order_data[0]['invoice_date']; ?></td></tr>
            <tr><th>Subtotal</th><td>$<?= number_format($order_data[0]['subtotal'], 2); ?></td></tr>
            <tr><th>Tax</th><td>$<?= number_format($order_data[0]['tax'], 2); ?></td></tr>
            <tr><th>Discount</th><td>$<?= number_format($order_data[0]['discount'], 2); ?></td></tr>
            <tr><th>Total Invoice Price</th><td>$<?= number_format($order_data[0]['invoice_total'], 2); ?></td></tr>
            <tr><th>Payment Status</th><td><?= $order_data[0]['payment_status']; ?></td></tr>
        </table>

        <h3>Purchased Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>

                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_data as $item): ?>
                    <tr>
                        <td><?= $item['product_id']; ?></td>
                        <td><?= $item['name']; ?></td>

                        <td><?= $item['quantity']; ?></td>
                        <td>$<?= number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
