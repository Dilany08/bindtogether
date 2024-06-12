<?php
require_once '../login-sec/connection.php';
session_start();

$conn = getDBConnection();

$EventName = $_POST['EventName'];
$StartDate = $_POST['StartDate'];
$StartTime = $_POST['StartTime'];
$EndDate = $_POST['EndDate'];
$EndTime = $_POST['EndTime'];

$save_query = "INSERT INTO calendar (EventName, StartDate, StartTime, EndDate, EndTime) VALUES ('$EventName', '$StartDate', '$StartTime', '$EndDate', '$EndTime')";

if (mysqli_query($conn, $save_query)) {
    $response = array('status' => true, 'msg' => 'Event saved successfully!');
} else {
    $response = array('status' => false, 'msg' => 'Error saving event: ' . mysqli_error($conn));
}

echo json_encode($response);
$conn->close();
?>
