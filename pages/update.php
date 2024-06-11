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
    
    $Fname = mysqli_real_escape_string($conn, $_POST['Fname']);
    $Mname = mysqli_real_escape_string($conn, $_POST['Mname']);
    $Lname = mysqli_real_escape_string($conn, $_POST['Lname']);
    $PhoneNum = mysqli_real_escape_string($conn, $_POST['PhoneNum']);
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

    // Check if user exists and Password is verified
    if ($user && password_verify($old_pass, $user['Password'])) {
        // Old Password matched, proceed to update profile
        $update_profile_query = "UPDATE users SET Fname=?, Mname=?, Lname=?, PhoneNum=? WHERE UserID=?";
        $stmt = $conn->prepare($update_profile_query);
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssssi", $Fname, $Mname, $Lname, $PhoneNum, $UserID);
        $profile_updated = $stmt->execute();
        if (!$profile_updated) {
            die("Execute failed: " . htmlspecialchars($stmt->error));
        }

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
                $new_avatar_name = "user_" . $UserID . "_" . time() . "." . $avatar_type;
                $avatar_path = "../upload/" . $new_avatar_name;

                if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
                    $update_avatar_query = "UPDATE users SET Avatar = ? WHERE UserID = ?";
                    $stmt = $conn->prepare($update_avatar_query);
                    if ($stmt === false) {
                        die("Prepare failed: " . htmlspecialchars($conn->error));
                    }
                    $stmt->bind_param("si", $new_avatar_name, $UserID);
                    $avatar_updated = $stmt->execute();
                    if (!$avatar_updated) {
                        die("Execute failed: " . htmlspecialchars($stmt->error));
                    }
                    // Update session Avatar
                    $_SESSION['Avatar'] = $new_avatar_name;
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

        // Check if profile, Password, and avatar are updated successfully
        if ($profile_updated && $Password_updated && $avatar_updated) {
            $message = "Profile, Password, and avatar updated successfully.";
            $message_class = "alert-success";
        } elseif ($profile_updated && $avatar_updated) {
            $message = "Profile and avatar updated successfully.";
            $message_class = "alert-success";
        } elseif ($profile_updated) {
            $message = "Profile updated successfully.";
            $message_class = "alert-success";
        } else {
            $message = "Failed to update profile, Password, or avatar.";
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
   <title>Profile Update</title>

   <!-- Font awesome CDN link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link  -->
   <link rel="stylesheet" href="../css/frontpage.css">
   <style>
        .btn{
            margin-left: 5px;
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

<?php require_once '../components/user_header.php'?>
 <!-- Back button -->
  <a href="frontpage.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

<section class="form-container">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Update Profile</h3>
      <?php if (!empty($message)): ?>
      <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
      <?php endif; ?>
      <p style="text-align:left; padding: 0;">First Name:</p>
      <input type="text" name="Fname" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo htmlspecialchars($Fname) ?>">
      <p style="text-align:left; padding: 0;">Middle Name:</p>
      <input type="text" name="Mname" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo htmlspecialchars($Mname) ?>">
      <p style="text-align:left; padding: 0;">Last Name:</p>
      <input type="text" name="Lname" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo htmlspecialchars($Lname) ?>">
      <p style="text-align:left; padding: 0;">Phone Number:</p>
      <input type="text" name="PhoneNum" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo htmlspecialchars($PhoneNum) ?>">
      <p style="text-align:left; padding: 0;">Profile Picture:</p>
      <input type="file" name="avatar" class="box">
      <p style="text-align:left; padding: 0;">Old Password:</p>
      <input type="Password" name="old_pass" maxlength="20" placeholder="Enter your old Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <p style="text-align:left; padding: 0;">New Password:</p>
      <input type="Password" name="new_pass" maxlength="20" placeholder="Enter your new Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <p style="text-align:left; padding: 0;">Confirm Password:</p>
      <input type="Password" name="confirm_pass" maxlength="20" placeholder="Confirm your new Password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Update Now" name="submit" class="btn">
   </form>

</section>
<!-- custom js file link  -->
<script src="../js/script.js"></script>
</body>
</html>
