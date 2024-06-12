<?php
session_start();
require "../login-sec/connection.php";

if (isset($_POST['send'])) {
    // Establish database connection
    $conn = getDBConnection();
    
    // Sanitize user input using prepared statements
    $Email = $_POST['Email'] ?? '';
    $Subject = $_POST['Subject'] ?? '';
    $Message = $_POST['Message'] ?? '';

    // Retrieve user details from the session
    $Fname = $_SESSION['Fname'];
    $Lname = $_SESSION['Lname'];
    $Name = $Fname . ' ' . $Lname; // Concatenate Fname and Lname
    
    // Retrieve user ID from the users table based on Email
    $query = "SELECT UserID FROM users WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $UserID = $row['UserID'];

        // Check if feedback already exists for the user
        $existing_feedback_query = "SELECT * FROM feedback WHERE UserID = ?";
        $stmt = $conn->prepare($existing_feedback_query);
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $existing_feedback_result = $stmt->get_result();

        // If feedback exists, update it; otherwise, insert new feedback
        if ($existing_feedback_result->num_rows > 0) {
            $update_data = "UPDATE feedback SET Fname=?, Lname=?, Email=?, Subject=?, Message=? WHERE UserID=?";
            $stmt = $conn->prepare($update_data);
            $stmt->bind_param("sssssi", $Fname, $Lname, $Email, $Subject, $Message, $UserID);
        } else {
            $insert_data = "INSERT INTO feedback (UserID, Fname, Lname, Email, Subject, Message) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_data);
            $stmt->bind_param("isssss", $UserID, $Fname, $Lname, $Email, $Subject, $Message);
        }

        // Execute SQL statement
        if ($stmt->execute()) {
            // Send Email notification
            $recipient = "bpsu.bindtogether@gmail.com";
            $mailheader = "From: $Name <$Email>\r\n";
            mail($recipient, $Subject, $Message, $mailheader) or die("Error sending Email.");

            // Redirect to success page
            header("Location: success.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "User not found.";
    }

    // Close prepared statements and database connection
    $stmt->close();
    $conn->close();
}

