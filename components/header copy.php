<?php
// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Avatar = $_SESSION['Avatar'];
?>

<header class="header">
        <a href="../superAdmin/super_admin.php" class="logo">Admin<span>Panel</span></a>

        <div class="profile">
        <div class="profile-img">
        <img src="../upload/<?php  echo htmlspecialchars($Avatar); ?>" alt="Profile Picture">
        <i class="fa-solid fa-circle"></i> 
        </div>
        <p><span><?php echo htmlspecialchars($Fname); ?></span><p>
        </div>

        <nav class="navbar">
        <a href="../superAdmin/super_admin.php"><i class="fas fa-home"></i> <span>home</span></a>
        <a href="../superAdmin/add_posts.php"><i class="fas fa-pen"></i> <span>Add a Post</span></a>
        <a href="../calendar/calendar.php"><i class="fas fa-eye"></i> <span>Schedule</span></a>
        <a href="admin_accounts.php"><i class="fas fa-user"></i> <span>Accounts</span></a>
        <a href="../login-sec/logout-user.php"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
        </nav>

        <div class="flex-btn">
        <a href="../login-sec/signup-admin.php" class="option-btn">Register New Admin</a>
        </div>
</header>
