<?php
require 'connection.php';
session_start();

// Get the email from session or GET parameter
$Email = isset($_SESSION['Email']) ? $_SESSION['Email'] : (isset($_GET['Email']) ? $_GET['Email'] : '');
$errors = [];

if (empty($Email)) {
    $errors[] = "Email is missing.";
} else {
    $conn = getDBConnection();

    // Check if the email belongs to a user or an admin
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt_user->bind_param("s", $Email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

    $stmt_admin = $conn->prepare("SELECT * FROM admins WHERE Email = ?");
    $stmt_admin->bind_param("s", $Email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin = $result_admin->fetch_assoc();

    if ($user || $admin) {
        $Code = rand(100000, 999999); // Generate a 6-digit code

        // Update the code for either user or admin
        if ($user) {
            $stmt_update = $conn->prepare("UPDATE users SET Code = ? WHERE Email = ?");
        } else {
            $stmt_update = $conn->prepare("UPDATE admins SET Code = ? WHERE Email = ?");
        }
        $stmt_update->bind_param("ss", $Code, $Email);
        $stmt_update->execute();

        // Send email with the new code
        $subject = "Resend Verification Code";
        $message = "Your verification code is: $Code";
        $sender = "From: bpsu.bindtogether@gmail.com";

        if (mail($Email, $subject, $message, $sender)) {
            $_SESSION['info'] = "A new verification code has been sent to your email - $Email";
            header('Location: verify.php');
            exit();
        } else {
            $errors[] = "Failed to send the verification code!";
        }

        $stmt_update->close();
    } else {
        $errors[] = "No account found with this email.";
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
    <title>Resend Verification Code</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <h2 class="text-center">Resend Verification Code</h2>
                <?php
                if (isset($_SESSION['info'])) {
                ?>
                    <div class="alert alert-success text-center">
                        <?php echo $_SESSION['info']; ?>
                    </div>
                <?php
                    unset($_SESSION['info']);
                }
                ?>
                <?php
                if (count($errors) > 0) {
                ?>
                    <div class="alert alert-danger text-center">
                        <?php
                        foreach ($errors as $showerror) {
                            echo $showerror . "<br>";
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>