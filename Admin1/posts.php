<?php
require '../login-sec/connection.php';
session_start();

// Start session and check if user is logged in
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
$post_Status = 'Deactivated';
$Date = Date('Y-m-d'); // Get current Date

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

   <?php require_once "../components/header.php"; ?>

   <div id="menu-btn" class="fas fa-bars"></div>

<!-- admin dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Bataan Peninsula State University</h1>

   <div class="box-container">

   <?php
         $select_posts = $conn->prepare("SELECT COUNT(posts.PostID) AS PostID 
                     FROM posts 
                     JOIN admins ON posts.AdminID = admins.AdminID 
                     WHERE admins.AdminID = ?");
         $select_posts->bind_param("i", $AdminID);
         $select_posts->execute();
         $numbers_of_posts_result = $select_posts->get_result();
         $numbers_of_posts = $numbers_of_posts_result->fetch_assoc()['PostID'];
         $select_posts->close();

         // Fetch numbers of active posts for all admins with specified Classifications
         $Role = $_SESSION['Role'];
         $AdminID = $_SESSION['AdminID'];

         $num_active_posts = 0;

         if ($Role == 'Sports Director') {
            $query = "SELECT COUNT(posts.PostID) AS num_active_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE posts.Status = 'Active' 
                  AND (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
         } elseif ($Role == 'Performers and Artists Director') {
            $query = "SELECT COUNT(posts.PostID) AS num_active_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE posts.Status = 'Active' 
                  AND (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
         }

         $select_active_posts = $conn->prepare($query);
         $select_active_posts->bind_param("i", $AdminID);
         $select_active_posts->execute();
         $numbers_of_active_posts_result = $select_active_posts->get_result();
         $num_active_posts = $numbers_of_active_posts_result->fetch_assoc()['num_active_posts'];
         $select_active_posts->close();


         // Fetch numbers of deactivate posts
         $Role = $_SESSION['Role'];
         $AdminID = $_SESSION['AdminID'];

         $num_deactivated_posts = 0;

         if ($Role == 'Sports Director') {
            $query = "SELECT COUNT(posts.PostID) AS num_deactivated_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (posts.Status = 'Inactive' OR posts.Status = 'Deactivated') 
                  AND (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
         } elseif ($Role == 'Performers and Artists Director') {
            $query = "SELECT COUNT(posts.PostID) AS num_deactivated_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (posts.Status = 'Inactive' OR posts.Status = 'Deactivated') 
                  AND (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
         }

         $select_inactive_deactivated_posts = $conn->prepare($query);
         $select_inactive_deactivated_posts->bind_param("i", $AdminID);
         $select_inactive_deactivated_posts->execute();
         $numbers_of_inactive_deactivated_posts_result = $select_inactive_deactivated_posts->get_result();
         $num_deactivated_posts = $numbers_of_inactive_deactivated_posts_result->fetch_assoc()['num_deactivated_posts'];
         $select_inactive_deactivated_posts->close();


         // Fetch number of active activities
         $select_activities = $conn->prepare("SELECT COUNT(ActivityID) AS num_activities FROM activities WHERE AdminID = ? AND Status = 'Active'");
         $select_activities->bind_param("i", $AdminID);
         $select_activities->execute();
         $numbers_of_activities_result = $select_activities->get_result();
         $numbers_of_activities = $numbers_of_activities_result->fetch_assoc()['num_activities'];
         $select_activities->close();

         // Fetch number of active activities
         $select_inactive = $conn->prepare("SELECT COUNT(ActivityID) AS num_inactive FROM activities WHERE AdminID = ? AND Status = 'Inactive'");
         $select_inactive->bind_param("i", $AdminID);
         $select_inactive->execute();
         $numbers_of_inactive_result = $select_inactive->get_result();
         $numbers_of_inactive = $numbers_of_inactive_result->fetch_assoc()['num_inactive'];
         $select_inactive->close();

         // Fetched number of liked posts
         $Role = $_SESSION['Role'];
         $AdminID = $_SESSION['AdminID'];

         $num_likes = 0;

         if ($Role == 'Sports Director') {
            $query = "SELECT COUNT(likes.LikeID) AS num_likes 
                  FROM likes 
                  JOIN posts ON likes.PostID = posts.PostID 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
         } elseif ($Role == 'Performers and Artists Director') {
            $query = "SELECT COUNT(likes.LikeID) AS num_likes 
                  FROM likes 
                  JOIN posts ON likes.PostID = posts.PostID 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
         }

         $select_likes = $conn->prepare($query);
         $select_likes->bind_param("i", $AdminID);
         $select_likes->execute();
         $numbers_of_likes_result = $select_likes->get_result();
         $num_likes = $numbers_of_likes_result->fetch_assoc()['num_likes'];
         $select_likes->close();

          // Fetch number of categories
         $select_categories = $conn->prepare("SELECT COUNT(CategoryID) AS num_categories FROM categories");
         $select_categories->execute();
         $numbers_of_categories_result = $select_categories->get_result();
         $numbers_of_categories = $numbers_of_categories_result->fetch_assoc()['num_categories'];
         $select_categories->close();
?>

<div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_posts); ?></h3>
            <p>Posts Added</p>
            <a href="add_posts.php" class="btn">Add New Post</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($num_active_posts); ?></h3>
            <p>Active Posts</p>
            <a href="view_posts.php" class="btn">See Posts</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($num_deactivated_posts); ?></h3>
            <p>Deactivated Posts</p>
            <a href="deactivated_posts.php" class="btn">See Posts</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_categories); ?></h3>
            <p>Categories</p>
            <a href="categories.php" class="btn">Add Category</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_activities); ?></h3>
            <p>Add Activity</p>
            <a href="add_activity.php" class="btn">Add New Activity</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_activities); ?></h3>
            <p>View Activity</p>
            <a href="view_activity.php" class="btn">View Activity</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_inactive); ?></h3>
            <p>Inactive Activity</p>
            <a href="deactivated_activity.php" class="btn">View Inactive Activity</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($num_likes); ?></h3>
            <p>Total Likes</p>
            <a href="likes.php" class="btn">See Posts</a>
         </div>

         </div>

</section>

<!-- admin dashboard section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>

</html>