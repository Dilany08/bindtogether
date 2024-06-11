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

<?php require_once "../components/headerSuperAdmin.php"; ?>

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

      // Fetch number of users
      $select_athlete_performer_artist = $conn->prepare("SELECT * FROM users");
      $select_athlete_performer_artist->execute();
      $numbers_of_athlete_performer_artist = $select_athlete_performer_artist->get_result()->num_rows;
      $select_athlete_performer_artist->close();

      // Fetch number of admins
      $select_coach_professor_officer = $conn->prepare("SELECT * FROM admins");
      $select_coach_professor_officer->execute();
      $numbers_of_coach_professor_officer = $select_coach_professor_officer->get_result()->num_rows;
      $select_coach_professor_officer->close();

      ?>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_athlete_performer_artist); ?></h3>
         <p>User Accounts</p>
         <a href="users_accounts.php" class="btn">See Users</a>
      </div>

      <div class="box">
         <h3><?php echo htmlspecialchars($numbers_of_coach_professor_officer); ?></h3>
         <p>Admin/Coach Accounts</p>
         <a href="admin_accounts.php" class="btn">See Admins</a>
      </div>

</section>

<!-- admin dashboard section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
