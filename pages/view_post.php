<?php
require_once '../login-sec/connection.php'; // Include the database connection file
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

// Check if user is logged in
if (isset($_SESSION['UserID'])) {
    $UserID = $_SESSION['UserID'];
} else {
    $UserID = '';
}

include '../components/like_post.php'; // Include the like post functionality

// Get the post ID from the URL parameters
$get_id = isset($_GET['PostID']) ? intval($_GET['PostID']) : 0;

// Establish database connection
$conn = getDBConnection();

// Retrieve user information from session
$Classification = $_SESSION['Classification'];
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];

// Get current Date and time
$Date = date('Y-m-d');

// Check if required fields are set
if (isset($_POST['add_Comment'])) {
   $Comment = $_POST['Comment'];
   if (!empty($Comment)) {
       // Insert new comment into the database
       $insert_Comment = $conn->prepare("INSERT INTO comments (PostID, UserID, Fname, Lname, Comment, Date) VALUES (?, ?, ?, ?, ?, ?)");
       $insert_Comment->bind_param("iissss", $get_id, $UserID, $Fname_header, $Lname_header, $Comment, $Date);
       $insert_Comment->execute();
       $message[] = 'New comment added!';
   } else {
       $message[] = 'Please fill in all fields!';
   }
}

// Edit comment functionality
if (isset($_POST['edit_Comment'])) {
   $edit_CommentID = $_POST['edit_CommentID'];
   $Comment_edit_box = $_POST['Comment_edit_box'];

   // Verify if the edited comment already exists
   $verify_Comment = $conn->prepare("SELECT * FROM comments WHERE Comment = ? AND CommentID != ?");
   $verify_Comment->bind_param("si", $Comment_edit_box, $edit_CommentID);
   $verify_Comment->execute();
   $verify_Comment->store_result();

   if ($verify_Comment->num_rows > 0) {
       $message[] = 'Comment already added!';
   } else {
       // Update the comment in the database
       $update_Comment = $conn->prepare("UPDATE comments SET Comment = ?, Date = ? WHERE CommentID = ?");
       $update_Comment->bind_param("ssi", $Comment_edit_box, $Date, $edit_CommentID);
       $update_Comment->execute();
       $message[] = 'Your comment edited successfully!';
   }
   $verify_Comment->close();
   $update_Comment->close();
}

// Delete comment functionality
if (isset($_POST['delete_comment'])) {
   $delete_CommentID = $_POST['CommentID'];

   // Delete the comment from the database
   $delete_Comment = $conn->prepare("DELETE FROM comments WHERE CommentID = ?");
   $delete_Comment->bind_param("i", $delete_CommentID);
   $delete_Comment->execute();
   $message[] = 'Comment deleted successfully!';
   $delete_Comment->close();
}

if (isset($_POST['SubmitReport'])) {
    $commentID = $_POST['CommentID'];
    $ReportReason = $_POST['ReportReason'];
    $otherReason = $_POST['OtherReason'] ?? '';

    $insert_report = $conn->prepare("INSERT INTO reports (CommentID, ReportReason, OtherReason) VALUES (?, ?, ?)");
    $insert_report->bind_param("iss", $commentID, $ReportReason, $otherReason);
    $insert_report->execute();

    if ($insert_report->affected_rows > 0) {
        echo "<script>alert('Report submitted successfully.');</script>";
    } else {
        echo "<script>alert('Failed to submit the report.');</script>";
    }

    $insert_report->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
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

    /* Report dialog overlay */
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    /* Report dialog */
    #reportDialog {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        width: 25rem;
    }

    #reportForm label {
        display: block;
        margin-bottom: 5px;
    }

    #reportForm select, #reportForm textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #reportForm button {
        background-color: #c72d2d;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #reportForm button:hover {
        background-color: #c82333;
    }
    </style>
