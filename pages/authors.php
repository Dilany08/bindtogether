<?php
require_once '../login-sec/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];

$Date = Date('Y-m-d'); // Get current Date

// Retrieve user information for the posts
$Fname_post = $_SESSION['Fname'];
$Lname_post = $_SESSION['Lname'];
$admin_Avatar = $_SESSION['Avatar'];


if (isset($_SESSION['UserID'])) {
    $UserID = $_SESSION['UserID'];
} else {
    $UserID = '';
}

if (isset($_GET['AdminID'])) {
    $AdminID = $_GET['AdminID'];
} else {
    $AdminID = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/frontpage.css">
    <style>
        .btn{
            margin-left: 5px;
        }
        .back-button {
            display: inline-block;
            width: 5rem;
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
        }

        .back-button i {
            margin-right: 3px;
        }

        .back-button:hover {
            background-color: #c72d2d;
        }
    </style>
</head>
<body>
    <?php require_once "../components/user_header.php" ?>
     <!-- Back button -->
     <a href="frontpage.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <section class="authors">

        <h1 class="heading">authors</h1>

        <div class="box-container">

            <?php
            $conn = getDBConnection();
            // Fetch all authors
            $select_author = $conn->query("SELECT DISTINCT admins.* FROM admins JOIN posts ON admins.AdminID = posts.AdminID");

            if ($select_author->num_rows > 0) {
                while ($fetch_authors = $select_author->fetch_assoc()) { 

                    // Count active posts by author
                    $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE AdminID = ? AND Status = ?");
                    $count_admin_posts->bind_param("is", $fetch_authors['AdminID'], $Status);
                    $Status = 'active';
                    $count_admin_posts->execute();
                    $count_admin_posts->store_result();
                    $total_admin_posts = $count_admin_posts->num_rows;
                    $count_admin_posts->close();

                    // Count likes for author's posts by logged-in user
                    $count_admin_likes = $conn->prepare(" SELECT COUNT(*) FROM `likes` JOIN `posts` ON `likes`.PostID = `posts`.PostID 
                        WHERE `posts`.AdminID = ? AND `likes`.UserID = ?
                    ");
                    $count_admin_likes->bind_param("ii", $fetch_authors['AdminID'], $UserID);
                    $count_admin_likes->execute();
                    $count_admin_likes->bind_result($total_admin_likes);
                    $count_admin_likes->fetch();
                    $count_admin_likes->close();

                    // Count comments for author's posts by logged-in user
                    $count_admin_comments = $conn->prepare(" SELECT COUNT(*) FROM `comments` JOIN `posts` ON `comments`.PostID = `posts`.PostID 
                        WHERE `posts`.AdminID = ? AND `comments`.UserID = ?
                    ");
                    $count_admin_comments->bind_param("ii", $fetch_authors['AdminID'], $UserID);
                    $count_admin_comments->execute();
                    $count_admin_comments->bind_result($total_admin_comments);
                    $count_admin_comments->fetch();
                    $count_admin_comments->close();
            ?>
            <div class="box">
                <p>author : <span><?= htmlspecialchars($fetch_authors['Fname']. ' ' .$fetch_authors['Lname']); ?></span></p>
                <p>total posts : <span><?= $total_admin_posts; ?></span></p>
                <p>posts likes : <span><?= $total_admin_likes; ?></span></p>
                <p>posts comments : <span><?= $total_admin_comments; ?></span></p>
                <a href="admin_posts.php?AdminID=<?= htmlspecialchars($fetch_authors['Fname']. ' ' .$fetch_authors['Lname']); ?>" class="btn">view posts</a>
            </div>
            <?php
                }
            } else {
                echo '<p class="empty">no authors found</p>';
            }
            ?>

        </div>
        
    </section>

    <!-- custom js file link  -->
    <script src="../js/script.js"></script>

</body>
</html>
