<?php
$servername = "localhost:3306";
$username = "dbuser";
$password = "";
$schema = "airport";

// Create connection
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->query("SET SESSION time_zone = '+8:00'");
?>