</head>
<body>
    <?php include '../components/user_header.php' ?> <!-- Include user header -->

     <!-- Back button -->
     <a href="frontpage.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <?php
    // Check if the edit comment form is opened
    if (isset($_POST['open_edit_box'])) {
        $CommentID = $_POST['CommentID'];
    ?>

    <section class="comment-edit-form">
        <p>Edit your comment</p>
        <?php
        // Retrieve the comment to be edited
        $select_edit_Comment = $conn->prepare("SELECT * FROM comments WHERE CommentID = ?");
        $select_edit_Comment->bind_param("i", $CommentID);
        $select_edit_Comment->execute();
        $result = $select_edit_Comment->get_result();
        if ($fetch_edit_comment = $result->fetch_assoc()) {
            ?>
            <form action="" method="POST">
                <input type="hidden" name="edit_CommentID" value="<?= $CommentID; ?>">
                <textarea name="Comment_edit_box" required cols="30" rows="10" placeholder="Write your comment..."><?= htmlspecialchars($fetch_edit_comment['Comment']); ?></textarea>
                <input type="submit" class="inline-btn" name="edit_Comment">
                <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?PostID=<?= $get_id; ?>';">Cancel Edit</div>
            </form>
            <?php
        }
        $select_edit_Comment->close();
        ?>
    </section>
    <?php
    }
    ?>

    <section class="posts-container" style="padding-bottom: 0;">
        <div class="box-container">
            <?php
            // Retrieve PostID from GET parameter
            $PostID = isset($_GET['PostID']) ? intval($_GET['PostID']) : null;

            if ($PostID) {
                // Prepare SQL query to select the specific post by ID
                $select_posts = $conn->prepare("SELECT posts.*, posts.Avatar AS post_Avatar, admins.Fname, admins.Lname, posts.MediaType, posts.MediaURL 
                                                FROM posts 
                                                JOIN admins ON posts.AdminID = admins.AdminID 
                                                WHERE posts.Status = ? 
                                                AND posts.PostID = ? ");
                $Status = 'Active';
                $select_posts->bind_param("si", $Status, $PostID);
                $select_posts->execute();
                $result_posts = $select_posts->get_result();

                // Check if any post is retrieved
                if ($result_posts->num_rows > 0) {
                    $fetch_posts = $result_posts->fetch_assoc();

                    // Retrieve the count of comments for the post
                    $count_post_Comments = $conn->prepare("SELECT COUNT(*) AS total_Comments FROM comments WHERE PostID = ?");
                    $count_post_Comments->bind_param("i", $PostID);
                    $count_post_Comments->execute();
                    $result_post_Comments = $count_post_Comments->get_result();
                    $total_post_Comments = $result_post_Comments->fetch_assoc()['total_Comments'];

                    // Retrieve the count of likes for the post
                    $count_post_likes = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE PostID = ?");
                    $count_post_likes->bind_param("i", $PostID);
                    $count_post_likes->execute();
                    $result_post_likes = $count_post_likes->get_result();
                    $total_post_likes = $result_post_likes->fetch_assoc()['total_likes'];

                    // Check if the current user has liked the post
                    $confirm_likes = $conn->prepare("SELECT COUNT(*) AS liked FROM likes WHERE UserID = ? AND PostID = ?");
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
                                <img src="../upload/<?= htmlspecialchars($fetch_posts['post_Avatar']); ?>" alt="Profile">
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
                        <a href="category.php?Category=<?= htmlspecialchars($fetch_posts['Category']); ?>" class="post-cat"><i class="fas fa-tag"></i> <span><?= htmlspecialchars($fetch_posts['Category']); ?></span></a>

                        <div class="icons">
                            <div><i class="fas fa-comment"></i><span>(<?= htmlspecialchars($total_post_Comments); ?>)</span></div>
                            <button id="like" name="like_post" class="like">
                                <i class="fas fa-heart" style="<?php if ($is_liked) { echo 'color: red;'; } ?>"></i><span>(<?= htmlspecialchars($total_post_likes); ?>)</span>
                            </button>
                        </div>
                    </form>
                    <?php
                } else {
                    echo '<p class="empty">Post not found!</p>';
                }

                // Close the prepared statements
                $select_posts->close();
                $count_post_Comments->close();
                $count_post_likes->close();
                $confirm_likes->close();
            } else {
                echo '<p class="empty">Invalid post ID!</p>';
            }
            ?>
        </div>
    </section>

    <section class="comments-container">
    <p class="comment-title">Add comment</p>
    <?php if ($UserID != '') {  
        $select_admin_id = $conn->prepare("SELECT posts.* FROM posts JOIN admins ON posts.AdminID = admins.AdminID 
                                    WHERE posts.PostID = ?");
        $select_admin_id->bind_param("i", $get_id);
        $select_admin_id->execute();
        $result = $select_admin_id->get_result();
        $fetch_admin_id = $result->fetch_assoc();
    ?>
    <form action="" method="post" class="add-comment">
        <input type="hidden" name="admin_id" value="<?= htmlspecialchars($fetch_admin_id['AdminID']); ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($Fname_header . ' ' . $Lname_header); ?>">
        <div class="post-admin">
            <div class="profile-img">
                <img src="../upload/<?= htmlspecialchars($Avatar_header); ?>" alt="Profile Picture">
            </div>
            <div class="user-name">
                <?= htmlspecialchars($Fname_header . ' ' . $Lname_header); ?>
            </div>
        </div>
        <textarea name="Comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="Write your comment.." required></textarea>
        <input type="submit" value="Add Comment" class="inline-btn" name="add_Comment">
    </form>
    <?php } ?>

    <p class="comment-title">Post comments</p>
    <div class="user-comments-container">
    <?php
        $select_comments = $conn->prepare("SELECT comments.*, users.Fname, users.Lname, users.Avatar FROM comments JOIN users ON comments.UserID = users.UserID WHERE PostID = ? ORDER BY CommentID DESC");
        $select_comments->bind_param("i", $get_id);
        $select_comments->execute();
        $result = $select_comments->get_result();

        if ($result->num_rows > 0) {
            while ($fetch_comments = $result->fetch_assoc()) {
                $comment_UserID = $fetch_comments['UserID'];
                $comment_content = $fetch_comments['Comment'];
                $comment_Date = $fetch_comments['Date'];
                $comment_Avatar = $fetch_comments['Avatar'];
                $name = $fetch_comments['Fname'] . ' ' . $fetch_comments['Lname'];
    ?>
        <div class="show-comments" style="<?php if($fetch_comments['UserID'] == $UserID){echo 'order:-1;'; } ?>">
            
            <div class="user-comments">
                <div class="profile-img">
                     <img src="../upload/<?= htmlspecialchars($comment_Avatar); ?>" alt="Profile Picture">
                </div>
                <div class="user-name">
                    <?= htmlspecialchars($name); ?>
                    <div class="date"><?= htmlspecialchars($comment_Date); ?></div>
                </div>
            </div>
            <div class="comment-box <?php if($fetch_comments['UserID'] == $UserID){echo 'user'; } ?>">
                <?= htmlspecialchars($comment_content); ?>
                
            </div>
            <?php if($fetch_comments['UserID'] == $UserID){ ?>
                <form action="" method="POST">
                    <input type="hidden" name="CommentID" value="<?= htmlspecialchars($fetch_comments['CommentID']); ?>">
                    <button type="submit" class="inline-option-btn" name="open_edit_box">Edit Comment</button>
                    <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('Delete this comment?');">Delete Comment</button>
                </form>
            <?php } else { ?>
                <form action="" method="POST">
                    <input type="hidden" name="CommentID" value="<?= htmlspecialchars($fetch_comments['CommentID']); ?>">
                    <button type="button" class="inline-option-btn" onclick="showReportDialog(<?= htmlspecialchars($fetch_comments['CommentID']); ?>)">Report</button>
                </form>
            <?php } ?>
        </div>
    <?php
            }
        } else {
            echo '<p class="empty">No comments yet!</p>';
        }
    ?>
    </div>
</section>

<!-- Report dialog -->
<div class="overlay" id="reportOverlay"></div>
<div id="reportDialog">
    <form id="reportForm" method="post">
        <input type="hidden" name="CommentID" id="reportCommentID">
        <label for="ReportReason">Reason for Report:</label>
        <select name="ReportReason" id="ReportReason" required>
            <option value="" selected disabled>-Select a Reason-</option>
            <option value="Spam">Spam</option>
            <option value="Harassment">Harassment</option>
            <option value="Hate Speech">Hate Speech</option>
            <option value="False Information">False Information</option>
            <option value="Other">Other</option>
        </select>
        <label for="OtherReason">Other Reason:</label>
        <textarea name="OtherReason" id="OtherReason" disabled required></textarea>
        <button type="submit" name="SubmitReport" class="delete-btn">Submit</button>
    </form>
</div>

<script>
function showReportDialog(CommentID) {
    document.getElementById('reportCommentID').value = CommentID;
    document.getElementById('reportOverlay').style.display = 'block';
    document.getElementById('reportDialog').style.display = 'block';
}

document.getElementById('reportOverlay').onclick = function() {
    document.getElementById('reportOverlay').style.display = 'none';
    document.getElementById('reportDialog').style.display = 'none';
};

document.getElementById('ReportReason').onchange = function() {
    const OtherReason = document.getElementById('OtherReason');
    if (this.value === 'Other') {
        OtherReason.disabled = false;
    } else {
        OtherReason.disabled = true;
        OtherReason.value = '';
    }
};

document.getElementById('reportForm').onsubmit = function() {
    const ReportReason = document.getElementById('ReportReason');
    if (ReportReason.value === '') {
        alert('Please select a reason for the report.');
        return false;
    }
    const OtherReason = document.getElementById('OtherReason');
    if (ReportReason.value === 'Other' && OtherReason.value.trim() === '') {
        alert('Please provide a reason in the "Other Reason" field.');
        return false;
    }
    return true;
};
</script>



    <?php $conn->close(); ?> <!-- Close the database connection -->

    <!-- custom js file link  -->
<script src="../js/script.js"></script>
</body>
</html>
