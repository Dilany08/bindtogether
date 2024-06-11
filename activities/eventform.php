<?php
require_once '../login-sec/connection.php';
session_start();

if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];

$conn = getDBConnection();
$ActivityID = isset($_GET['ActivityID']) ? $_GET['ActivityID'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ActivityID = $_POST['ActivityID'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $studNum = $_POST['studNum'];
    $contactNum = $_POST['contactNum'];
    $yearLevel = $_POST['yearLevel'];
    $program = $_POST['program'];
    $college = $_POST['college'];

    // Insert the form data into the EventForm table
    $sql = "INSERT INTO EventForm (ActivityID, UserID, Name, Email, StudNum, ContactNum, YearLevel, Program, College) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('issssssss', $ActivityID, $_SESSION['UserID'], $name, $email, $studNum, $contactNum, $yearLevel, $program, $college);

    if ($bind === false) {
        die('Bind failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();

    if ($exec) {
        // Send confirmation email
        $to = $email;
        $subject = "Event Registration Confirmation";
        $message = "Dear $name,\n\nThank you for registering for the event. Here are your details:\n\nName: $name\nEmail: $email\nStudent Number: $studNum\nContact Number: $contactNum\nYear Level: $yearLevel\nProgram: $program\nCollege: $college\n\nWe look forward to your participation.\n\nBest regards,\nEvent Team";
        $headers = "From: bpsu.bindtogether@gmail.com";

        mail($to, $subject, $message, $headers);

        echo 'success';
    } else {
        echo 'Execute failed: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Upcoming Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/frontpage.css">
    <link rel="stylesheet" type="text/css" href="../css/activities.css">
    <style>
        .form-container {
            width: 50%;
            margin: 0 auto;
            text-align: center;
            padding: 2rem;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-container h1 {
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group button {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            background-color: #7D0A0A;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #c72d2d;
        }

        .btn {
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

        h2 {
            text-align: center;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }

        #successDialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 1000;
        }

        #successDialog p {
            margin: 0 0 20px;
        }

        #successDialog button {
            background: #7D0A0A;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 auto;
            display: block;
        }

        #successDialog button:hover {
            background: #c72d2d;
        }

        #otherProgramField {
            display: none;
        }
    </style>
</head>
<body>
<?php require_once "../components/user_header.php"; ?>
<!-- Back button -->
<a href="activities.php" class="btn btn-secondary back-button">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>
<h2>Register for Upcoming Events</h2>

<div class="form-container">
    <form id="eventForm" action="eventform.php" method="POST">
        <input type="hidden" name="ActivityID" value="<?php echo htmlspecialchars($ActivityID); ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="studNum">Student Number:</label>
            <input type="text" id="studNum" name="studNum" required>
        </div>

        <div class="form-group">
            <label for="contactNum">Contact Number:</label>
            <input type="text" id="contactNum" name="contactNum" required>
        </div>

        <div class="form-group">
            <label for="yearLevel">Year Level:</label>
            <select id="yearLevel" name="yearLevel" required>
            <option hidden>Year Level</option>
                <option value="First Year">First Year</option>
                <option value="Second Year">Second Year</option>
                <option value="Third Year">Third Year</option>
                <option value="Fourth Year">Fourth Year</option>
                <option value="Fifth Year">Fifth Year</option>
            </select>
        </div>

        <div class="form-group">
            <label for="program">Program:</label>
            <select id="program" name="program" required>
            <option hidden>Program</option>
                <option value="Bachelor of Science in Hospitality Management">Bachelor of Science in Hospitality Management</option>
                <option value="Bachelor of Science in Tourism Management">Bachelor of Science in Tourism Management</option>
                <option value="Bachelor of Science in Developmental Communication">Bachelor of Science in Developmental Communication</option>
                <option value="Bachelor of Arts in Communication">Bachelor of Arts in Communication</option>
                <option value="Bachelor of Science in Mechanical Engineering">Bachelor of Science in Mechanical Engineering</option>
                <option value="Bachelor of Science in Railway Engineering">Bachelor of Science in Railway Engineering</option>
                <option value="Bachelor of Science in Industrial Engineering">Bachelor of Science in Industrial Engineering</option>
                <option value="Bachelor of Science in Electronics Engineering">Bachelor of Science in Electronics Engineering</option>
                <option value="Bachelor of Science in Electrical Engineering">Bachelor of Science in Electrical Engineering</option>
                <option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
                <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
                <option value="Bachelor of Science in Architecture">Bachelor of Science in Architecture</option>
                <option value="Bachelor of Science in Industrial Technology">Bachelor of Science in Industrial Technology</option>
                <option value="Bachelor of Technical - Vocational Teacher Education">Bachelor of Technical - Vocational Teacher Education</option>
                <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                <option value="Bachelor of Science in Data Science">Bachelor of Science in Data Science</option>
                <option value="Bachelor of Science in Entertainment and Multimedia Computing">Bachelor of Science in Entertainment and Multimedia Computing</option>
                <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                <option value="Bachelor of Science in Midwifery">Bachelor of Science in Midwifery</option>
                <option value="Bachelor of Science in Nursing">Bachelor of Science in Nursing</option>
                <option value="Bachelor of Science in Public Health">Bachelor of Science in Public Health</option>
                <option value="Others:">Others:</option>
            </select>
            <input type="text" id="otherProgramField" name="otherProgram" placeholder="Please specify your program">
        </div>

        <div class="form-group">
            <label for="college">College:</label>
            <input type="text" id="college" name="college" required>
        </div>

        <div class="form-group">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    const programSelect = document.getElementById('program');
    const otherProgramField = document.getElementById('otherProgramField');

    programSelect.addEventListener('change', function() {
        if (programSelect.value === 'Others:') {
            otherProgramField.style.display = 'block';
            otherProgramField.required = true;
        } else {
            otherProgramField.style.display = 'none';
            otherProgramField.required = false;
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('eventform.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(response => {
            if (response.trim() === 'success') {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('successDialog').style.display = 'block';
            } else {
                alert(response); // Display the server's error message
            }
        })
        .catch(() => {
            alert('There was an error connecting to the server. Please try again.');
        });
    });

    document.getElementById('overlay').addEventListener('click', function() {
        this.style.display = 'none';
        document.getElementById('successDialog').style.display = 'none';
        location.reload(); // Reload the page
    });

    document.getElementById('closeDialog').addEventListener('click', function() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('successDialog').style.display = 'none';
        location.reload(); // Reload the page
    });
});
</script>

<div id="overlay"></div>
<div id="successDialog">
    <p>Successfully registered!</p>
    <button id="closeDialog">Close</button>
</div>

</body>
</html>

