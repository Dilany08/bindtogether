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

   </section>

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>

<?php
// Close database connection
$conn->close();
?>