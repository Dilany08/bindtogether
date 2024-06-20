<?php
require_once '../login-sec/connection.php';

// Function to send email
function sendEmailReminder($email, $eventName, $eventDate, $eventTime)
{
    $to = $email;
    $subject = "Event Reminder: $eventName";
    $message = "This is a reminder for the event '$eventName' scheduled on $eventDate at $eventTime.";
    $headers = "From: bpsu.bindtogether@gmail.com";

    // Send email
    mail($to, $subject, $message, $headers);
}

$conn = getDBConnection();

// Get the current date and the date for the next day
$currentDate = date('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 day'));

// Query to get events happening the next day
$query = "SELECT e.EventName, e.StartDate, e.StartTime, r.Email 
          FROM calendar e
          JOIN reminders r ON e.EventID = r.EventID
          WHERE e.StartDate = '$nextDate'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $eventName = $row['EventName'];
        $eventDate = $row['StartDate'];
        $eventTime = $row['StartTime'];
        $email = $row['Email'];

        // Send email reminder
        sendEmailReminder($email, $eventName, $eventDate, $eventTime);
    }
}

$conn->close();
