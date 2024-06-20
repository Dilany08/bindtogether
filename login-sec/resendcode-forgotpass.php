<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['Email'])) {
    header('Location: forgot-password.php');
    exit();
}

$Email = $_SESSION['Email'];
$UserType = $_SESSION['UserType'];

$errors = [];

$conn = getDBConnection();
$Code = rand(100000, 999999); // Generate a 6-digit Code

if ($UserType == 'user') {
    $stmt = $conn->prepare("UPDATE users SET Code = ? WHERE Email = ?");
} else {
    $stmt = $conn->prepare("UPDATE admins SET Code = ? WHERE Email = ?");
}
$stmt->bind_param("ss", $Code, $Email);

if ($stmt->execute()) {
    $subject = "Password Reset Code";
    $message = "Your password reset code is: $Code";
    $sender = "From: bpsu.bindtogether@gmail.com";

    if (mail($Email, $subject, $message, $sender)) {
        $_SESSION['info'] = "We've sent a new password reset code to your email - $Email";
        header('Location: reset-Code.php');
        exit();
    } else {
        $errors['mail-error'] = "Failed to send the code!";
    }
} else {
    $errors['db-error'] = "Database error: Failed to update the code.";
}

$stmt->close();
$conn->close();

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
    header('Location: reset-Code.php');
    exit();
}
