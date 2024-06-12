<?php
require_once "../login-sec/connection.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$AdminID = $_SESSION['AdminID'] ?? '';
$Fname = $_SESSION['Fname'];
$Lname = $_SESSION['Lname'];
$Avatar = $_SESSION['Avatar'] ?? '';
$Role = $_SESSION['Role'] ?? ''; // Assuming the role is stored in the session
$Date = date('Y-m-d'); // Get current Date

if (isset($_POST['publish'])) {
    $conn = getDBConnection();

    $post_Title = $_POST['Title'];
    $post_Content = $_POST['Content'];
    $post_Category = $_POST['Category'];
    $post_Venue = $_POST['Venue'];
    $post_Address = $_POST['Address'];
    $post_Date = $_POST['Date'];
    $post_Time = $_POST['Time'];
    $post_status = 'Active';
    $Media_Type = 'image';
    $Media_url = '';

    $message = '';

    // Handle media file upload
    $media_file = $_FILES['media']['name'];
    $media_size = $_FILES['media']['size'];
    $media_tmp_name = $_FILES['media']['tmp_name'];
    $media_folder = "../activityPictures/" . $media_file;

    if (!empty($media_file)) {
        $select_media_query = "SELECT * FROM activities WHERE Image = ? AND AdminID = ?";
        $stmt = $conn->prepare($select_media_query);
        $stmt->bind_param('si', $media_file, $AdminID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = 'Media name repeated!';
            $message_class = "alert-danger";
        } elseif ($media_size > 99990000) {
            $message = 'Media size is too large!';
            $message_class = "alert-danger";
        } else {
            $allowed_image_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/webp');
            $file_mime_type = mime_content_type($media_tmp_name);

            if (in_array($file_mime_type, $allowed_image_types)) {
                move_uploaded_file($media_tmp_name, $media_folder);
                $Media_url = $media_folder;
            } else {
                $message = 'Invalid file type!';
                $message_class = "alert-danger";
            }
        }

        $stmt->close();
    }

    $insert_post_query = "INSERT INTO activities (AdminID, Title, Content, Image, Category, Venue, Address, Date, Time, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_post_query);
    $stmt->bind_param('isssssssss', $AdminID, $post_Title, $post_Content, $Media_url, $post_Category, $post_Venue, $post_Address, $post_Date, $post_Time, $post_status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = 'Event added successfully!';
        $message_class = "alert-success";
    } else {
        $message = 'Event could not be added!';
        $message_class = "alert-danger";
    }

    $stmt->close();
    $conn->close();
}

// Fetch categories based on role
$categories = [];
if ($Role === 'Sports Director') {
    $categories = [
        'Sports Competitions',
        'Sports Tryouts',
        'Sports Practices',
        'Upcoming Events'
    ];
} elseif ($Role === 'Performers and Artists Director') {
    $categories = [
        'Performers Competitions',
        'Performers Practices',
        'Performers Auditions',
        'Upcoming Events'
    ];
}

$conn = getDBConnection();
$categories_result = $conn->query("SELECT * FROM categories");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>

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
<?php require_once "../components/header.php"; ?>

<a href="super_admin.php" class="btn btn-secondary back-button">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>

<section class="post-editor">
    <h1 class="heading">Add New Event</h1>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <p><input type="hidden" name="name" value="<?= htmlspecialchars($Fname . ' ' . $Lname); ?>"></p>
        <p>Event Title <span>*</span></p>
        <input type="text" name="Title" maxlength="100" required placeholder="Add event title" class="box">
        <p>Event Content <span>*</span></p>
        <textarea name="Content" class="box" required maxlength="10000" placeholder="Write your content..." cols="30" rows="10"></textarea>
        <p>Event Category <span>*</span></p>
        <select name="Category" class="box" required>
            <option value="" selected disabled>--Select Category*--</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
            <?php endforeach; ?>
        </select>
        <p>Venue <span>*</span></p>
        <input type="text" name="Venue" maxlength="255" required placeholder="Add event venue" class="box">
        <p>Address <span>*</span></p>
        <input type="text" name="Address" maxlength="255" required placeholder="Add event address" class="box">
        <p>Event Date <span>*</span></p>
        <input type="date" name="Date" required class="box">
        <p>Event Time <span>*</span></p>
        <input type="time" name="Time" required class="box">
        <p>Media <span>*</span></p>
        <input type="file" name="media" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required multiple>
        <div class="flex-btn">
            <input type="submit" value="Publish Event" name="publish" class="btn">
        </div>
    </form>
    <?php
    if (isset($messages)) {
        foreach ($messages as $msg) {
            echo '<div class="message">' . htmlspecialchars($msg) . '</div>';
        }
    }
    ?>
</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

