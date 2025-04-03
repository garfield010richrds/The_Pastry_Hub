<?php
// works with signup page

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "pastryhub";

// Create connection using a try-catch block
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Retrieve JSON data from POST request
    $data = json_decode(file_get_contents("php://input"), true);

    // Sanitize and extract data
    $first_name = $conn->real_escape_string($data['firstName']);
    $last_name = $conn->real_escape_string($data['lastName']);
    $dob = $conn->real_escape_string($data['dob']);
    $gender = $conn->real_escape_string($data['gender']);
    $phone = $conn->real_escape_string($data['phone']);
    $trn = $conn->real_escape_string($data['trn']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert into Users table
    $sql = "INSERT INTO Users (first_name, last_name, date_of_birth, gender, phone, trn, email, password)
            VALUES ('$first_name', '$last_name', '$dob', '$gender', '$phone', '$trn', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Registration successful"]);
    } else {
        throw new Exception("Error: " . $sql . " - " . $conn->error);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

// Close connection
$conn->close();
?>
