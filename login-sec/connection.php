<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bindtogether";

function getDBConnection() {
    $conn = new mysqli("localhost", "root", "", "bindtogether");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
    


     