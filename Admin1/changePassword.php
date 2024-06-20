<?php
require '../login-sec/connection.php';
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Mname = $_SESSION['Mname'];
$Lname = $_SESSION['Lname'];
$Avatar = $_SESSION['Avatar'];
$PhoneNum = $_SESSION['PhoneNum'];
$Password = $_SESSION['Password'];
$AdminID = $_SESSION['AdminID'] ?? '';

if (empty($AdminID)) {
    die("AdminID is not set.");
}

$message = "";
$message_class = "";

if (isset($_POST['submit'])) {
    $conn = getDBConnection();

    $old_pass = mysqli_real_escape_string($conn, $_POST['old_pass']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass']);

    // Validate old Password
    $check_password_query = "SELECT Password FROM admins WHERE AdminID = ?";
    $stmt = $conn->prepare($check_password_query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $AdminID);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and password is verified
    if ($user && password_verify($old_pass, $user['Password'])) {

        // Check if new password is provided and confirm password matches
        $password_updated = true; // Default to true in case no new password is provided
        if (!empty($new_pass)) {
            if ($new_pass === $confirm_pass) {
                // Hash and update new password
                $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
                $update_pass_query = "UPDATE admins SET Password = ? WHERE AdminID = ?";
                $stmt = $conn->prepare($update_pass_query);
                if ($stmt === false) {
                    die("Prepare failed: " . htmlspecialchars($conn->error));
                }
                $stmt->bind_param("si", $hashed_password, $AdminID);
                $password_updated = $stmt->execute();
                if (!$password_updated) {
                    die("Execute failed: " . htmlspecialchars($stmt->error));
                }
                // Update session password
                $_SESSION['Password'] = $hashed_password;
            } else {
                $password_updated = false;
                $message = "New password and confirm password do not match.";
                $message_class = "alert-danger";
            }
        }

        // Check if profile, password, and avatar are updated successfully
        if ($password_updated) {
            $message = "Password updated successfully.";
            $message_class = "alert-success";
        } else {
            $message = "Failed to update password.";
            $message_class = "alert-danger";
        }
    } else {
        $message = "Old password is incorrect.";
        $message_class = "alert-danger";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>

    <!-- Font awesome CDN link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <Style>
        form>div {
            display: flex;
            justify-content: right;
        }

        form>div>.btn {
            margin-left: 0;
            width: 48%;
        }

        /* make two buttons appear next to eachother */
        .back-button {
            display: inline-block;
            width: 7rem;
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
        }

        .back-button i {
            margin-right: 1px;
        }

        .back-button:hover {
            background-color: #c72d2d;
        }
    </Style>
</head>

<body>

    <?php require_once "../components/header.php"; ?>

    <a href="update_profile.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <!-- Admin profile update section starts -->
    <section class="form-container">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Change Password</h3>
            <?php if (!empty($message)) : ?>
                <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <p style="text-align:left">Old Password:</p>
            <input type="password" name="old_pass" maxlength="20" placeholder="Enter your old password" class="box">
            <p style="text-align:left">New Password:</p>
            <input type="password" name="new_pass" maxlength="20" placeholder="Enter your new password" class="box">
            <p style="text-align:left">Confirm Password:</p>
            <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm your new password" class="box">
            <div>
                <input type="submit" value="Change Password" name="submit" class="btn">
            </div>
        </form>

    </section>
    <!-- Admin profile update section ends -->

    <!-- Custom JS file link -->
    <script src="../js/admin_script.js"></script>
</body>

</html>