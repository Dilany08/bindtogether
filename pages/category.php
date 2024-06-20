<?php
require_once '../login-sec/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$UserID = $_SESSION['UserID'] ?? '';
$AdminID = $_SESSION['AdminID'] ?? '';
$Category = $_GET['Category'] ?? ''; // Changed from 'category' to 'Category'

// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];

$Date = date('Y-m-d'); // Get current Date

// Handle like/unlike form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['PostID'])) {
    $PostID = $_POST['PostID'];

    // Check if user already liked the post
    $conn = getDBConnection();
    $check_like = $conn->prepare("SELECT * FROM `likes` WHERE UserID = ? AND PostID = ?");
    $check_like->bind_param("ii", $UserID, $PostID);
    $check_like->execute();
    $result_check_like = $check_like->get_result();

    if ($result_check_like->num_rows > 0) {
        // User already liked the post, remove the like (unlike)
        $delete_like = $conn->prepare("DELETE FROM `likes` WHERE UserID = ? AND PostID = ?");
        $delete_like->bind_param("ii", $UserID, $PostID);
        $delete_like->execute();
        $delete_like->close();
    } else {
        // User has not liked the post, add a like
        $insert_like = $conn->prepare("INSERT INTO `likes` (UserID, PostID) VALUES (?, ?)");
        $insert_like->bind_param("ii", $UserID, $PostID);
        $insert_like->execute();
        $insert_like->close();
    }

    $check_like->close();
    $conn->close();

    // Redirect to the same page to avoid form resubmission
    header("Location: #like");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
    <?php require_once "../components/user_header.php" ?>
    <!-- Back button -->
    <a href="frontpage.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>


    <section class="posts-container">

        <h1 class="heading">post categories</h1>

        <div class="box-container">

            <?php
            $conn = getDBConnection();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $Status = 'Active';
            $select_posts = $conn->prepare("SELECT posts.*, admins.Fname, admins.Lname, admins.Avatar AS admin_Avatar, posts.MediaType, posts.MediaURL 
                                        FROM posts 
                                        JOIN admins ON posts.AdminID = admins.AdminID 
                                        WHERE posts.Status = ? AND posts.Category = ?");
            $select_posts->bind_param("ss", $Status, $Category);
            $select_posts->execute();
            $result_posts = $select_posts->get_result();

            if ($result_posts->num_rows > 0) {
                while ($fetch_posts = $result_posts->fetch_assoc()) {

                    $PostID = $fetch_posts['PostID'];

                    $count_post_comments = $conn->prepare("SELECT COUNT(*) AS total_comments FROM `comments` WHERE PostID = ?");
                    $count_post_comments->bind_param("i", $PostID);
                    $count_post_comments->execute();
                    $result_post_comments = $count_post_comments->get_result();
                    $total_post_comments = $result_post_comments->fetch_assoc()['total_comments'];

                    $count_post_likes = $conn->prepare("SELECT COUNT(*) AS total_likes FROM `likes` WHERE PostID = ?");
                    $count_post_likes->bind_param("i", $PostID);
                    $count_post_likes->execute();
                    $result_post_likes = $count_post_likes->get_result();
                    $total_post_likes = $result_post_likes->fetch_assoc()['total_likes'];

                    $confirm_likes = $conn->prepare("SELECT COUNT(*) AS liked FROM `likes` WHERE UserID = ? AND PostID = ?");
                    $confirm_likes->bind_param("ii", $UserID, $PostID);
                    $confirm_likes->execute();
                    $result_confirm_likes = $confirm_likes->get_result();
                    $is_liked = $result_confirm_likes->fetch_assoc()['liked'] > 0;
            ?>

                    <form class="box" method="post">
                        <input type="hidden" name="PostID" value="<?= htmlspecialchars($PostID); ?>">
                        <input type="hidden" name="AdminID" value="<?= htmlspecialchars($fetch_posts['AdminID']); ?>">
                        <div class="post-admin">
                            <div class="profile-img">
                                <img src="../upload/<?= htmlspecialchars($fetch_posts['admin_Avatar']); ?>" alt="Profile">
                            </div>
                            <div>
                                <a href="admin_posts.php?AdminID=<?= urlencode($fetch_posts['AdminID']); ?>"><?= htmlspecialchars($fetch_posts['Fname'] . ' ' . $fetch_posts['Lname']); ?></a>
                                <div><?= htmlspecialchars($fetch_posts['Date']); ?></div>
                            </div>
                        </div>
                        <?php
                        if (!empty($fetch_posts['MediaURL'])) {
                            if ($fetch_posts['MediaType'] == 'image') {
                        ?>
                                <img src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" class="post-image" alt="">
                            <?php
                            } elseif ($fetch_posts['MediaType'] == 'video') {
                            ?>
                                <video controls class="post-image">
                                    <source src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mp4">
                                    <source src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/webm">
                                    <source src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/ogg">
                                    <source src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/quicktime">
                                    <source src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mov">
                                    Your browser does not support the video tag.
                                </video>
                        <?php
                            }
                        }
                        ?>
                        <div class="post-title"><?= htmlspecialchars($fetch_posts['Title']); ?></div>
                        <div class="post-content content-150"><?= htmlspecialchars($fetch_posts['Content']); ?></div>
                        <a href="view_post.php?PostID=<?= htmlspecialchars($PostID); ?>" class="inline-btn">read more</a>
                        <a href="category.php?Category=<?= htmlspecialchars($fetch_posts['Category']); ?>" class="post-cat"><i class="fas fa-tag"></i> <span><?= htmlspecialchars($fetch_posts['Category']); ?></span></a>
                        <div class="icons">
                            <a href="view_post.php?PostID=<?= htmlspecialchars($PostID); ?>"><i class="fas fa-comment"></i><span>(<?= htmlspecialchars($total_post_comments); ?>)</span></a>
                            <button id="like" name="like_post" class="like" style="background: none; border: none;"><i class="fas fa-heart" style="<?= $is_liked ? 'color:red;' : ''; ?>"></i><span>(<?= htmlspecialchars($total_post_likes); ?>)</span></button>
                        </div>
                    </form>
            <?php
                    $confirm_likes->close();
                    $count_post_comments->close();
                    $count_post_likes->close();
                }
            } else {
                echo '<p class="empty">no posts added yet!</p>';
            }

            // Close the prepared statements
            $select_posts->close();

            // Close the database connection
            $conn->close();
            ?>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="../js/script.js"></script>

</body>

</html>