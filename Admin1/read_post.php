<?php require_once "../login-sec/connection.php"; ?>
<?php
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'] ?? '';
$Lname = $_SESSION['Lname'] ?? '';
$Avatar = $_SESSION['Avatar'] ?? '';

$Date = date('Y-m-d'); // Get current date

$get_id = $_GET['PostID'] ?? null;

if (isset($_POST['delete'])) {
    $p_id = $_POST['PostID'];
    $delete_image_query = "SELECT * FROM `posts` WHERE PostID = ?";
    $delete_image_stmt = $conn->prepare($delete_image_query);
    $delete_image_stmt->bind_param("i", $p_id);
    $delete_image_stmt->execute();
    $fetch_delete_image = $delete_image_stmt->get_result()->fetch_assoc();
    if ($fetch_delete_image['MediaURL'] != '') {
        unlink('../uploaded_media/' . $fetch_delete_image['MediaURL']);
    }
    $delete_post_query = "DELETE FROM `posts` WHERE PostID = ?";
    $delete_post_stmt = $conn->prepare($delete_post_query);
    $delete_post_stmt->bind_param("i", $p_id);
    $delete_post_stmt->execute();
    $delete_comments_query = "DELETE FROM `comments` WHERE PostID = ?";
    $delete_comments_stmt = $conn->prepare($delete_comments_query);
    $delete_comments_stmt->bind_param("i", $p_id);
    $delete_comments_stmt->execute();
    header('location:view_posts.php');
}

if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $delete_comment_query = "DELETE FROM `comments` WHERE CommentID = ?";
    $delete_comment_stmt = $conn->prepare($delete_comment_query);
    $delete_comment_stmt->bind_param("i", $comment_id);
    $delete_comment_stmt->execute();
    $message[] = 'Comment deleted!';
}

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

    <section class="read-post">

        <?php
        $conn = getDBConnection();
        $select_posts_query = "SELECT p.*, a.Avatar AS adminAvatar FROM `posts` p JOIN `admins` a ON p.AdminID = a.AdminID WHERE p.PostID = ?";
        $select_posts_stmt = $conn->prepare($select_posts_query);
        $select_posts_stmt->bind_param("i", $get_id);
        $select_posts_stmt->execute();
        $result = $select_posts_stmt->get_result();
        if ($result->num_rows > 0) {
            while ($fetch_posts = $result->fetch_assoc()) {
                $PostID = $fetch_posts['PostID'];

                $count_post_comments_query = "SELECT * FROM `comments` WHERE PostID = ?";
                $count_post_comments_stmt = $conn->prepare($count_post_comments_query);
                $count_post_comments_stmt->bind_param("i", $PostID);
                $count_post_comments_stmt->execute();
                $total_post_comments = $count_post_comments_stmt->get_result()->num_rows;

                $count_post_likes_query = "SELECT * FROM `likes` WHERE PostID = ?";
                $count_post_likes_stmt = $conn->prepare($count_post_likes_query);
                $count_post_likes_stmt->bind_param("i", $PostID);
                $count_post_likes_stmt->execute();
                $total_post_likes = $count_post_likes_stmt->get_result()->num_rows;

        ?>
                <form method="post">
                    <input type="hidden" name="PostID" value="<?= $PostID; ?>">
                    <div class="status" style="background-color:<?php if ($fetch_posts['Status'] == 'Active') {
                                                                    echo 'limegreen';
                                                                } else {
                                                                    echo 'coral';
                                                                }; ?>;"><?= htmlspecialchars($fetch_posts['Status']); ?></div>
                    <div class="post-admin">
                        <div class="avatar-img">
                            <img src="../upload/<?= htmlspecialchars($fetch_posts['adminAvatar']); ?>" alt="Profile">
                        </div>
                        <div>
                            <p class="admin-name"> <?= htmlspecialchars($fetch_posts['Fname'] . ' ' . $fetch_posts['Lname']); ?></p>
                            <div><?= htmlspecialchars($fetch_posts['Date']); ?></div>
                        </div>
                    </div>

                    <?php
                    if (!empty($fetch_posts['MediaURL'])) {
                        if ($fetch_posts['MediaType'] == 'image') {
                    ?>
                            <img src="../uploaded_media/<?= htmlspecialchars($fetch_posts['MediaURL']); ?>" class="image" alt="">
                        <?php
                        } elseif ($fetch_posts['MediaType'] == 'video') {
                        ?>
                            <video controls class="image">
                                <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mp4">
                                <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/webm">
                                <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/ogg">
                                <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/quicktime">
                                <source src="../uploaded_media/<?php echo htmlspecialchars($fetch_posts['MediaURL']); ?>" type="video/mov">
                                Your browser does not support the video tag.
                            </video>
                    <?php
                        }
                    }
                    ?>
                    <div class="title"><?= htmlspecialchars($fetch_posts['Title']); ?></div>
                    <div class="content"><?= htmlspecialchars($fetch_posts['Content']); ?></div>
                    <div class="icons">
                        <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                        <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                    </div>
                    <div class="flex-btn">
                        <a href="edit_post.php?PostID=<?= $PostID; ?>" class="inline-option-btn">Edit</a>
                        <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('Delete this post?');">Delete</button>
                        <a href="view_posts.php" class="inline-option-btn">Go Back</a>
                    </div>
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">No posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Add Post</a></p>';
        }
        ?>

    </section>

    <section class="comments" style="padding-top: 0;">
        <p class="comment-title">Comments</p>
        <div class="box-container">
            <?php
            $select_comments_query = "SELECT * FROM `comments` WHERE PostID = ?";
            $select_comments_stmt = $conn->prepare($select_comments_query);
            $select_comments_stmt->bind_param("i", $get_id);
            $select_comments_stmt->execute();
            $result = $select_comments_stmt->get_result();
            if ($result->num_rows > 0) {
                while ($fetch_comments = $result->fetch_assoc()) {
            ?>
                    <div class="box">
                        <div class="user">
                            <i class="fas fa-user"></i>
                            <div class="user-info">
                                <span><?= htmlspecialchars($fetch_comments['Fname']); ?></span>
                                <div><?= htmlspecialchars($fetch_comments['Date']); ?></div>
                            </div>
                        </div>
                        <div class="text"><?= htmlspecialchars($fetch_comments['Comment']); ?></div>
                        <form action="" method="POST">
                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comments['CommentID']); ?>">
                            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('Delete this comment?');">Delete Comment</button>
                        </form>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No comments added yet!</p>';
            }
            ?>
        </div>
    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>