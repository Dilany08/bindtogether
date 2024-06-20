<?php
require 'connection.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Code = $_POST['Code'];
    $Email = $_SESSION['Email'];

    $conn = getDBConnection();

    // Check if the email belongs to a user or an admin
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE Email = ? AND Code = ?");
    $stmt_user->bind_param("ss", $Email, $Code);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

    $stmt_admin = $conn->prepare("SELECT * FROM admins WHERE Email = ? AND Code = ?");
    $stmt_admin->bind_param("ss", $Email, $Code);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin = $result_admin->fetch_assoc();

    if ($user) {
        $_SESSION['Code'] = $Code;
        $_SESSION['UserType'] = 'user';
        header("Location: reset-password.php");
        exit();
    } elseif ($admin) {
        $_SESSION['Code'] = $Code;
        $_SESSION['UserType'] = 'admin';
        header("Location: reset-password.php");
        exit();
    } else {
        $errors['Code-error'] = "You've entered an incorrect Code.";
    }

    $stmt_user->close();
    $stmt_admin->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Code Verification</title>
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
            /* Lighten background color on hover */
            color: #7D0A0A;
            -webkit-transform: scale(1);
            transform: scale(1.1);
        }

        .back-button i {
            margin-right: 2px;

        }
    </style>
</head>

<body>
    <!-- Back button -->
    <a href="forgot-password.php" class="btn back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="reset-Code.php" method="POST" autocomplete="off">
                    <h2 class="text-center">Code Verification</h2>
                    <?php
                    if (isset($_SESSION['info'])) {
                    ?>
                        <div class="alert alert-success text-center" style="padding: 0.4rem 0.4rem">
                            <?php echo $_SESSION['info']; ?>
                        </div>
                    <?php
                    }
                    ?>
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
                        <input class="form-control" type="number" name="Code" placeholder="Enter Code" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="check-reset-Code" value="Submit">
                    </div>
                </form>
                <div class="text-center">
                    <a href="resendcode-forgotpass.php">Resend Code</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>