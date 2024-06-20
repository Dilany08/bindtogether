<header class="header">
        <a href="../admin/dashboard.php" class="logo">Home</span></a>

        <div class="profile">
                <div class="profile-img">
                        <img src="../upload/<?php echo htmlspecialchars($Avatar); ?>" alt="Profile Picture">
                        <i class="fa-solid fa-circle"></i>
                </div>
                <p><span><?php echo htmlspecialchars($Fname); ?></span>
                <p>
        </div>

        <nav class="navbar">
                <a href="../admin/dashboard.php"><i class="fas fa-home"></i> <span>Home</span></a>
                <a href="../admin/update_profile.php"><i class="fa-regular fa-user"></i> <span>Update Profile</span></a>
                <a href="../admin/posts.php"><i class="fas fa-pen"></i> <span>Posts/Activities</span></a>
                <a href="../admin/users_accounts.php"><i class="fa-solid fa-user-friends"></i> <span>Student Athletes</span></a>
                <a href="../admin/comments.php"><i class="fa-regular fa-comments"></i> <span>Reported Comments</span></a>
                <a href="#"><i class="fa-solid fa-bullhorn"></i> <span>Reported Posts</span></a>
                <a href="../admin/tryout_list.php"><i class="fa-solid fa-user-edit"></i> <span>Student Tryout List</span></a>
                <a href="../calendar/coach_calendar.php"><i class="fas fa-eye"></i> <span>Schedule</span></a>
                <a href="#"><i class="fa-solid fa-file-alt"></i> <span>Reports</span></a>
        </nav>

        <div class="flex-btn">
    <a href="../login-sec/logout-user.php" class="option-btn"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
    </div>
</header>