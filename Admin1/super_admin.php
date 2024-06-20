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

         <!-- boxes -->
         <div class="box">
            <h3>Welcome!</h3>
            <p><?php echo htmlspecialchars($Fname); ?></p>
            <a href="upDate_profile.php" class="btn">Update Profile</a>
         </div>

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



         // Fetch the role of the admin
         $AdminID = $_SESSION['AdminID'];
         $select_admin_role = $conn->prepare("SELECT Role FROM admins WHERE AdminID = ?");
         $select_admin_role->bind_param("i", $AdminID);
         $select_admin_role->execute();
         $admin_role_result = $select_admin_role->get_result();
         $admin_role = $admin_role_result->fetch_assoc()['Role'];
         $select_admin_role->close();

         // Determine the user role to count based on admin role
         $user_role = '';
         if ($admin_role == 'Sports Director') {
            $user_role = 'Athletes';
         } elseif ($admin_role == 'Performers and Artists Director') {
            $user_role = 'Performers and Artists';
         }

         if ($user_role != '') {
            // Fetch the number of users with the determined role
            $select_users = $conn->prepare("SELECT * FROM users WHERE Role = ?");
            $select_users->bind_param("s", $user_role);
            $select_users->execute();
            $number_of_users = $select_users->get_result()->num_rows;
            $select_users->close();
         }

         // Prepare the query to fetch the role of the admin
         $select_role = $conn->prepare("SELECT Role FROM admins WHERE Role IN ('Sports Director', 'Performers and Artists Director')");
         $select_role->execute();
         $result_role = $select_role->get_result();

         $numbers_of_coach_and_officers = 0;

         while ($row = $result_role->fetch_assoc()) {
            if ($row['Role'] == 'Sports Director') {
               // Count 'Coach in Sports' and 'Student Athletes Officer' if the role is 'Sports Director'
               $select_coach_and_officers = $conn->prepare("SELECT * FROM admins WHERE Role IN ('Coach in Sports', 'Student Athletes Officer')");
               $select_coach_and_officers->execute();
               $numbers_of_coach_and_officers = $select_coach_and_officers->get_result()->num_rows;
               $select_coach_and_officers->close();
            } elseif ($row['Role'] == 'Performers and Artists Director') {
               // Count 'Coach in Performers and Artists' and 'Student Performers Officer' if the role is 'Performers and Artists Director'
               $select_coach_and_officers = $conn->prepare("SELECT * FROM admins WHERE Role IN ('Coach in Performers and Artists', 'Student Performers Officer')");
               $select_coach_and_officers->execute();
               $numbers_of_coach_and_officers = $select_coach_and_officers->get_result()->num_rows;
               $select_coach_and_officers->close();
            }
         }

         $select_role->close();

         // Fetch the number of reported comments on admin's posts
         $Role = $_SESSION['Role'];
         $AdminID = $_SESSION['AdminID'];

         $num_reported_comments = 0;

         if ($Role == 'Sports Director') {
            $query = "SELECT COUNT(comments.CommentID) AS num_reported_comments 
                  FROM comments 
                  JOIN posts ON comments.PostID = posts.PostID 
                  JOIN reports ON comments.CommentID = reports.CommentID 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
         } elseif ($Role == 'Performers and Artists Director') {
            $query = "SELECT COUNT(comments.CommentID) AS num_reported_comments 
                  FROM comments 
                  JOIN posts ON comments.PostID = posts.PostID 
                  JOIN reports ON comments.CommentID = reports.CommentID 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
         }

         $select_reported_comments = $conn->prepare($query);
         $select_reported_comments->bind_param("i", $AdminID);
         $select_reported_comments->execute();
         $numbers_of_reported_comments_result = $select_reported_comments->get_result();
         $num_reported_comments = $numbers_of_reported_comments_result->fetch_assoc()['num_reported_comments'];
         $select_reported_comments->close();


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


         // Fetch number of pending posts
         $Role = $_SESSION['Role'];
         $AdminID = $_SESSION['AdminID'];

         $pending_posts_count = 0;

         if ($Role == 'Sports Director') {
            $query = "SELECT COUNT(PostID) AS pending_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE posts.Status = 'Pending' 
                  AND (admins.Role = 'Coach in Sports' OR admins.Role = 'Student Athletes Officer' OR posts.AdminID = ?)";
         } elseif ($Role == 'Performers and Artists Director') {
            $query = "SELECT COUNT(PostID) AS pending_posts 
                  FROM posts 
                  JOIN admins ON posts.AdminID = admins.AdminID 
                  WHERE posts.Status = 'Pending' 
                  AND (admins.Role = 'Coach in Performers and Artists' OR admins.Role = 'Student Performers Officer' OR posts.AdminID = ?)";
         }

         $select_pending_posts = $conn->prepare($query);
         $select_pending_posts->bind_param("i", $AdminID);
         $select_pending_posts->execute();
         $pending_posts_result = $select_pending_posts->get_result();
         $numbers_of_pending_posts = $pending_posts_result->fetch_assoc()['pending_posts'];
         $select_pending_posts->close();


         // Fetch number of added events
         $select_events = $conn->prepare("SELECT COUNT(EventID) AS num_events FROM calendar");
         $select_events->execute();
         $numbers_of_events_result = $select_events->get_result();
         $numbers_of_added_events = $numbers_of_events_result->fetch_assoc()['num_events'];
         $select_events->close();

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
            <h3><?php echo htmlspecialchars($number_of_users); ?></h3>
            <p>User Accounts</p>
            <a href="users_accounts.php" class="btn">See Users</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_coach_and_officers); ?></h3>
            <p>Admin Accounts</p>
            <a href="admin_accounts.php" class="btn">See Admins</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($num_reported_comments); ?></h3>
            <p>User Comments</p>
            <a href="comments.php" class="btn">See Comments</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($num_likes); ?></h3>
            <p>Total Likes</p>
            <a href="likes.php" class="btn">See Posts</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_pending_posts); ?></h3>
            <p>Pending Posts</p>
            <a href="pending_posts.php" class="btn">See Posts</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_added_events); ?></h3>
            <p>Added Events</p>
            <a href="../calendar/calendar.php" class="btn">See Calendar</a>
         </div>

         <div class="box">
            <h3><?php echo htmlspecialchars($numbers_of_categories); ?></h3>
            <p>Categories</p>
            <a href="categories.php" class="btn">Add Category</a>
         </div>

      </div>

   </section>

   <!-- admin dashboard section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>