<?php
include 'db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $old_price = isset($_POST['old_price']) && $_POST['old_price'] !== "" ? $_POST['old_price'] : 0.0;
    $image = $_POST['image'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    if ($name && $price && $image && $description && $category) {
        $query = "INSERT INTO products (name, price, old_price, image, description, category) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        // Bind parameters dynamically (NULL for old_price if empty)
        $stmt->bind_param("sdssss", $name, $price, $old_price, $image, $description, $category);

        if ($stmt->execute()) {
            $message = "✅ Product added successfully!";
        } else {
            $message = "❌ Error adding product: " . $conn->error;
        }
    } else {
        $message = "❌ Please fill in all required fields!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - The Pastry Hub</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ffd1dc, #ff69b4, #da70d6);
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
    margin: 0;
    padding: 30px;
    min-height: 100vh;
    color: #2d3436;
    line-height: 1.6;
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

h1 {
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    font-size: 2.5rem;
    margin-bottom: 30px;
    letter-spacing: 1px;
    position: relative;
    display: inline-block;
}

h1::after {
    content: '';
    position: absolute;
    width: 60%;
    height: 4px;
    background: #ffffff;
    border-radius: 10px;
    left: 50%;
    bottom: -10px;
    transform: translateX(-50%);
}

/* Button to open modal - ENHANCED VISIBILITY */
.open-modal {
    padding: 16px 32px;
    background-color: #ff0055;
    color: white;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 1px;
    box-shadow: 0 6px 20px rgba(255, 0, 85, 0.5);
    transition: all 0.3s ease;
    transform: translateY(0);
    text-transform: uppercase;
    outline: 3px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
    position: relative;
    overflow: hidden;
}

/* Pulsing effect for button */
.open-modal::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    animation: pulse 2s infinite;
    pointer-events: none;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 0; }
    100% { transform: scale(1); opacity: 0; }
}

.open-modal:hover {
    background-color: #f50057;
    box-shadow: 0 8px 25px rgba(245, 0, 87, 0.6);
    transform: translateY(-3px);
    outline-color: rgba(255, 255, 255, 0.8);
}

.open-modal:active {
    transform: translateY(1px);
    box-shadow: 0 4px 15px rgba(245, 0, 87, 0.5);
}

/* Modal background */
.modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.modal-content {
    background: linear-gradient(to bottom right, #ffffff, #f5f7fa);
    margin: 8% auto;
    padding: 30px;
    border-radius: 20px;
    width: 55%;
    max-width: 650px;
    max-height: 80vh;
    overflow-y: auto;
    text-align: left;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    transform: translateY(0);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.modal.active .modal-content {
    transform: translateY(0);
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
    width: 8px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #ff4081;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #f50057;
}

/* Close button */
.close {
    position: absolute;
    right: 25px;
    top: 20px;
    color: #ff4081;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.close:hover {
    color: #f50057;
    transform: rotate(90deg);
    background-color: rgba(255, 64, 129, 0.1);
}

/* Form styling */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group {
    position: relative;
    margin-bottom: 10px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #444;
    transition: all 0.3s;
}

input, textarea {
    padding: 15px;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    font-size: 16px;
    width: 100%;
    box-sizing: border-box;
    transition: all 0.3s;
    background-color: rgba(255, 255, 255, 0.9);
}

input:focus, textarea:focus {
    border-color: #ff4081;
    box-shadow: 0 0 0 4px rgba(255, 64, 129, 0.15);
    outline: none;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

button {
    padding: 16px;
    background: linear-gradient(to right, #4CAF50, #2E7D32);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    margin-top: 10px;
}

button:hover {
    background: linear-gradient(to right, #43A047, #2E7D32);
    box-shadow: 0 7px 20px rgba(76, 175, 80, 0.4);
    transform: translateY(-2px);
}

button:active {
    transform: translateY(1px);
}

#message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s;
}

.message-success {
    background-color: rgba(76, 175, 80, 0.2);
    color: #2E7D32;
    border-left: 4px solid #2E7D32;
}

.message-error {
    background-color: rgba(244, 67, 54, 0.2);
    color: #d32f2f;
    border-left: 4px solid #d32f2f;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 85%;
        margin: 15% auto;
        padding: 20px;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .open-modal {
        padding: 14px 28px;
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .modal-content {
        width: 95%;
        padding: 15px;
    }
    
    input, textarea, button {
        padding: 12px;
    }
}
   
    </style>
</head>
<body>

    <h1>Manage Products</h1>


    <!-- Open Modal Button -->
    <button class="open-modal" onclick="openModal()">Add New Product</button>
    <button class="open-modal" onclick="window.location.href='product.php'">Back To Product Page</button>

    <div id="message"><?= $message; ?></div>

    <!-- Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>

            <h2>Add a New Product</h2>

            <form method="POST" action="">
                <label>Product Name:</label>
                <input type="text" name="name" required>

                <label>Price:</label>
                <input type="number" name="price" step="0.01" required>

                <label>Old Price:</label>
                <input type="number" name="old_price" step="0.01">

                <label>Image URL:</label>
                <input type="text" name="image" placeholder = "Format: img/image name .image type (jpeg etc)"required>

                <label>Description:</label>
                <textarea name="description" rows="3" required></textarea>

                <label>Category:</label>
                <input type="text" name="category" required>

                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("productModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("productModal").style.display = "none";
        }

        window.onclick = function(event) 
        {
            let modal = document.getElementById("productModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
            form.clear();

        }

        // Clear form after submitting
        productForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent actual form submission
            
            // Here you would normally send data to the backend using fetch/AJAX

            alert("Product added successfully!");
            
            // Clear form fields
            productForm.reset();
        });
    </script>

</body>
</html>
