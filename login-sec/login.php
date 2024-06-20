<?php
require 'connection.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];

    $conn = getDBConnection();

    // First, check the admins table
    $stmt = $conn->prepare("SELECT 'Admin' AS Type, AdminID AS UserID, Fname, Mname, Lname, Avatar, Email, Password, PhoneNum, Status, Classification, Role, Active FROM admins WHERE Email = ?");
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        // If no result is found in the admins table, check the users table
        $stmt = $conn->prepare("SELECT 'User' AS Type, UserID AS UserID, Fname, Mname, Lname, Avatar, Email, Password, PhoneNum, Status, Active FROM users WHERE Email = ?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }

    $conn->close();

    if ($user) {
        if ($user['Active'] == 0) {
            $errors['Email'] = "Your account is no longer active. Please coordinate with your admin.";
        } else {
            if (password_verify($Password, $user['Password'])) {
                // Set session variables common to both users and admins
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['AdminID'] = $user['AdminID'];
                $_SESSION['Fname'] = $user['Fname'];
                $_SESSION['Mname'] = $user['Mname'];
                $_SESSION['Lname'] = $user['Lname'];
                $_SESSION['Avatar'] = $user['Avatar'];
                $_SESSION['Email'] = $user['Email'];
                $_SESSION['PhoneNum'] = $user['PhoneNum'];
                $_SESSION['Status'] = $user['Status'];
                $_SESSION['Classification'] = $user['Classification'];
                $_SESSION['Password'] = $user['Password'];
                $_SESSION['Role'] = $user['Role'];

                if ($user['Type'] == 'Admin') {
                    $_SESSION['Role'] = $user['Role'];
                    $_SESSION['Classification'] = $user['Classification'];
                    $_SESSION['AdminID'] = $user['UserID']; 

                    if ($user['Role'] == "Coach in Sports" || $user['Role'] == "Coach in Performers and Artists" || $user['Role'] == "Student Athletes Officer" || $user['Role'] == "Student Performers Officer") {
                        header("Location: ../admin/dashboard.php");
                    } elseif ($user['Role'] == "Sports Director" || $user['Role'] == "Performers and Artists Director") {
                        header("Location: ../Admin1/super_admin.php");
                    } elseif ($user['Role'] == "SuperAdmin") {
                        header("Location: ../super_admin/super_admin.php");
                    } else {
                        $errors['Email'] = "Incorrect Email or Password.";
                    }
                } else {
                    header("Location: ../pages/frontpage.php");
                }
                exit();
            } else {
                $errors['Email'] = "Incorrect Email or Password.";
            }
        }
    } else {
        $errors['Email'] = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <style>
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: white;
            color: #7D0A0A;
            border-color: #7D0A0A;
            font-weight: 600;
        }

        .back-button:hover {
            background-color: #f8f9fa;
            color: #7D0A0A;
            transform: scale(1.1);
        }

        .back-button i {
            margin-right: 2px;
        }
    </style>
</head>

<body>
    <!-- Back button -->
    <a href="../pages/homepage.php" class="btn back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form login-form">
                <form action="login.php" method="POST" enctype="multipart/form-data" autocomplete="on">
                    <h2 class="text-center">Login Now!</h2>
                    <p class="text-center">Login with your Email and Password.</p>
                    <?php
                    if (count($errors) > 0) {
                    ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="form-group">
                        <input class="form-control" type="Email" name="Email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="Password" name="Password" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Password" required>
                    </div>
                    <div class="link forget-pass text-left"><a href="forgot-password.php">Forgot Password?</a></div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="login" value="Login">
                    </div>
                    <div class="link login-link text-center">Don't have an account? <a href="signup.php">Signup now</a></div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
