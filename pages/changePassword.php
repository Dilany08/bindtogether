<?php
require_once '../login-sec/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Password = $_SESSION['Password'];
$Lname = $_SESSION['Lname'];
$Mname = $_SESSION['Mname'];
$PhoneNum = $_SESSION['PhoneNum'];
$UserID = $_SESSION['UserID'] ?? '';
$Avatar = $_SESSION['Avatar'];

$message = "";
$message_class = "";

if (isset($_POST['submit'])) {
    $conn = getDBConnection();

    // Check if UserID is set
    if (empty($UserID)) {
        die("User ID is not set.");
    }

    $old_pass = mysqli_real_escape_string($conn, $_POST['old_pass']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass']);

    // Validate old Password
    $check_Password_query = "SELECT Password FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($check_Password_query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $UserID);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if password is verified
    if ($user && password_verify($old_pass, $user['Password'])) {

        // Check if new Password is provided and confirm Password matches
        $Password_updated = true; // Default to true in case no new Password is provided
        if (!empty($new_pass)) {
            if ($new_pass === $confirm_pass) {
                // Hash and update new Password
                $hashed_Password = password_hash($new_pass, PASSWORD_DEFAULT);
                $update_pass_query = "UPDATE users SET Password = ? WHERE UserID = ?";
                $stmt = $conn->prepare($update_pass_query);
                if ($stmt === false) {
                    die("Prepare failed: " . htmlspecialchars($conn->error));
                }
                $stmt->bind_param("si", $hashed_Password, $UserID);
                $Password_updated = $stmt->execute();
                if (!$Password_updated) {
                    die("Execute failed: " . htmlspecialchars($stmt->error));
                }
                // Update session Password
                $_SESSION['Password'] = $hashed_Password;
            } else {
                $Password_updated = false;
                $message = "New Password and confirm Password do not match.";
                $message_class = "alert-danger";
            }
        }

        // password validation
        if ($Password_updated) {
            $message = "Password updated successfully.";
            $message_class = "alert-success";
        } else {
            $message = "Failed to update password.";
            $message_class = "alert-danger";
        }
    } else {
        $message = "Old Password is incorrect.";
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
    <title>Change Password</title>

    <!-- Font awesome CDN link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link  -->
    <link rel="stylesheet" href="../css/frontpage.css">
    <style>
        form>div {
            display: flex;
            justify-content: right;
        }

        form>div>.btn {
            margin-left: 0;
            width: 48%;
        }

        /* make two buttons appear next to eachother */

        .btn {
            margin-left: 15px;
        }

        .back-button {
            display: inline-block;
            width: 5rem;
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
        }

        .back-button i {
            margin-right: 3px;
        }

        .back-button:hover {
            background-color: #c72d2d;
        }
    </style>
</head>

<body>

    <?php require_once '../components/user_header.php' ?>
    <!-- Back button -->
    <a href="update.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <section class="form-container">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Change Password</h3>
            <?php if (!empty($message)) : ?>
                <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <p style="text-align:left; padding: 0;">Old Password:</p>
            <input type="Password" name="old_pass" maxlength="20" placeholder="Enter your old Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <p style="text-align:left; padding: 0;">New Password:</p>
            <input type="Password" name="new_pass" maxlength="20" placeholder="Enter your new Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <p style="text-align:left; padding: 0;">Confirm Password:</p>
            <input type="Password" name="confirm_pass" maxlength="20" placeholder="Confirm your new Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <div>
                <input type="submit" value="Change Password" name="submit" class="btn">
            </div>
        </form>

    </section>
    <!-- custom js file link  -->
    <script src="../js/script.js"></script>
</body>

</html>