<?php
require_once '../login-sec/connection.php';
session_start();

$EventID = $_POST['EventID'];
$conn = getDBConnection();

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Delete from dependent tables first
    $delete_reminders_query = "DELETE FROM `reminders` WHERE `EventID` = '".$EventID."'";
    if (!mysqli_query($conn, $delete_reminders_query)) {
        throw new Exception('Failed to delete from reminders table');
    }

    // Then delete from main table
    $delete_event_query = "DELETE FROM `calendar` WHERE `EventID` = '".$EventID."'";
    if (!mysqli_query($conn, $delete_event_query)) {
        throw new Exception('Failed to delete event');
    }

    // Commit transaction
    mysqli_commit($conn);
    $data = array('status' => true, 'msg' => 'Event and associated reminders deleted successfully!');
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    $data = array('status' => false, 'msg' => 'Sorry, Event not deleted. ' . $e->getMessage());
}

echo json_encode($data);
$conn->close();