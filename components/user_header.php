<?php

// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<header class="navbar">
    <a href="../pages/frontpage.php"><img class="logo" src="../img/bpsu.png" alt="logo"></a>

    <form action="search.php" method="POST" class="search-form">
        <input type="text" name="search_box" class="box" maxlength="100" placeholder="Search" required>
        <button type="submit" class="fas fa-search" name="search_btn"></button>
    </form>

    <ul class="navlist">
        <li><a href="../pages/frontpage.php">Home</a></li>
        <li><a href="../activities/activities.php">Activities</a></li>
    </ul>

    <div class="profile-dropdown">
        <div class="profile-dropdown-btn" onclick="toggle()">
            <div class="profile-img">
                <img src="../upload/<?php echo htmlspecialchars($Avatar_header); ?>" alt="Profile Picture">
                <i class="fa-solid fa-circle"></i>
            </div>
            <span>
                <?php echo htmlspecialchars($Fname_header); ?>
                <i class="fa-solid fa-angle-down"></i>
            </span>
        </div>

        <ul class="profile-dropdown-list">
            <li class="profile-dropdown-list-item">
                <a href="../pages/update.php">
                    <i class="fa-regular fa-user"></i>
                    Profile
                </a>
            </li>
            <li class="profile-dropdown-list-item">
                <a href="#">
                    <i class="fa-regular fa-envelope"></i>
                    Inbox
                </a>
            </li>
            <li class="profile-dropdown-list-item">
                <a href="../calendar/user_calendar.php">
                    <i class="fa-regular fa-calendar"></i>
                    Calendar
                </a>
            </li>
            <li class="profile-dropdown-list-item">
                <a href="../feedback/contact.php">
                    <i class="fa-regular fa-file-alt"></i>
                    Give Feedback
                </a>
            </li>
            <hr />
            <li class="profile-dropdown-list-item">
                <a href="../login-sec/logout-user.php">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Log out
                </a>
            </li>
        </ul>
    </div>

    <div class="bx bx-menu" id="menu-icon"></div>
</header>