<?php
require 'connection.php';
session_start();

$errors = [];
$Email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check-Email'])) {
    $Email = $_POST['Email'];

    // Validate Email format
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $errors['Email'] = "Invalid Email format!";
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Prepare and execute query to check if the Email exists in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $table = "users";
        } else {
            // Email not found in users table, check admins table
            $stmt = $conn->prepare("SELECT * FROM admins WHERE Email = ?");
            $stmt->bind_param("s", $Email);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if ($admin) {
                $table = "admins";
            } else {
                $errors['Email'] = "This Email address does not exist!";
            }
        }

        if (empty($errors)) {
            $Code = rand(100000, 999999); // Generate a 6-digit Code
            
            // Prepare and execute query to update the Code in the appropriate table
            $stmt = $conn->prepare("UPDATE $table SET Code = ? WHERE Email = ?");
            $stmt->bind_param("ss", $Code, $Email);
            if ($stmt->execute()) {
                $subject = "Password Reset Code";
                $message = "Your password reset Code is: $Code";
                $sender = "From: bpsu.bindtogether@gmail.com";
                
                // Send the Email with the reset Code
                if (mail($Email, $subject, $message, $sender)) {
                    $_SESSION['info'] = "We've sent a password reset Code to your Email - $Email";
                    $_SESSION['Email'] = $Email;
                    header("Location: reset-Code.php");
                    exit();
                } else {
                    $errors['mail-error'] = "Failed to send Code!";
                }
            } else {
                $errors['db-error'] = "Database error: Failed to set Code.";
            }
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
            background-color: #f8f9fa; /* Lighten background color on hover */
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
<a href="login.php" class="btn back-button">
    <i class="fas fa-arrow-left"></i> Back
</a>

    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form action="forgot-password.php" method="POST" autocomplete="">
                    <h2 class="text-center">Forgot Password</h2>
                    <p class="text-center">Enter your Email address</p>
                    <?php 
                    ?>
                    <?php
                    if(count($errors) > 0){
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach($errors as $showerror){
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <input class="form-control" type="Email" name="Email" placeholder="Enter Email address" required value="<?php echo htmlspecialchars($Email); ?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="check-Email" value="Continue">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>
