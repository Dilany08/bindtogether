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
    <a href="../Admin1/super_admin.php" class="logo">Admin<span>Panel</span></a>

    <div class="profile">
        <div class="profile-img">
            <img src="../upload/<?php echo htmlspecialchars($Avatar); ?>" alt="Profile Picture">
            <i class="fa-solid fa-circle"></i>
        </div>
        <p><span><?php echo htmlspecialchars($Fname); ?></span>
        <p>
    </div>

    <nav class="navbar">
        <a href="../Admin1/super_admin.php"><i class="fas fa-home"></i> <span>home</span></a>
        <a href="../Admin1/update_profile.php"><i class="fa-regular fa-user"></i> <span>Update Profile</span></a>
        <a href="../Admin1/posts.php"><i class="fa-regular fa-file-alt"></i> <span>Posts/Activities</span></a>
        <a href="../Admin1/users_accounts.php"><i class="fa-solid fa-user-friends"></i> <span>User Accounts</span></a>
        <a href="../Admin1/admin_accounts.php"><i class="fa-solid fa-user-edit"></i> <span>Admin Accounts</span></a>
        <a href="../Admin1/comments.php"><i class="fa-regular fa-comments"></i> <span>Reported Comments</span></a>
        <a href="#"><i class="fa-solid fa-bullhorn"></i> <span>Reported Posts</span></a>
        <a href="../calendar/calendar.php"><i class="fa-regular fa-calendar"></i> <span>Calendar</span></a>
        <a href="#"><i class="fa-solid fa-file-alt"></i> <span>Reports</span></a>
        
    </nav>

    <div class="flex-btn">
    <a href="../login-sec/logout-user.php" class="option-btn"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
    </div>
</header>