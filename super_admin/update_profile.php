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

    $Fname = mysqli_real_escape_string($conn, $_POST['Fname']);
    $Mname = mysqli_real_escape_string($conn, $_POST['Mname']);
    $Lname = mysqli_real_escape_string($conn, $_POST['Lname']);
    $PhoneNum = mysqli_real_escape_string($conn, $_POST['PhoneNum']);
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
        // Old password matched, proceed to update profile
        $update_profile_query = "UPDATE admins SET Fname=?, Mname=?, Lname=?, PhoneNum=? WHERE AdminID=?";
        $stmt = $conn->prepare($update_profile_query);
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssssi", $Fname, $Mname, $Lname, $PhoneNum, $AdminID);
        $profile_updated = $stmt->execute();
        if (!$profile_updated) {
            die("Execute failed: " . htmlspecialchars($stmt->error));
        }

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

        // Check if a new avatar is uploaded
        $avatar_updated = true; // Default to true in case no new avatar is provided
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
            $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
            $avatar_name = basename($_FILES['avatar']['name']);
            $avatar_size = $_FILES['avatar']['size'];
            $avatar_type = pathinfo($avatar_name, PATHINFO_EXTENSION);

            // Validate file type and size
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($avatar_type), $allowed_types) && $avatar_size <= 5000000) {
                $new_avatar_name = "admin_" . $AdminID . "_" . time() . "." . $avatar_type;
                $avatar_path = "../upload/" . $new_avatar_name;

                if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
                    $update_avatar_query = "UPDATE admins SET Avatar = ? WHERE AdminID = ?";
                    $stmt = $conn->prepare($update_avatar_query);
                    if ($stmt === false) {
                        die("Prepare failed: " . htmlspecialchars($conn->error));
                    }
                    $stmt->bind_param("si", $new_avatar_name, $AdminID);
                    $avatar_updated = $stmt->execute();
                    if (!$avatar_updated) {
                        die("Execute failed: " . htmlspecialchars($stmt->error));
                    }
                    // Update session Avatar
                    $_SESSION['Avatar'] = $new_avatar_name;

                    // Update avatar in posts table
                    $update_avatar_posts_query = "UPDATE posts SET Avatar = ? WHERE AdminID = ?";
                    $stmt = $conn->prepare($update_avatar_posts_query);
                    if ($stmt === false) {
                        die("Prepare failed: " . htmlspecialchars($conn->error));
                    }
                    $stmt->bind_param("si", $new_avatar_name, $AdminID);
                    $avatar_updated = $stmt->execute();
                    if (!$avatar_updated) {
                        die("Execute failed: " . htmlspecialchars($stmt->error));
                    }
                } else {
                    $avatar_updated = false;
                    $message = "Failed to upload new avatar.";
                    $message_class = "alert-danger";
                }
            } else {
                $avatar_updated = false;
                $message = "Invalid avatar file type or size.";
                $message_class = "alert-danger";
            }
        }

        // Update session variables with the new data
        $_SESSION['Fname'] = $Fname;
        $_SESSION['Mname'] = $Mname;
        $_SESSION['Lname'] = $Lname;
        $_SESSION['PhoneNum'] = $PhoneNum;

        // Check if profile, password, and avatar are updated successfully
        if ($profile_updated && $password_updated && $avatar_updated) {
            $message = "Profile, password, and avatar updated successfully.";
            $message_class = "alert-success";
        } elseif ($profile_updated && $password_updated) {
            $message = "Profile and password updated successfully.";
            $message_class = "alert-success";
        } elseif ($profile_updated) {
            $message = "Profile updated successfully.";
            $message_class = "alert-success";
        } else {
            $message = "Failed to update profile, password, or avatar.";
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

<?php require_once "../components/headerSuperAdmin.php"; ?>

<a href="super_admin.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

<!-- Admin profile update section starts -->
<section class="form-container">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Update Profile</h3>
      <?php if (!empty($message)): ?>
      <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
   <?php endif; ?>
      <p style="text-align:left">First Name:</p>
      <input type="text" name="Fname" maxlength="20" class="box" value="<?php echo htmlspecialchars($Fname) ?>">
      <p style="text-align:left">Middle Name:</p>
      <input type="text" name="Mname" maxlength="20" class="box" value="<?php echo htmlspecialchars($Mname) ?>">
      <p style="text-align:left">Last Name:</p>
      <input type="text" name="Lname" maxlength="20" class="box" value="<?php echo htmlspecialchars($Lname) ?>">
      <p style="text-align:left">Phone Number:</p>
      <input type="text" name="PhoneNum" maxlength="20" class="box" value="<?php echo htmlspecialchars($PhoneNum) ?>">
      <p style="text-align:left">New Profile Picture:</p>
      <input type="file" name="avatar" class="box">
      <p style="text-align:left">Old Password:</p>
      <input type="password" name="old_pass" maxlength="20" placeholder="Enter your old password" class="box">
      <p style="text-align:left">New Password:</p>
      <input type="password" name="new_pass" maxlength="20" placeholder="Enter your new password" class="box">
      <p style="text-align:left">Confirm Password:</p>
      <input type="password" name="confirm_pass" maxlength="20" placeholder="Confirm your new password" class="box">
      <input type="submit" value="Update Now" name="submit" class="btn">
   </form>

</section>
<!-- Admin profile update section ends -->

<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>
</body>
</html>
