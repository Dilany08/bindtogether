<?php
require_once '../login-sec/connection.php'; // Include the database connection file
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar']) || !isset($_SESSION['AdminID'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Avatar = $_SESSION['Avatar'];
$AdminID = $_SESSION['AdminID']; // Ensure AdminID is stored in the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reported Comments</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .comments .text {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
}

.comments .report-reason {
    font-size: 14px;
    color: #888;
    margin-bottom: 10px;
    background: #ffe9e9;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #e74c3c;
}

.comments .report-reason strong {
    color: #333;
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

<?php
function fetchReportedComments($AdminID) {
   $conn = getDBConnection();
   
   // Fetch admin role
   $adminRoleQuery = "SELECT Role FROM admins WHERE AdminID = ?";
   $stmt = $conn->prepare($adminRoleQuery);
   $stmt->bind_param("i", $AdminID);
   $stmt->execute();
   $result = $stmt->get_result();
   $adminRole = $result->fetch_assoc()['Role'];
   $stmt->close();
   
   // Set the appropriate user role based on the admin role
   $userRole = '';
   if ($adminRole == 'Sports Director') {
       $userRole = 'Athletes';
   } elseif ($adminRole == 'Performers and Artists Director') {
       $userRole = 'Performers and Artists';
   }
   
   // Fetch reported comments along with user, post, and report details
   $query = "SELECT comments.CommentID, comments.Comment, comments.Date AS CommentDate, users.Fname, users.Lname, users.Avatar, posts.Title, posts.PostID, reports.ReportReason, reports.OtherReason, reports.ReportDate
       FROM comments
       JOIN users ON comments.UserID = users.UserID
       JOIN posts ON comments.PostID = posts.PostID
       JOIN reports ON comments.CommentID = reports.CommentID
       WHERE users.Role = ?
       ORDER BY reports.ReportDate DESC";
   
   $stmt = $conn->prepare($query);
   $stmt->bind_param("s", $userRole);
   $stmt->execute();
   $result = $stmt->get_result();
   
   $comments = [];
   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $comments[] = $row;
       }
   }
   
   $stmt->close();
   $conn->close();
   
   return $comments;
}

$comments = fetchReportedComments($AdminID); // Pass the AdminID parameter

$conn = getDBConnection();
if (isset($_POST['delete_comment'])) {
    $commentID = $_POST['CommentID'];

    // Fetch the user details for the comment to send an email notification
    $userQuery = "SELECT users.Email, users.Fname, users.Lname
                  FROM users
                  JOIN comments ON users.UserID = comments.UserID
                  WHERE comments.CommentID = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $commentID);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userDetails = $userResult->fetch_assoc();

    // Delete the comment
    $deleteQuery = "DELETE FROM comments WHERE CommentID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $commentID);
    $stmt->execute();

    // Send email notification
    $to = $userDetails['Email'];
    $subject = "Comment Deleted Notification";
    $message = "Dear " . $userDetails['Fname'] . " " . $userDetails['Lname'] . ",\n\nYour comment has been deleted because it was reported for containing malicious or inappropriate content.\n\nRegards,\nAdmin Team";
    $headers = "From: bpsu.bindtogether@gmail.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "<script>alert('Comment deleted and user notified.');</script>";
    } else {
        echo "<script>alert('Comment deleted but failed to send email.');</script>";
    }

    $stmt->close();
    // Refresh the page to reflect changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<section class="comments">

   <h1 class="heading">Reported Comments</h1>
   
   <p class="comment-title">Reported Comments</p>
   <div class="box-container">
  
   <?php if (empty($comments)): ?>
      <p class="empty">No Reported Comments</p>
   <?php else: ?>
      <?php foreach ($comments as $comment): ?>
         <div class="post-title"> from : <span><?= htmlspecialchars($comment['Title']); ?></span> 
         <a href="read_post.php?PostID=<?= htmlspecialchars($comment['PostID']); ?>">view post</a></div>
         
         <div class="box">
            <div class="user">
               <div class="comment-img">
                  <img src="../upload/<?= htmlspecialchars($comment['Avatar']); ?>" alt="Profile Picture">
               </div>
               <div class="user-info">
                  <span><?= htmlspecialchars($comment['Fname'] . ' ' . $comment['Lname']); ?></span>
                  <div><?= htmlspecialchars($comment['CommentDate']); ?></div>
               </div>
            </div>
            <div class="text"><?= htmlspecialchars($comment['Comment']); ?></div>
            <div class="report-reason">
               <strong>Reason:</strong> <?= htmlspecialchars($comment['ReportReason']); ?><br>
               <?php if ($comment['ReportReason'] == 'Other'): ?>
                  <strong>Other Reason:</strong> <?= htmlspecialchars($comment['OtherReason']); ?><br>
               <?php endif; ?>
               <strong>Reported on:</strong> <?= htmlspecialchars($comment['ReportDate']); ?>
            </div>
            <form action="" method="POST">
               <input type="hidden" name="CommentID" value="<?= htmlspecialchars($comment['CommentID']); ?>">
               <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
            </form>
         </div>
      <?php endforeach; ?>
   <?php endif; ?>

   </div>

</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
