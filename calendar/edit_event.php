<?php
require_once '../login-sec/connection.php';
session_start();

$EventID = $_POST['EventID'];
$EventName = $_POST['EventName'];
$StartDate = date("Y-m-d", strtotime($_POST['StartDate']));
$EndDate = date("Y-m-d", strtotime($_POST['EndDate']));

$conn = getDBConnection();

$update_query = "UPDATE `calendar` SET `EventName` = '" . $EventName . "', `StartDate` = '" . $StartDate . "', `EndDate` = '" . $EndDate . "' WHERE `EventID` = '" . $EventID . "'";

if (mysqli_query($conn, $update_query)) {
    $data = array('status' => true, 'msg' => 'Event updated successfully!');
} else {
    $data = array('status' => false, 'msg' => 'Sorry, Event not updated.');
}

echo json_encode($data);
$conn->close();
