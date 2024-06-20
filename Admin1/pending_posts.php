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
$Role = $_SESSION['Role'] ?? '';


$conn = getDBConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Approve'])) {
        $postID = $_POST['PostID'];

        // Update the post status to 'approved'
        $updateQuery = "UPDATE posts SET Status = 'Active' WHERE PostID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $postID);
        $stmt->execute();

        // Get the email of the admin who posted the announcement and the post title
        $getEmailQuery = "SELECT a.Email, p.Title FROM admins a JOIN posts p ON a.AdminID = p.AdminID WHERE p.PostID = ?";
        $stmt = $conn->prepare($getEmailQuery);
        $stmt->bind_param("i", $postID);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        // Send email notification to the admin
        $to = $admin['Email'];
        $subject = "Post Approval Notification";
        $message = "Your post titled '{$admin['Title']}' has been approved.";
        $headers = "From: no-reply@example.com";

        mail($to, $subject, $message, $headers);

        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

        // Decline post functionality
    } elseif (isset($_POST['Decline'])) {
        $PostID = (int)$_POST['PostID'];
        $DeclineReason = $_POST['DeclineReason'];

        // Fetch the email of the admin who posted and the title of the post
        $fetch_post_details_query = $conn->prepare("SELECT admins.Email, posts.Title FROM admins JOIN posts ON admins.AdminID = posts.AdminID WHERE posts.PostID = ?");
        $fetch_post_details_query->bind_param("i", $PostID);
        $fetch_post_details_query->execute();
        $post_details_result = $fetch_post_details_query->get_result();
        $post_details_row = $post_details_result->fetch_assoc();
        $admin_email = $post_details_row['Email'];
        $post_title = $post_details_row['Title'];

        // Update post status
        $update_status_query = $conn->prepare("UPDATE posts SET Status = 'Deactivated' WHERE PostID = ?");
        $update_status_query->bind_param("i", $PostID);
        if ($update_status_query->execute()) {
            // Send email notification
            $subject = "Post Declined";
            $message_body = "Your post titled '$post_title' has been declined. Reason: $DeclineReason";
            $sender = "From: bpsu.bindtogether@gmail.com";
            if (mail($admin_email, $subject, $message_body, $sender)) {
                $_SESSION['info'] = "We've sent the reason for decline to the email - $admin_email";
                $_SESSION['Email'] = $admin_email;
            } else {
                $errors['mail-error'] = "Failed while sending the decline reason. Error: " . error_get_last()['message'];
            }
        } else {
            $errors['db-error'] = "Database error: Failed to update post status.";
        }

        $message = 'Post declined!';
        $message_class = "alert-danger";

        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}



// Select all Pending posts
$Role = $_SESSION['Role'];
$AdminID = $_SESSION['AdminID'];

$query = "";
if ($Role == 'Sports Director') {
    $query = "SELECT posts.*, admins.Fname, admins.Lname 
              FROM posts 
              JOIN admins ON posts.AdminID = admins.AdminID 
              WHERE posts.Status = 'Pending' 
              AND (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
} elseif ($Role == 'Performers and Artists Director') {
    $query = "SELECT posts.*, admins.Fname, admins.Lname 
              FROM posts 
              JOIN admins ON posts.AdminID = admins.AdminID 
              WHERE posts.Status = 'Pending' 
              AND (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
}

$select_posts_query = $conn->prepare($query);
$select_posts_query->bind_param("i", $AdminID);
$select_posts_query->execute();
$result = $select_posts_query->get_result();


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
    <title>Pending Posts</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <!-- jQuery cdn link -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS for Decline Dialog -->
    <style>
        #declineDialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 50%;
        }

        #declineDialog label {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }

        #declineDialog textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #declineDialog button {
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            background-color: #f44336;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        #declineDialog button:hover {
            background-color: #d32f2f;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }

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

    <section class="show-posts">

        <h1 class="heading">Pending Posts</h1>
        <?php if (!empty($message)) : ?>
            <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="box-container">

            <?php if ($result->num_rows > 0) { ?>
                <?php while ($fetch_posts = $result->fetch_assoc()) { ?>
                    <form method="post" class="box">
                        <input type="hidden" name="PostID" value="<?php echo htmlspecialchars($fetch_posts['PostID']); ?>">
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
                        <div class="status" style="background-color:<?php echo ($fetch_posts['Status'] == 'Pending') ? 'coral' : 'limegreen'; ?>;">
                            <?php echo htmlspecialchars($fetch_posts['Status']); ?>
                        </div>
                        <div class="title"><?php echo htmlspecialchars($fetch_posts['Title']); ?></div>
                        <div class="posts-Content"><?php echo htmlspecialchars($fetch_posts['Content']); ?></div>
                        <div class="icons">
                            <div class="likes"><i class="fas fa-heart"></i><span><?php echo countPostLikes($conn, $fetch_posts['PostID']); ?></span></div>
                            <div class="comments"><i class="fas fa-comment"></i><span><?php echo countPostComments($conn, $fetch_posts['PostID']); ?></span></div>
                        </div>
                        <a href="read_post.php?PostID=<?php echo htmlspecialchars($fetch_posts['PostID']); ?>" class="btn">View Post</a>
                        <div class="flex-btn">
                            <button type="submit" name="Approve" class="option-btn">Approve</button>
                            <button type="button" class="delete-btn" onclick="showDeclineDialog('<?php echo $fetch_posts['PostID']; ?>');">Decline</button>
                        </div>
                    </form>
                <?php } ?>
            <?php } else { ?>
                <p class="empty">No Pending posts!</p>
            <?php } ?>

        </div>
    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <!-- Decline dialog -->
    <div class="overlay" id="overlay"></div>
    <div id="declineDialog">
        <form id="declineForm" method="post">
            <input type="hidden" name="PostID" id="declinePostID">
            <label for="declineReason">Reason for Decline:</label>
            <textarea name="DeclineReason" id="declineReason" required></textarea>
            <button type="submit" name="Decline" class="delete-btn">Submit</button>
        </form>
    </div>

    <script>
        function showDeclineDialog(PostID) {
            $('#declinePostID').val(PostID);
            $('#overlay').show();
            $('#declineDialog').show();
        }

        $('#overlay').click(function() {
            $('#overlay').hide();
            $('#declineDialog').hide();
        });
    </script>


    <!-- Success Dialog -->
    <div id="successDialog" class="dialog">
        <div class="dialog-content">
            <span class="close-btn" onclick="closeDialog('successDialog')">&times;</span>
            <p id="successMessage"></p>
        </div>
    </div>

    <!-- Error Dialog -->
    <div id="errorDialog" class="dialog">
        <div class="dialog-content">
            <span class="close-btn" onclick="closeDialog('errorDialog')">&times;</span>
            <p id="errorMessage"></p>
        </div>
    </div>

    <script>
        function showDialog(dialogId, message) {
            document.getElementById(dialogId).style.display = 'block';
            if (dialogId === 'successDialog') {
                document.getElementById('successMessage').innerText = message;
            } else if (dialogId === 'errorDialog') {
                document.getElementById('errorMessage').innerText = message;
            }
        }

        function closeDialog(dialogId) {
            document.getElementById(dialogId).style.display = 'none';
        }
    </script>

    <style>
        .dialog {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #888;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
            width: 300px;
            text-align: center;
        }

        .dialog-content {
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }
    </style>


</body>

</html>