<?php
require_once '../login-sec/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();

    if (isset($_SESSION['UserID'])) {
        $UserID = $_SESSION['UserID'];
    } else {
        $UserID = '';
    };

    include '../components/like_post.php';
}
// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];

// Fetch categories
$conn = getDBConnection();
$sql = "SELECT CategoryID, Category FROM categories";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/frontpage.css">
    <style>
        .btn {
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

    <section class="categories">

        <h1 class="heading">Categories</h1>

        <div class="box-container">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="box"><span>' . '</span><a href="category.php?Category=' . urlencode($row["Category"]) . '">' . htmlspecialchars($row["Category"]) . '</a></div>';
                }
            } else {
                echo "<p>No categories found</p>";
            }
            ?>
        </div>

    </section>

    <!-- custom js file link  -->
    <script src="../js/script.js"></script>

</body>

</html>