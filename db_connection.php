<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "pastryhub";

try {
    // Create connection using MySQLi with improved error handling
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection error
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    else{
        //throw new Exception("Database connection success: " . $conn->connect_error);

 }

} catch (Exception $e) {
    // Display a user-friendly error message and log the error
    die("<p style='color: red; text-align: center;'>Sorry, we are experiencing technical issues. Please try again later.</p>");
    
}
?>
