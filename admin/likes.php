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
$AdminID = $_SESSION['AdminID'] ?? '';
$Avatar = $_SESSION['Avatar'] ?? '';

// Get the admin ID
$AdminID = $_SESSION['AdminID'] ?? '';

// Get the database connection
$conn = getDBConnection();

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update post status functionality
if (isset($_POST['update_status'])) {
    $PostID = (int)$_POST['PostID']; // Directly casting to integer
    $new_status = $_POST['current_status'] === 'Active' ? 'Inactive' : 'Active';

    // Update the post status
    $update_status_query = $conn->prepare("UPDATE posts SET Status = ? WHERE PostID = ?");
    $update_status_query->bind_param("si", $new_status, $PostID);
    $update_status_query->execute();

    $message = 'Post status updated successfully!';
    $message_class = "alert-success";
}

// Select all posts created by the admin and liked by the admins
$select_posts_query = $conn->prepare("SELECT posts.*, posts.Avatar AS postAvatar, admins.Fname, admins.Lname 
                                      FROM posts 
                                      JOIN admins ON posts.AdminID = admins.AdminID 
                                      JOIN likes ON posts.PostID = likes.PostID
                                      WHERE posts.Status = 'Active' AND posts.AdminID = ?");
$select_posts_query->bind_param("i", $AdminID);
$select_posts_query->execute();
$result = $select_posts_query->get_result();

// Count post comments and likes
function countPostComments($conn, $PostID)
{
    $count_post_comments_query = $conn->prepare("SELECT * FROM `comments` WHERE PostID = ?");
    $count_post_comments_query->bind_param("i", $PostID);
    $count_post_comments_query->execute();
    return $count_post_comments_query->get_result()->num_rows;
}

function countPostLikes($conn, $PostID)
{
    $count_post_likes_query = $conn->prepare("SELECT * FROM `likes` WHERE PostID = ?");
    $count_post_likes_query->bind_param("i", $PostID);
    $count_post_likes_query->execute();
    return $count_post_likes_query->get_result()->num_rows;
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

    <?php require_once "../components/header_coach.php"; ?>
    <a href="posts.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <section class="show-posts">

        <h1 class="heading">Your Posts</h1>
        <?php if (!empty($message)) : ?>
            <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="box-container">

            <?php if ($result->num_rows > 0) { ?>
                <?php while ($fetch_posts = $result->fetch_assoc()) { ?>
                    <form method="post" class="box">
                        <input type="hidden" name="PostID" value="<?php echo htmlspecialchars($fetch_posts['PostID']); ?>">
                        <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($fetch_posts['Status']); ?>">
                        <div class="user">
                            <div class="user-info">
                                <span><?php echo htmlspecialchars($fetch_posts['Fname'] . ' ' . $fetch_posts['Lname']); ?></span>
                                <div><?php echo htmlspecialchars($fetch_posts['Date'] ?? ''); ?></div>
                            </div>
                        </div>
                        <?php if (!empty($fetch_posts['MediaURL'])) { ?>
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
                        <?php } ?>
                        <div class="status" style="background-color:<?php echo ($fetch_posts['Status'] == 'Active') ? 'limegreen' : 'coral'; ?>;">
                            <?php echo htmlspecialchars($fetch_posts['Status']); ?>
                        </div>
                        <div class="title"><?php echo htmlspecialchars($fetch_posts['Title']); ?></div>
                        <div class="posts-Content"><?php echo htmlspecialchars($fetch_posts['Content']); ?></div>
                        <div class="icons">
                            <div class="likes"><i class="fas fa-heart"></i><span><?php echo countPostLikes($conn, $fetch_posts['PostID']); ?></span></div>
                            <div class="comments"><i class="fas fa-comment"></i><span><?php echo countPostComments($conn, $fetch_posts['PostID']); ?></span></div>
                        </div>
                        <div class="flex-btn">
                            <a href="edit_post.php?PostID=<?php echo htmlspecialchars($fetch_posts['PostID']); ?>" class="option-btn">Edit</a>
                            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Delete this post?');">Delete</button>
                        </div>
                        <a href="read_post.php?PostID=<?php echo htmlspecialchars($fetch_posts['PostID']); ?>" class="btn">View Post</a>
                        <button type="submit" name="update_status" class="option-btn"><?php echo ($fetch_posts['Status'] == 'Active') ? 'Deactivate' : 'Activate'; ?></button>
                    </form>
                <?php } ?>
            <?php } else { ?>
                <p class="empty">No posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Add Post</a></p>
            <?php } ?>

        </div>
    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>