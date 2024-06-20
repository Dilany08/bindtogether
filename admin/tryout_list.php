<?php require_once "../login-sec/connection.php"; ?>
<?php
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Lname = $_SESSION['Lname'];
$Avatar = $_SESSION['Avatar'];
$AdminID = $_SESSION['AdminID'] ?? '';
$PostID = $_SESSION['PostID'] ?? '';

// Get database connection
$conn = getDBConnection();

// Function to send email
function sendEmail($to, $subject, $message)
{
    // Additional headers
    $headers = "From: bpsu.bindtogether@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send email
    mail($to, $subject, $message, $headers);
}

// Handle form submissions for approve, decline, and delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $UserID = $_POST['UserID'];
    $TryOutID = $_POST['TryOutID']; // Ensure TryOutID is being passed
    $action = $_POST['action'];
    $email = $_POST['email'];
    $name = $_POST['name'];

    if ($action == 'Approve') {
        // Update the approval status to 'Approved'
        $stmt = $conn->prepare("UPDATE tryouts SET ApprovalStatus = 'Approved' WHERE UserID = ?");
        $stmt->bind_param('i', $UserID);
        if ($stmt->execute()) {
            // Send approval email
            $subject = "Tryout Approval Notification";
            $message = "Dear $name,<br><br>Congratulations! You've passed the tryouts. Please wait for the announcement of the first training or practice that you will be attending.<br><br>Best Regards,<br>Team";
            sendEmail($email, $subject, $message);
        }
        $stmt->close();
    } elseif ($action == 'Decline') {
        // Update the approval status to 'Declined'
        $stmt = $conn->prepare("UPDATE tryouts SET ApprovalStatus = 'Declined' WHERE UserID = ?");
        $stmt->bind_param('i', $UserID);
        if ($stmt->execute()) {
            // Send decline email
            $subject = "Tryout Decline Notification";
            $message = "Dear $name,<br><br>We regret to inform you that you did not pass the tryouts. Thank you for your interest.<br><br>Best Regards,<br>Team";
            sendEmail($email, $subject, $message);
        }
        $stmt->close();
    } elseif ($action == 'Delete') {
        // Delete the tryout entry
        $stmt = $conn->prepare("DELETE FROM tryouts WHERE TryOutID = ?");
        $stmt->bind_param('i', $TryOutID);
        if ($stmt->execute()) {
            // Entry deleted successfully
        }
        $stmt->close();
    }
    // Refresh the page to reflect changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tryouts Accounts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
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
    </style>

</head>

<body>
    <?php require_once "../components/header_coach.php"; ?>

    <a href="dashboard.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <!-- tryouts accounts section starts  -->

    <section class="accounts">

        <h1 class="heading">Tryouts/Audtion Accounts</h1>

        <div class="box-container">

            <div class="box" style="order: -2;">
                <p>Register New Admin</p>
                <a href="../login-sec/signup-admin.php" class="option-btn" style="margin-bottom: .5rem;">register</a>
            </div>

            <?php
            // Determine which users to fetch based on admin role
            $logged_in_admin_role = $_SESSION['Role'];

            $role_to_fetch = '';
            if ($logged_in_admin_role == 'Coach in Sports' || $logged_in_admin_role == 'Student Athletes Officer') {
                $role_to_fetch = 'Athletes';
            } elseif ($logged_in_admin_role == 'Coach in Performers and Artists' || $logged_in_admin_role == 'Student Performers Officer') {
                $role_to_fetch = 'Performers and Artists';
            }

            if ($role_to_fetch != '') {
                // Fetch all data from the tryouts table joined with users table based on role
                $sql = "SELECT tryouts.*, users.Role FROM tryouts 
               JOIN users ON tryouts.UserID = users.UserID 
               WHERE users.Role = ?";

                // Prepare and execute the statement
                $select_account = $conn->prepare($sql);
                $select_account->bind_param('s', $role_to_fetch);
                $select_account->execute();
                $result_account = $select_account->get_result();

                if ($result_account->num_rows > 0) {
                    while ($fetch_accounts = $result_account->fetch_assoc()) {
            ?>
                        <div class="box" id="tryout-<?php echo htmlspecialchars($fetch_accounts['TryOutID']); ?>">
                            <p> Activity ID: <span><?php echo htmlspecialchars($fetch_accounts['ActivityID']); ?></span> </p>
                            <p> User ID: <span><?php echo htmlspecialchars($fetch_accounts['UserID']); ?></span> </p>
                            <p> Name: <span><?php echo htmlspecialchars($fetch_accounts['Name']); ?></span> </p>
                            <p> Email: <span><?php echo htmlspecialchars($fetch_accounts['Email']); ?></span> </p>
                            <p> Student Number: <span><?php echo htmlspecialchars($fetch_accounts['StudNum']); ?></span> </p>
                            <p> Contact Number: <span><?php echo htmlspecialchars($fetch_accounts['ContactNum']); ?></span> </p>
                            <p> Year Level: <span><?php echo htmlspecialchars($fetch_accounts['YearLevel']); ?></span> </p>
                            <p> Program: <span><?php echo htmlspecialchars($fetch_accounts['Program']); ?></span> </p>
                            <p> College: <span><?php echo htmlspecialchars($fetch_accounts['College']); ?></span> </p>
                            <p> Gender: <span><?php echo htmlspecialchars($fetch_accounts['Gender']); ?></span> </p>
                            <p> Address: <span><?php echo htmlspecialchars($fetch_accounts['Address']); ?></span> </p>
                            <p> Father Name: <span><?php echo htmlspecialchars($fetch_accounts['FatherName']); ?></span> </p>
                            <p> Mother Name: <span><?php echo htmlspecialchars($fetch_accounts['MotherName']); ?></span> </p>
                            <p> Parent Contact Number: <span><?php echo htmlspecialchars($fetch_accounts['ParentContactNum']); ?></span> </p>
                            <p> Date of Birth: <span><?php echo htmlspecialchars($fetch_accounts['DateOfBirth']); ?></span> </p>
                            <p> Height: <span><?php echo htmlspecialchars($fetch_accounts['Height']); ?></span> </p>
                            <p> Weight: <span><?php echo htmlspecialchars($fetch_accounts['Weight']); ?></span> </p>
                            <p> Role: <span><?php echo htmlspecialchars($fetch_accounts['Role']); ?></span></p>

                            <form action="" method="POST">
                                <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($fetch_accounts['UserID']); ?>">
                                <input type="hidden" name="TryOutID" value="<?php echo htmlspecialchars($fetch_accounts['TryOutID']); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($fetch_accounts['Email']); ?>">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($fetch_accounts['Name']); ?>">
                                <?php if ($fetch_accounts['ApprovalStatus'] == 'Approved' || $fetch_accounts['ApprovalStatus'] == 'Declined') : ?>
                                    <button type="submit" name="action" value="Delete" class="delete-btn" style="margin-bottom: .5rem;">Delete</button>
                                <?php else : ?>
                                    <button type="submit" name="action" value="Approve" class="option-btn" style="margin-bottom: .5rem;">Approve</button>
                                    <button type="submit" name="action" value="Decline" class="delete-btn" style="margin-bottom: .5rem;">Decline</button>
                                <?php endif; ?>
                            </form>
                        </div>
            <?php
                    }
                } else {
                    echo '<p class="empty">No accounts available</p>';
                }
            }
            ?>
        </div>
    </section>
    <!-- tryouts accounts section ends -->

</body>

</html>