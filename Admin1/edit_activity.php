<?php
require_once "../login-sec/connection.php";
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

// Get session variables
$Fname = $_SESSION['Fname'] ?? '';
$Lname = $_SESSION['Lname'] ?? '';
$Avatar = $_SESSION['Avatar'] ?? '';

$message = '';
$message_class = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getDBConnection();

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the Activity ID is set in the URL
    if (!isset($_GET['ActivityID'])) {
        die("Activity ID is not set.");
    }

    $ActivityID = $_GET['ActivityID'];

    if (isset($_POST['save'])) {
        $Title = $_POST['Title'];
        $Content = $_POST['Content'];
        $Category = $_POST['Category'];
        $Status = $_POST['Status'];
        $Venue = $_POST['Venue'];
        $Address = $_POST['Address'];
        $Date = $_POST['Date'];
        $Time = $_POST['Time'];
        $old_Image = $_POST['old_Image'];

        // Check if the form data is valid
        if (empty($Title) || empty($Content) || empty($Category) || empty($Status) || empty($Venue) || empty($Address) || empty($Date) || empty($Time)) {
            $message = 'All fields are required.';
            $message_class = "alert-danger";
        } else {
            // Prepare the media update if a new media file is uploaded
            $Image = $old_Image; // Default to old Image
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $Image = $_FILES['media']['name'];
                $media_size = $_FILES['media']['size'];
                $media_tmp_name = $_FILES['media']['tmp_name'];
                $media_folder = '../activityPictures/' . $Image;

                if ($media_size > 5000000) { // 5MB max file size
                    $message = 'Media size is too large.';
                    $message_class = "alert-danger";
                } else {
                    if (move_uploaded_file($media_tmp_name, $media_folder)) {
                        // Delete old media from server if new media is uploaded successfully
                        if ($old_Image != '' && file_exists('../activityPictures/' . $old_Image)) {
                            unlink('../activityPictures/' . $old_Image);
                        }
                    } else {
                        $message = 'Error uploading media.';
                        $message_class = "alert-danger";
                    }
                }
            }

            // Prepare the update query
            $stmt = $conn->prepare("UPDATE activities SET Title = ?, Content = ?, Category = ?, Venue = ?, Address = ?, Date = ?, Time = ?, Image = ?, Status = ? WHERE ActivityID = ?");
            $stmt->bind_param("sssssssssi", $Title, $Content, $Category, $Venue, $Address, $Date, $Time, $Image, $Status, $ActivityID);

            if ($stmt->execute()) {
                $message = 'Activity updated successfully.';
                $message_class = "alert-success";
            } else {
                $message = 'Error updating activity: ' . $stmt->error;
                $message_class = "alert-danger";
            }

            $stmt->close();
        }
    }

    // Delete activity handling
    if (isset($_POST['delete_activity'])) {
        $stmt = $conn->prepare("DELETE FROM activities WHERE ActivityID = ?");
        $stmt->bind_param("i", $ActivityID);

        if ($stmt->execute()) {
            $message = 'Activity deleted successfully.';
            $message_class = "alert-success";
        } else {
            $message = 'Error deleting activity: ' . $stmt->error;
            $message_class = "alert-danger";
        }

        $stmt->close();
    }

    // Delete media handling
    if (isset($_POST['delete_media'])) {
        $Image = $_POST['old_Image'];

        $stmt = $conn->prepare("UPDATE activities SET Image = '' WHERE ActivityID = ?");
        $stmt->bind_param("i", $ActivityID);

        if ($stmt->execute()) {
            if ($Image != '' && unlink('../activityPictures/' . $Image)) {
                $message = 'Media deleted successfully.';
                $message_class = "alert-success";
            } else {
                $message = 'Error deleting media from server.';
                $message_class = "alert-danger";
            }
        } else {
            $message = 'Error removing media from activity: ' . $stmt->error;
            $message_class = "alert-danger";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

<a href="view_posts.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

<section class="post-editor">
    <h1 class="heading">Edit Activity</h1>

    <?php
    $conn = getDBConnection();

    if (isset($_GET['ActivityID'])) {
        $ActivityID = $_GET['ActivityID'];
        $select_activities = $conn->prepare("SELECT * FROM activities WHERE ActivityID = ?");
        $select_activities->bind_param("i", $ActivityID);
        $select_activities->execute();
        $result = $select_activities->get_result();

        if ($result->num_rows > 0) {
            while ($fetch_activities = $result->fetch_assoc()) {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="old_Image" value="<?php echo htmlspecialchars($fetch_activities['Image']); ?>">
        <input type="hidden" name="ActivityID" value="<?php echo htmlspecialchars($fetch_activities['ActivityID']); ?>">
        <?php if (!empty($message)): ?>
      <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
   <?php endif; ?>
        <p>Activity Status <span>*</span></p>
        <select name="Status" class="box" required>
            <option value="<?php echo htmlspecialchars($fetch_activities['Status']); ?>" selected><?php echo htmlspecialchars($fetch_activities['Status']); ?></option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        <p>Activity Title <span>*</span></p>
        <input type="text" name="Title" maxlength="100" required placeholder="Add activity Title" class="box" value="<?php echo htmlspecialchars($fetch_activities['Title']); ?>">
        <p>Activity Content <span>*</span></p>
        <textarea name="Content" class="box" required maxlength="10000" placeholder="Write your Content..." cols="30" rows="10"><?php echo htmlspecialchars($fetch_activities['Content']); ?></textarea>
        <p>Activity Category <span>*</span></p>
        <select name="Category" class="box" required>
            <option value="<?php echo htmlspecialchars($fetch_activities['Category']); ?>" selected><?php echo htmlspecialchars($fetch_activities['Category']); ?></option>
            <option value="Sports">Sports</option>
            <option value="Performers and Artists">Performers and Artists</option>
            <option value="Upcoming Events">Upcoming Events</option>
            <option value="Competitions">Competitions</option>
            <option value="Practices">Practices</option>
            <option value="Auditions">Auditions</option>
        </select>
        <p>Venue <span>*</span></p>
        <input type="text" name="Venue" maxlength="255" required placeholder="Add event venue" class="box" value="<?php echo htmlspecialchars($fetch_activities['Venue']); ?>">
        <p>Address <span>*</span></p>
        <input type="text" name="Address" maxlength="255" required placeholder="Add event address" class="box" value="<?php echo htmlspecialchars($fetch_activities['Address']); ?>">
        <p>Date <span>*</span></p>
        <input type="date" name="Date" required class="box" value="<?php echo htmlspecialchars($fetch_activities['Date']); ?>">
        <p>Time <span>*</span></p>
        <input type="time" name="Time" required class="box" value="<?php echo htmlspecialchars($fetch_activities['Time']); ?>">
        <p>Media</p>
        <input type="file" name="media" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
        <?php if ($fetch_activities['Image'] != '') { ?>
            <p>Current Media:</p>
            <img src="../activityPictures/<?php echo htmlspecialchars($fetch_activities['Image']); ?>" class="image" alt="">
            <input type="submit" value="Delete Media" class="inline-delete-btn" name="delete_media">
        <?php } ?>
        <div class="flex-btn">
            <input type="submit" value="Save Activity" name="save" class="btn">
            <a href="view_activity.php" class="option-btn">Go Back</a>
            <input type="submit" value="Delete Activity" class="delete-btn" name="delete_activity">
        </div>
    </form>
    <?php
            }
        } else {
            echo '<p class="empty">No activities found!</p>';
    ?>
    <div class="flex-btn">
        <a href="view_activities.php" class="option-btn">View Activities</a>
        <a href="add_activity.php" class="option-btn">Add Activity</a>
    </div>
    <?php
        }
        $select_activities->close();
    }
    $conn->close();
    ?>
</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
