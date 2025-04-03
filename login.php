
<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "pastryhub";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Validate input
if (!isset($_POST['trn']) || !isset($_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "TRN and password are required"]);
    exit;
}

$trn = $_POST['trn'];
$password = $_POST['password'];

// Query to fetch user details
$sql = "SELECT user_id, email, first_name, last_name, trn, password FROM users WHERE trn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $trn);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        unset($user['password']);
        echo json_encode(["status" => "success", "user" => $user]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid TRN or password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid TRN or password"]);
}

$stmt->close();
$conn->close();
?>