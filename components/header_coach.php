

<header class="header">
        <a href="../admin/dashboard.php" class="logo">Home</span></a>

        <div class="profile">
        <div class="profile-img">
        <img src="../upload/<?php  echo htmlspecialchars($Avatar); ?>" alt="Profile Picture">
        <i class="fa-solid fa-circle"></i> 
        </div>
        <p><span><?php echo htmlspecialchars($Fname); ?></span><p>
        </div>

        <nav class="navbar">
        <a href="../admin/dashboard.php"><i class="fas fa-home"></i> <span>Home</span></a>
        <a href="../admin/add_posts.php"><i class="fas fa-pen"></i> <span>Add a Post</span></a>
        <a href="../calendar/coach_calendar.php"><i class="fas fa-eye"></i> <span>Schedule</span></a>
        <a href="../login-sec/logout-user.php" style="color:var(--red);"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
        </nav>

</header>
