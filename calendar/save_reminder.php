<?php
require_once '../login-sec/connection.php';
session_start();

$EventID = $_POST['EventID'];
$Email = $_POST['Email'];

$conn = getDBConnection();

$insert_query = "INSERT INTO `reminders`(`EventID`, `Email`) VALUES ('$EventID', '$Email')";

if(mysqli_query($conn, $insert_query)) {
    $data = array('status' => true, 'msg' => 'Reminder set successfully!');
} else {
    $data = array('status' => false, 'msg' => 'Sorry, Reminder not set. Check your details');
}

echo json_encode($data);
$conn->close();
?>
