<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

// Fetch the user's details from the session
$Fname = $_SESSION['Fname'];
$Lname = $_SESSION['Lname'];
$Email = $_SESSION['Email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="../css/contact.css">
</head>

<body>
    <a href="../pages/frontpage.php" class="backBtn">Back</a>
    <div class="container">
        <h1>Contact Us</h1>
        <p>Feel free to contact and share your feedback with us for any concern.</p>
        <form action="mail.php" method="POST" autocomplete="on">
            <label for="Name">Name: </label>
            <input type="text" name="Name" id="Name" value="<?= htmlspecialchars($Fname . ' ' . $Lname); ?>" required readonly>
            <label for="Email">Email: </label>
            <input type="email" name="Email" id="Email" value="<?= htmlspecialchars($Email); ?>" required readonly>
            <label for="Subject">Subject: </label>
            <input type="text" name="Subject" id="Subject" required>
            <label for="Message">Feedback: </label>
            <textarea name="Message" cols="30" rows="10" required></textarea>
            <input class="button" type="submit" name="send" value="Send Feedback">
        </form>
    </div>
</body>

</html>