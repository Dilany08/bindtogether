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
$category = 'Performers Practices';

// Fetch activities for the specific category
$sql = "SELECT a.ActivityID, a.AdminID, a.Title, a.Content, a.Image, a.Category, a.Venue, a.Address, a.Date, a.Time, a.Status, 
               ad.Fname, ad.Lname 
        FROM activities a 
        JOIN admins ad ON a.AdminID = ad.AdminID 
        WHERE a.Status = 'Active' AND a.Category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $category);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/sports.css">
    <link rel="stylesheet" type="text/css" href="../css/frontpage.css">

    <style>
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
    </style>
</head>

<body>
    <?php require_once "../components/user_header.php"; ?>

    <!-- Back button -->
    <a href="performers.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <section class="show-posts">

        <h1 class="heading">Activities</h1>
        <?php if (!empty($message)) : ?>
            <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="box-container">

            <?php if ($result->num_rows > 0) { ?>
                <?php while ($fetch_activities = $result->fetch_assoc()) {
                    // Convert time to 12-hour format with AM/PM
                    $activity_time = date("h:i A", strtotime($fetch_activities['Time']));
                ?>
                    <form method="post" class="box">
                        <input type="hidden" name="ActivityID" value="<?php echo htmlspecialchars($fetch_activities['ActivityID']); ?>">
                        <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($fetch_activities['Status']); ?>">
                        <?php if (!empty($fetch_activities['Image'])) { ?>
                            <img src="../uploaded_media/<?php echo htmlspecialchars($fetch_activities['Image']); ?>" class="image" alt="">
                        <?php } ?>
                        <div class="title"><?php echo htmlspecialchars($fetch_activities['Title']); ?></div>
                        <div class="posts-Content" style="font-size: 14px;"><?php echo htmlspecialchars($fetch_activities['Content']); ?></div>

                        <div class="details" style="font-size: 12px;">
                            <p><strong>Venue:</strong> <?php echo htmlspecialchars($fetch_activities['Venue']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($fetch_activities['Address']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($fetch_activities['Date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($activity_time); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($fetch_activities['Category']); ?></p>
                        </div>
                        <div class="flex-btn">
                            <a href="tryouts.php?ActivityID=<?php echo htmlspecialchars($fetch_activities['ActivityID']); ?>" class="option-btn">Register</a>
                        </div>
                    </form>
                <?php } ?>
            <?php } else { ?>
                <p class="empty">No activities added yet!</p>
            <?php } ?>

        </div>
    </section>

    <script>
        document.querySelectorAll('.register-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                alert('Registration functionality to be implemented.');
            });
        });
    </script>

    <script src="../js/script.js"></script>
</body>

</html>