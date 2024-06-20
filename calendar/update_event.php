<?php
require_once '../login-sec/connection.php';
session_start();

$conn = getDBConnection();

$EventID = $_POST['EventID'];
$EventName = $_POST['EventName'];
$StartDate = $_POST['StartDate'];
$StartTime = $_POST['StartTime'];
$EndDate = $_POST['EndDate'];
$EndTime = $_POST['EndTime'];

$update_query = "UPDATE calendar SET EventName='$EventName', StartDate='$StartDate', StartTime='$StartTime', EndDate='$EndDate', EndTime='$EndTime' WHERE EventID='$EventID'";

if (mysqli_query($conn, $update_query)) {
    $response = array('status' => true, 'msg' => 'Event updated successfully!');
} else {
    $response = array('status' => false, 'msg' => 'Error updating event: ' . mysqli_error($conn));
}

echo json_encode($response);
$conn->close();
