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
$Title = $_SESSION['Title'] ?? '';
$Content = $_SESSION['Content'] ?? '';
$Category = $_SESSION['Category'] ?? '';
$post_status = 'Deactivated';
$Date = date('Y-m-d'); // Get current Date

if (isset($_POST['publish'])) {
    $post_status = 'Active';
}

if (isset($_POST['publish']) || isset($_POST['draft'])) {
    $conn = getDBConnection();

    $post_Title = $_POST['Title'];
    $post_Content = $_POST['Content'];
    $post_Category = $_POST['Category'];
    $Media_Type = $_POST['MediaType'];
    $Media_url = '';

    $message = '';

    if ($Media_Type == 'image' || $Media_Type == 'video') {
        $media_file = $_FILES['media']['name'];
        $media_size = $_FILES['media']['size'];
        $media_tmp_name = $_FILES['media']['tmp_name'];
        $media_folder = "../uploaded_media/" . $media_file;

        if (!empty($media_file)) {
            $select_media_query = "SELECT * FROM posts WHERE MediaURL = ? AND AdminID = ?";
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
                // Check if the uploaded file is a video or an image
                $allowed_image_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/webp');
                $allowed_video_types = array('video/mp4', 'video/webm', 'video/quicktime', 'video/ogg', 'video/mov');
                $file_mime_type = mime_content_type($media_tmp_name);

                if (in_array($file_mime_type, $allowed_image_types) || in_array($file_mime_type, $allowed_video_types)) {
                    move_uploaded_file($media_tmp_name, $media_folder);
                    $Media_url = $media_folder;
                } else {
                    $message = 'Invalid file type!';
                    $message_class = "alert-danger";
                }
            }

            $stmt->close();
        }
    } elseif ($Media_Type == 'text') {
        $Media_url = $_POST['mediaText'];
    }

    $insert_post_query = "INSERT INTO posts (AdminID, Fname, Lname, Title, Content, Category, MediaType, MediaURL, Date, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_post_query);
    $stmt->bind_param('isssssssss', $AdminID, $Fname, $Lname, $post_Title, $post_Content, $post_Category, $Media_Type, $Media_url, $Date, $post_status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = isset($_POST['publish']) ? 'Post published!' : 'Draft saved!';
        $message_class = "alert-success";
    } else {
        $message = 'Post could not be added!';
        $message_class = "alert-danger";
    }

    $stmt->close();
    $conn->close();
}

// Fetch categories from the database
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
    <title>Posts</title>

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

<!-- add post -->

<section class="post-editor">
    <h1 class="heading">Add New Post</h1>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <p><input type="hidden" name="name" value="<?= htmlspecialchars($Fname . ' ' . $Lname); ?>"></p>
        <p><input type="hidden" name="Date" value="<?= htmlspecialchars($Date); ?>"></p>
        <p>Post Title <span>*</span></p>
        <input type="text" name="Title" maxlength="100" required placeholder="Add post Title" class="box">
        <p>Post Content <span>*</span></p>
        <textarea name="Content" class="box" required maxlength="10000" placeholder="Write your Content..." cols="30" rows="10"></textarea>
        <p>Post Category <span>*</span></p>
        <select name="Category" class="box" required>
            <option value="" selected disabled>--Select Category*--</option>
            <?php while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($category['Category']); ?>"><?php echo htmlspecialchars($category['Category']); ?></option>
            <?php endwhile; ?>
        </select>
        <p>Media Type <span>*</span></p>
        <select name="MediaType" class="box" required>
            <option value="" selected disabled>--Select Media Type*--</option>
            <option value="text">Text</option>
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>
        <div id="mediaField">
            <p>Media Content</p>
            <input type="file" name="media" class="box" accept="image/jpg, image/jpeg, image/png, image/webp, video/mp4, video/webm, video/ogg, video/quicktime, video/mov">
        </div>
        <div class="flex-btn">
            <input type="submit" value="Publish Post" name="publish" class="btn">
            <input type="submit" value="Save Draft" name="draft" class="option-btn">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const MediaTypeSelect = document.querySelector('select[name="MediaType"]');
    const mediaField = document.getElementById('mediaField');

    MediaTypeSelect.addEventListener('change', function () {
        if (this.value === 'text') {
            mediaField.innerHTML = '<textarea name="mediaText" class="box" required placeholder="Enter text Content..." cols="30" rows="10"></textarea>';
        } else {
            mediaField.innerHTML = '<input type="file" name="media" class="box" accept="image/jpg, image/jpeg, image/png, image/webp, video/mp4, video/webm">';
        }
    });
});
</script>

</body>
</html>
