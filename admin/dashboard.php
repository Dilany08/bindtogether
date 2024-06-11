<?php 
require '../login-sec/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
   header("Location: ../login-sec/login.php");
   exit();
}
$AdminID = $_SESSION['AdminID'] ?? '';
$UserID = $_SESSION['UserID'] ?? '';
$Fname = $_SESSION['Fname'];
$Lname = $_SESSION['Lname'];
$Avatar = $_SESSION['Avatar'] ?? '';
$Title = $_SESSION['Title'] ?? '';
$Content = $_SESSION['Content'] ?? '';
$Category = $_SESSION['Category'] ?? '';
$Classification = $_SESSION['Classification'] ?? '';
$Status = 'Deactivated';
$date = date('Y-m-d'); // Get current date

$Fname = $_SESSION['Fname'];
$Avatar = $_SESSION['Avatar'];

// Get database connection
$conn = getDBConnection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" Content="IE=edge">
   <meta name="viewport" Content="width=device-width, initial-scale=1.0">
   <Title>dashboard</Title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php require_once "../components/header_coach.php"; ?>

<div id="menu-btn" class="fas fa-bars"></div>

<!-- admin dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Bataan Peninsula State University</h1>

   <div class="box-container">

   <!-- boxes -->
   <div class="box">
      <h3>Welcome!</h3>
      <p><?php echo htmlspecialchars($Fname); ?></p>
         <a href="update_profile.php" class="btn">Update Profile</a>
      </div>

      <?php
      // Fetch numbers of posts added by admin
      $select_posts = $conn->prepare("SELECT COUNT(posts.PostID) AS num_posts 
                           FROM posts 
                           JOIN admins ON posts.AdminID = admins.AdminID 
                           WHERE admins.AdminID = ?");
      $select_posts->bind_param("i", $AdminID);
      $select_posts->execute();
      $numbers_of_posts_result = $select_posts->get_result();
      $numbers_of_posts = $numbers_of_posts_result->fetch_assoc()['num_posts'];
      $select_posts->close();

      // Fetch numbers of active posts
      $select_active_posts = $conn->prepare("SELECT COUNT(posts.PostID) AS num_posts 
                                 FROM posts WHERE Status = 'Active' AND AdminID = ?");
      $select_active_posts->bind_param("i", $AdminID);
      $select_active_posts->execute();
      $numbers_of_active_posts_result = $select_active_posts->get_result();
      $numbers_of_active_posts = $numbers_of_active_posts_result->fetch_assoc()['num_posts'];
      $select_active_posts->close();

      // Fetch numbers of deactivated posts
      $select_deactivated_posts = $conn->prepare("SELECT COUNT(posts.PostID) AS num_posts 
                                 FROM posts WHERE Status = 'Deactivated' AND AdminID = ?");
      $select_deactivated_posts->bind_param("i", $AdminID);
      $select_deactivated_posts->execute();
      $numbers_of_deactivated_posts_result = $select_deactivated_posts->get_result();
      $numbers_of_deactivated_posts = $numbers_of_deactivated_posts_result->fetch_assoc()['num_posts'];
      $select_deactivated_posts->close();


      // Set the appropriate user role based on the admin role
      $userRole = '';
      $adminRole = $_SESSION['Role'] ?? '';
      if ($adminRole == 'Coach in Sports' || $adminRole == 'Student Athletes Officer') {
         $userRole = 'Athletes';
      } elseif ($adminRole == 'Coach in Performers and Artists' || $adminRole == 'Student Performers Officer') {
         $userRole = 'Performers and Artists';
      }

      // Prepare the SQL statement to count the number of comments based on the user role
      $select_reported_comments = $conn->prepare(" SELECT COUNT(reports.ReportID) AS num_reported_comments
         FROM reports
         JOIN comments ON reports.CommentID = comments.CommentID
         JOIN users ON comments.UserID = users.UserID
         WHERE users.Role = ?
      ");
      $select_reported_comments->bind_param("s", $userRole);
      $select_reported_comments->execute();
      $numbers_of_reported_comments_result = $select_reported_comments->get_result();
      $numbers_of_reported_comments = $numbers_of_reported_comments_result->fetch_assoc()['num_reported_comments'];
      $select_reported_comments->close();


      // Fetch numbers of likes added to admin's posts
      $select_likes = $conn->prepare("SELECT COUNT(likes.LikeID) AS num_likes 
      FROM likes 
      JOIN posts ON likes.PostID = posts.PostID 
      WHERE posts.AdminID = ?");
      $select_likes->bind_param("i", $AdminID);
      $select_likes->execute();
      $numbers_of_likes_result = $select_likes->get_result();
      $numbers_of_likes = $numbers_of_likes_result->fetch_assoc()['num_likes'];
      $select_likes->close();

       // Prepare the SQL statement to count the number of users with the same classification as admins
      $count_users_with_same_classification = $conn->prepare("SELECT COUNT(*) FROM users INNER JOIN admins ON users.Classification = admins.Classification");
      $count_users_with_same_classification->execute();
      $count_users_with_same_classification->bind_result($number_of_users_with_same_classification);
      $count_users_with_same_classification->fetch();
      $count_users_with_same_classification->close();

      // Fetch number of categories
      $select_categories = $conn->prepare("SELECT COUNT(CategoryID) AS num_categories FROM categories");
      $select_categories->execute();
      $numbers_of_categories_result = $select_categories->get_result();
      $numbers_of_categories = $numbers_of_categories_result->fetch_assoc()['num_categories'];
      $select_categories->close();


      // Determine which role to count tryouts for based on admin role
      $Role = $_SESSION['Role'];

      $role_to_count = '';
      if ($Role == 'Coach in Sports' || $Role == 'Student Athletes Officer') {
         $role_to_count = 'Athletes';
      } elseif ($Role == 'Coach in Performers and Artists' || $Role == 'Student Performers Officer') {
         $role_to_count = 'Performers and Artists';
      }

      $numbers_of_tryouts = 0;
      if ($role_to_count != '') {
         // Count all TryOutID in the tryouts table with the specified role in the users table
         $sql_count = "SELECT COUNT(tryouts.TryOutID) AS count_tryouts FROM tryouts 
                        JOIN users ON tryouts.UserID = users.UserID 
                        WHERE users.Role = ?";
         
         // Prepare and execute the statement
         $count_stmt = $conn->prepare($sql_count);
         $count_stmt->bind_param('s', $role_to_count);
         $count_stmt->execute();
         $count_result = $count_stmt->get_result();
         
         if ($count_result->num_rows > 0) {
            $row = $count_result->fetch_assoc();
            $numbers_of_tryouts = $row['count_tryouts'];
         }
      }
      
      ?>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_posts); ?></h3>
         <p>Posts Added</p>
         <a href="add_posts.php" class="btn">Add New Post</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_active_posts); ?></h3>
         <p>Active Posts</p>
         <a href="view_posts.php" class="btn">See Posts</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_deactivated_posts); ?></h3>
         <p>Deactivated Posts</p>
         <a href="deactivated_posts.php" class="btn">See Posts</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($number_of_users_with_same_classification); ?></h3>
         <p>User Accounts</p>
         <a href="users_accounts.php" class="btn">See Users</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_reported_comments); ?></h3>
         <p>Comments Added</p>
         <a href="comments.php" class="btn">See Comments</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_likes); ?></h3>
         <p>Total Likes</p>
         <a href="likes.php" class="btn">See Posts</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_categories); ?></h3>
         <p>Categories</p>
         <a href="categories.php" class="btn">Add Category</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_tryouts); ?></h3>
         <p>Tryouts List</p>
         <a href="tryout_list.php" class="btn">See List</a>
      </div>
   </div>

   
</section>

<!-- admin dashboard section ends -->










<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>