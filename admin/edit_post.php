<?php
require_once "../login-sec/connection.php";
session_start();

// Get session variables
$Fname = $_SESSION['Fname'] ?? '';
$Lname = $_SESSION['Lname'] ?? '';
$Avatar = $_SESSION['Avatar'] ?? '';
$UserID = $_SESSION['UserID'] ?? '';
$Status = $_SESSION['Status'] ?? '';

$message = '';
$message_class = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getDBConnection();

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the post ID is set in the URL
    if (!isset($_GET['PostID'])) {
        die("Post ID is not set.");
    }

    $PostID = $_GET['PostID'];

    if (isset($_POST['save'])) {
        $Title = $_POST['Title'];
        $Content = $_POST['Content'];
        $Category = $_POST['Category'];
        $Status = $_POST['Status'];
        $MediaType = $_POST['MediaType'];
        $old_MediaURL = $_POST['old_MediaURL'];

        // Check if the form data is valid
        if (empty($Title) || empty($Content) || empty($Category) || empty($Status) || empty($MediaType)) {
            $message = 'All fields are required.';
            $message_class = "alert-danger";
        } else {
            // Prepare the media update if a new media file is uploaded
            $MediaURL = $old_MediaURL; // Default to old MediaURL
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $MediaURL = $_FILES['media']['name'];
                $media_size = $_FILES['media']['size'];
                $media_tmp_name = $_FILES['media']['tmp_name'];
                $media_folder = '../uploaded_media/' . $MediaURL;

                if ($media_size > 5000000) { // 5MB max file size
                    $message = 'Media size is too large.';
                    $message_class = "alert-danger";
                } else {
                    if (move_uploaded_file($media_tmp_name, $media_folder)) {
                        // Delete old media from server if new media is uploaded successfully
                        if ($old_MediaURL != '' && file_exists('../uploaded_media/' . $old_MediaURL)) {
                            unlink('../uploaded_media/' . $old_MediaURL);
                        }
                    } else {
                        $message = 'Error uploading media.';
                        $message_class = "alert-danger";
                    }
                }
            }

            // Prepare the update query
            $stmt = $conn->prepare("UPDATE posts SET Title = ?, Content = ?, Category = ?, MediaType = ?, MediaURL = ?, Status = ? WHERE PostID = ?");
            $stmt->bind_param("ssssssi", $Title, $Content, $Category, $MediaType, $MediaURL, $Status, $PostID);

            if ($stmt->execute()) {
                $message = 'Post updated successfully.';
                $message_class = "alert-success";
            } else {
                $message = 'Error updating post: ' . $stmt->error;
                $message_class = "alert-danger";
            }

            $stmt->close();
        }
    }

    // Delete post handling
    if (isset($_POST['delete_post'])) {
        $stmt = $conn->prepare("DELETE FROM posts WHERE PostID = ?");
        $stmt->bind_param("i", $PostID);

        if ($stmt->execute()) {
            $message = 'Post deleted successfully.';
            $message_class = "alert-success";
        } else {
            $message = 'Error deleting post: ' . $stmt->error;
            $message_class = "alert-danger";
        }

        $stmt->close();
    }

    // Delete media handling
    if (isset($_POST['delete_media'])) {
        $MediaURL = $_POST['old_MediaURL'];

        $stmt = $conn->prepare("UPDATE posts SET MediaURL = '', MediaType = '' WHERE PostID = ?");
        $stmt->bind_param("i", $PostID);

        if ($stmt->execute()) {
            if ($MediaURL != '' && unlink('../uploaded_media/' . $MediaURL)) {
                $message = 'Media deleted successfully.';
                $message_class = "alert-success";
            } else {
                $message = 'Error deleting media from server.';
                $message_class = "alert-danger";
            }
        } else {
            $message = 'Error removing media from post: ' . $stmt->error;
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
    <meta http-equiv="X-UA-Compatible" Content="IE=edge">
    <meta name="viewport" Content="width=device-width, initial-scale=1.0">
    <Title>Edit Post</Title>
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

<?php require_once "../components/header_coach.php"; ?>

<a href="view_posts.php" class="btn btn-secondary back-button">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>

<section class="post-editor">
    <h1 class="heading">Edit Post</h1>

    <?php
    $conn = getDBConnection();

    if (isset($_GET['PostID'])) {
        $PostID = $_GET['PostID'];
        $select_posts = $conn->prepare("SELECT * FROM posts WHERE PostID = ?");
        $select_posts->bind_param("i", $PostID);
        $select_posts->execute();
        $result = $select_posts->get_result();

        if ($result->num_rows > 0) {
            while ($fetch_posts = $result->fetch_assoc()) {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="old_MediaURL" value="<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>">
        <input type="hidden" name="PostID" value="<?php echo htmlspecialchars($fetch_posts['PostID']); ?>">
        <?php if (!empty($message)): ?>
      <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
   <?php endif; ?>
        <p>Post Status <span>*</span></p>
        <select name="Status" class="box" required>
            <option value="<?php echo htmlspecialchars($fetch_posts['Status']); ?>" selected><?php echo htmlspecialchars($fetch_posts['Status']); ?></option>
            <option value="active">Active</option>
            <option value="deactive">Deactive</option>
        </select>
        <p>Post Title <span>*</span></p>
        <input type="text" name="Title" maxlength="100" required placeholder="Add post Title" class="box" value="<?php echo htmlspecialchars($fetch_posts['Title']); ?>">
        <p>Post Content <span>*</span></p>
        <textarea name="Content" class="box" required maxlength="10000" placeholder="Write your Content..." cols="30" rows="10"><?php echo htmlspecialchars($fetch_posts['Content']); ?></textarea>
        <p>Post Category <span>*</span></p>
          <?php  if ($result->num_rows > 0) {
        // Fetch current post category
        $current_category = htmlspecialchars($fetch_posts['Category']);
        ?>
        <select name="Category" class="box" required>
            <option value="<?php echo $current_category; ?>" selected><?php echo $current_category; ?></option>
            <?php
            // Generate options from fetched categories
            while($row = $result->fetch_assoc()) {
                $category_id = htmlspecialchars($row['CategoryID']);
                $category_name = htmlspecialchars($row['Category']);
                echo "<option value='$category_id'>$category_name</option>";
            }
            ?>
        </select>
        <?php
    } else {
        echo "No categories available.";
    }   ?>  
        <p>Media Type <span>*</span></p>
        <select name="MediaType" class="box" required>
            <option value="<?php echo htmlspecialchars($fetch_posts['MediaType']); ?>" selected><?php echo htmlspecialchars($fetch_posts['MediaType']); ?></option>
            <option value="image">Image</option>
            <option value="video">Video</option>
            <option value="text">Text</option>
        </select>
        <p>Media</p>
        <input type="file" name="media" class="box" accept="image/jpg, image/jpeg, image/png, image/webp, video/mp4, video/webm, video/ogg, video/MOV">
        <?php if ($fetch_posts['MediaURL'] != '') { ?>
            <p>Current Media:</p>
            <?php if ($fetch_posts['MediaType'] == 'image') { ?>
                <img src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" class="image" alt="">
            <?php } elseif ($fetch_posts['MediaType'] == 'video') { ?>
                <video controls class="image">
                            <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mp4">
                            <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/webm">
                            <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/ogg">
                            <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/quicktime">
                            <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mov">
                            Your browser does not support the video tag.
                        </video>
            <?php } ?>
            <input type="submit" value="Delete Media" class="inline-delete-btn" name="delete_media">
        <?php } ?>
        <div class="flex-btn">
            <input type="submit" value="Save Post" name="save" class="btn">
            <a href="view_posts.php" class="option-btn">Go Back</a>
            <input type="submit" value="Delete Post" class="delete-btn" name="delete_post">
        </div>
    </form>
    <?php
            }
        } else {
            echo '<p class="empty">No posts found!</p>';
    ?>
    <div class="flex-btn">
        <a href="view_posts.php" class="option-btn">View Posts</a>
        <a href="add_posts.php" class="option-btn">Add Posts</a>
    </div>
    <?php
        }
        $select_posts->close();
    }
    $conn->close();
    ?>
</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
