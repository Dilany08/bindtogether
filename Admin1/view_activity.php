<?php
require_once "../login-sec/connection.php";
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'] ?? '';
$Lname = $_SESSION['Lname'] ?? '';
$AdminID = $_SESSION['AdminID'] ?? '';

// Get the database connection
$conn = getDBConnection();

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Avatar from user table
$select_user_query = $conn->prepare("SELECT Avatar FROM admins WHERE AdminID = ?");
$select_user_query->bind_param("i", $AdminID);
$select_user_query->execute();
$result_user = $select_user_query->get_result();
$user_data = $result_user->fetch_assoc();
$Avatar = $user_data['Avatar'] ?? '';

// Delete post functionality
if (isset($_POST['delete'])) {
    $ActivityID = (int)$_POST['ActivityID'];

    // Delete the post media
    $delete_media_query = $conn->prepare("SELECT Image FROM activities WHERE ActivityID = ?");
    $delete_media_query->bind_param("i", $ActivityID);
    $delete_media_query->execute();
    $result = $delete_media_query->get_result();
    $fetch_delete_media = $result->fetch_assoc();
    if (!empty($fetch_delete_media['Image'])) {
        unlink('../uploaded_media/' . $fetch_delete_media['Image']);
    }

    // Delete the post
    $delete_post_query = $conn->prepare("DELETE FROM activities WHERE ActivityID = ?");
    $delete_post_query->bind_param("i", $ActivityID);
    $delete_post_query->execute();

    $message = 'Activity deleted successfully!';
    $message_class = "alert-success";
}

// Update activity status functionality
if (isset($_POST['update_status'])) {
    $ActivityID = (int)$_POST['ActivityID'];
    $new_status = $_POST['current_status'] === 'Active' ? 'Inactive' : 'Active';

    // Update the activity status
    $update_status_query = $conn->prepare("UPDATE activities SET Status = ? WHERE ActivityID = ?");
    $update_status_query->bind_param("si", $new_status, $ActivityID);
    $update_status_query->execute();

    $message = 'Activity status updated successfully!';
    $message_class = "alert-success";
}

// Assume $logged_in_admin_role and $AdminID are fetched from session or other means
$logged_in_admin_role = $_SESSION['Role'];
$AdminID = $_SESSION['AdminID'];

$query = "";
if ($logged_in_admin_role == 'Sports Director') {
    $query = "SELECT activities.*, admins.Fname, admins.Lname 
              FROM activities 
              JOIN admins ON activities.AdminID = admins.AdminID 
              WHERE activities.Status = 'Active' 
              AND (admins.Role IN ('Coach in Sports', 'Student Athletes Officer', 'Sports Director') OR activities.AdminID = ?)";
} elseif ($logged_in_admin_role == 'Performers and Artists Director') {
    $query = "SELECT activities.*, admins.Fname, admins.Lname 
              FROM activities 
              JOIN admins ON activities.AdminID = admins.AdminID 
              WHERE activities.Status = 'Active' 
              AND (admins.Role IN ('Coach in Performers and Artists', 'Student Performers Officer', 'Performers and Artists Director') OR activities.AdminID = ?)";
            }

            $select_activities_query = $conn->prepare($query);
            $select_activities_query->bind_param("i", $AdminID);
            $select_activities_query->execute();
            $result = $select_activities_query->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Activities</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <Style>
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
    </Style>
</head>
<body>

<?php require_once "../components/header.php"; ?>
<a href="super_admin.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

<section class="show-posts">

   <h1 class="heading">Activities</h1>
   <?php if (!empty($message)): ?>
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
                <div class="user">
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($fetch_activities['Fname'] . ' ' . $fetch_activities['Lname']); ?></span>
                        <div><?php echo htmlspecialchars($fetch_activities['Date'] ?? ''); ?></div>
                    </div>
                </div>
                <?php if (!empty($fetch_activities['Image'])) { ?>
                    <img src="../uploaded_media/<?php echo htmlspecialchars($fetch_activities['Image']); ?>" class="image" alt="">
                <?php } ?>
                <div class="status" style="background-color:<?php echo ($fetch_activities['Status'] == 'Active') ? 'limegreen' : 'coral'; ?>;">
                    <?php echo htmlspecialchars($fetch_activities['Status']); ?>
                </div>
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
                    <a href="edit_activity.php?ActivityID=<?php echo htmlspecialchars($fetch_activities['ActivityID']); ?>" class="option-btn">Edit</a>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Delete this activity?');">Delete</button>
                </div>
                <button type="submit" name="update_status" class="option-btn"><?php echo ($fetch_activities['Status'] == 'Active') ? 'Deactivate' : 'Activate'; ?></button>
            </form>
        <?php } ?>
    <?php } else { ?>
        <p class="empty">No activities added yet! <a href="add_activity.php" class="btn" style="margin-top:1.5rem;">Add Activity</a></p>
    <?php } ?>

    </div>
</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

<?php
$conn->close();
?>
