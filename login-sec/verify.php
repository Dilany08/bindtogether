<?php
require 'connection.php';
session_start();

// Retrieve Email from session or GET parameter
$Email = isset($_SESSION['Email']) ? $_SESSION['Email'] : (isset($_GET['Email']) ? $_GET['Email'] : '');
$errors = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Code = $_POST['Code'];

    // Validate input
    if (empty($Code)) {
        $errors[] = "Verification Code is required.";
    }

    if (empty($Email)) {
        $errors[] = "Email is missing.";
    }

    if (empty($errors)) {
        // Get database connection
        $conn = getDBConnection();

        // Check if the user is in the users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ? AND Code = ?");
        $stmt->bind_param("ss", $Email, $Code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Update user status to 'verified'
            $updateStmt = $conn->prepare("UPDATE users SET status = 'verified', Code = '' WHERE Email = ?");
            $updateStmt->bind_param("s", $Email);
            $updateStmt->execute();

            $_SESSION['info'] = "Email verified successfully.";
            $_SESSION['redirect'] = 'login.php';

        } else {
            // Check if the user is in the admins table
            $stmt = $conn->prepare("SELECT * FROM admins WHERE Email = ? AND Code = ?");
            $stmt->bind_param("ss", $Email, $Code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Update admin status to 'verified'
                $updateStmt = $conn->prepare("UPDATE admins SET status = 'verified', Code = '' WHERE Email = ?");
                $updateStmt->bind_param("s", $Email);
                $updateStmt->execute();

                $_SESSION['info'] = "Email verified successfully.";
                $_SESSION['redirect'] = '../super_admin/super_admin.php';
            } else {
                $errors[] = "Invalid verification Code.";
            }
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_SESSION['info']) && isset($_SESSION['redirect'])) { ?>
                if (confirm("<?php echo $_SESSION['info']; ?>")) {
                    window.location.href = "<?php echo $_SESSION['redirect']; ?>";
                }
                <?php unset($_SESSION['info']); unset($_SESSION['redirect']); ?>
            <?php } ?>
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4 form">
                <form method="post">
                    <h2 class="text-center">Code Verification</h2>
                    <?php 
                    if (isset($_SESSION['info']) && !isset($_SESSION['redirect'])) {
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
                    <div class="form-group">
                        <input class="form-control" type="text" name="Code" placeholder="Enter verification Code" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" value="Submit">
                    </div>
                </form>
                <div class="text-center mt-3" style="color: #7D0A0A;">
                    <a href="resend_code.php?Email=<?php echo $Email; ?>">Resend verification code</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
