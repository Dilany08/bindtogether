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
    <a href="../super_admin/super_admin.php" class="logo">SuperAdmin<span>Panel</span></a>

    <div class="profile">
        <div class="profile-img">
            <img src="../upload/<?php echo htmlspecialchars($Avatar); ?>" alt="Profile Picture">
            <i class="fa-solid fa-circle"></i>
        </div>
        <p><span><?php echo htmlspecialchars($Fname); ?></span>
        <p>
    </div>

    <nav class="navbar">
        <a href="../super_admin/super_admin.php"><i class="fas fa-home"></i> <span>home</span></a>
        <a href="../login-sec/logout-user.php"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
    </nav>

    <div class="flex-btn">
        <a href="../login-sec/signup-admin.php" class="option-btn">Register New Admin</a>
    </div>
</header>