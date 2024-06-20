<?php
require 'connection.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $Fname = $_POST['Fname'];
    $Mname = $_POST['Mname'];
    $Lname = $_POST['Lname'];
    $PhoneNum = $_POST['PhoneNum'];
    $Email = $_POST['Email'];
    $Gender = $_POST['Gender'];
    $Password = $_POST['Password'];
    $CPassword = $_POST['CPassword'];

    // Validate Email domain
    if (!preg_match('/@bpsu\.edu\.ph$/', $Email)) {
        $errors['Email'] = "Email must be a BPSU Email (example@bpsu.edu.ph).";
    } else {
        // Check if Email already exists in users table
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['Email'] = "$Email is already existed";
        }
        $stmt->close();
        $conn->close();
    }

    // Check if Passwords match
    if ($Password !== $CPassword) {
        $errors['Password'] = "Passwords do not match.";
    }

    // Check if Password is strong
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[@*!])[a-zA-Z0-9@*!]{8,}$/', $Password)) {
        $errors['Password'] = "Password must be at least 8 characters long, contain both letters and numbers, <br/> and include at least one special symbol (@, *, !).";
    }

    // Validate file upload
    $target_dir = "../upload/";
    $target_file = $target_dir . basename($_FILES["Avatar"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!empty($_FILES["Avatar"]["tmp_name"])) {
        $check = getimagesize($_FILES["Avatar"]["tmp_name"]);
        if ($check === false) {
            $errors['Avatar'] = "File is not an image.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['Avatar'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["Avatar"]["tmp_name"], $target_file)) {
            $errors['Avatar'] = "There was an error uploading your file.";
        }
    } else {
        // Set default avatar if no file is uploaded
        $target_file = "../upload/default_avatar.jpg";
    }

    if (count($errors) === 0) {
        $conn = getDBConnection();
        $hashed_Password = password_hash($Password, PASSWORD_DEFAULT);
        $Code = rand(100000, 999999); // Generate a 6-digit Code
        $Status = "not verified";

        $stmt = $conn->prepare("INSERT INTO users (Fname, Mname, Lname, PhoneNum, Email, Gender, Avatar, Password, Code, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $Fname, $Mname, $Lname, $PhoneNum, $Email, $Gender, $target_file, $hashed_Password, $Code, $Status);

        if ($stmt->execute()) {
            $subject = "Email Verification Code";
            $message = "Your verification Code is: $Code";
            $sender = "From: bpsu.bindtogether@gmail.com";
            if (mail($Email, $subject, $message, $sender)) {
                $_SESSION['info'] = "We've sent a verification Code to your Email - $Email";
                $_SESSION['Email'] = $Email;
                header("Location: verify.php");
                exit();
            } else {
                $errors['mail-error'] = "Failed while sending the Code. Error: " . error_get_last()['message'];
            }
        } else {
            $errors['db-error'] = "Database error: Failed to register.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/signup.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
    <a href="javascript:history.back()" class="btn back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <section class="container">
        <h2>Sign Up Now!</h2>
        <form action="signup.php" class="form" method="post" enctype="multipart/form-data" autocomplete="" onsubmit="return validateForm()">
            <?php
            if (count($errors) > 0) {
                echo '<div class="alert alert-danger text-center">';
                foreach ($errors as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';
            }
            ?>

            <div class="column">
                <div class="input-box">
                    <label> First Name: </label>
                    <input type="text" name="Fname" id="Fname" placeholder="First Name" required>
                </div>
                <div class="input-box">
                    <label> Middle Name: </label>
                    <input type="text" name="Mname" id="Mname" placeholder="Middle Name">
                </div>
                <div class="input-box">
                    <label> Last Name: </label>
                    <input type="text" name="Lname" id="Lname" placeholder="Last Name" required>
                </div>
            </div>

            <div class="column">
                <div class="input-box">
                    <label> E-mail Address: </label>
                    <input type="text" name="Email" placeholder="@bpsu.edu.ph" required>
                </div>
                <div class="input-box">
                    <label> Phone Number: </label>
                    <input type="text" name="PhoneNum" placeholder="Contact #" required>
                </div>

            </div>

            <div class="gender-box">
                <div class="gender-option" required>
                    <h3> Gender: </h3>
                    <div class="Gender">
                        <input type="radio" name="Gender" value="Male" required> Male
                        <input type="radio" name="Gender" value="Female" required> Female
                        <input type="radio" name="Gender" value="other" required> Prefer not to say
                    </div>
                </div>
            </div>

            <div class="column">
                <div class="profile">
                    <label>Upload Picture: </label>
                    <input type="file" name="Avatar">
                </div>
            </div>

            <div class="column">
                <div class="input-box">
                    <h3> Password: </h3>
                    <input type="Password" name="Password" id="Password" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Enter your Password" required>
                </div>
                <div class="input-box">
                    <h3> Confirm Password: </h3>
                    <input type="Password" name="CPassword" id="CPassword" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Confirm Password" required>
                </div>
            </div>

            <input class="button" type="submit" name="signup" value="Signup">
            <div class="link">Already have an account? <a href="login.php">Login here</a></div>
        </form>
    </section>

    <script>
        function validateForm() {
            var Mname = document.getElementById("Mname").value;
            if (Mname.trim() === "") {
                var confirmNoMiddleName = confirm("You have not entered a middle name. Do you confirm that you do not have a middle name?");
                if (!confirmNoMiddleName) {
                    return false;
                }
            }

            var Password = document.getElementById("Password").value;
            var CPassword = document.getElementById("CPassword").value;

            if (Password !== CPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>