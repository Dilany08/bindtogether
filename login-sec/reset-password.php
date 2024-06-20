<?php
require 'connection.php';
session_start();

$errors = [];

if (!isset($_SESSION['Email']) || !isset($_SESSION['UserType'])) {
    header('Location: forgot-password.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change-password'])) {
    $Password = $_POST['Password'];
    $Cpassword = $_POST['Cpassword'];
    $Email = $_SESSION['Email'];

    if (!preg_match('/^[a-zA-Z0-9]+$/', $Password)) {
        $errors['Password'] = "Password must be alphanumeric.";
    }

    if ($Password !== $Cpassword) {
        $errors['Cpassword'] = "Passwords do not match.";
    }

    if (count($errors) === 0) {
        $hashed_password = password_hash($Password, PASSWORD_DEFAULT);
        $conn = getDBConnection();

        // Check if the email belongs to a user or an admin
        if ($_SESSION['UserType'] == 'user') {
            $stmt = $conn->prepare("UPDATE users SET Password = ?, Code = '' WHERE Email = ?");
        } elseif ($_SESSION['UserType'] == 'admin') {
            $stmt = $conn->prepare("UPDATE admins SET Password = ?, Code = '' WHERE Email = ?");
        } else {
            $errors['UserType'] = "Invalid user type.";
        }

        if (isset($stmt)) {
            $stmt->bind_param("ss", $hashed_password, $Email);
            if ($stmt->execute()) {
                $_SESSION['info'] = "Your password has been changed. You can now log in.";
                header("Location: password-changed.php");
                exit();
            } else {
                $errors['db-error'] = "Database error: Failed to change password.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create a New Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="reset-password.php" method="POST" autocomplete="off">
                    <h2 class="text-center">New Password</h2>
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
                        <input class="form-control" type="Password" name="Password" placeholder="Create new Password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="Password" name="Cpassword" placeholder="Confirm your password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="change-password" value="Change">
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>