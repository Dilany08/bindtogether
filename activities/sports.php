<?php
session_start();

if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}
// Retrieve user information for the header
$Fname_header = $_SESSION['Fname'];
$Lname_header = $_SESSION['Lname'];
$Avatar_header = $_SESSION['Avatar'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/activities.css">
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
    <?php require_once "../components/user_header.php"; ?>

    <!-- Back button -->
    <a href="activities.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <div class="container-one">
        <h1>Join Activities</h1>
        <div class="grid">
            <?php
            $categories = [
                'Competitions' => 'sports_competitions.php',
                'Tryouts' => 'sports_tryouts.php',
                'Auditions' => 'sports_auditions.php'
            ];

            $images = [
                'Competitions' => '../img/competition.jpeg',
                'Tryouts' => '../img/tryouts.jpg',
                'Auditions' => '../img/practices.jpeg'
            ];

            foreach ($categories as $category => $file) : ?>
                <div class="card-activity">
                    <a href="<?php echo htmlspecialchars($file); ?>">
                        <div class="card-content">
                            <img src="<?php echo htmlspecialchars($images[$category]); ?>" alt="<?php echo htmlspecialchars($category); ?>">
                            <h2><?php echo htmlspecialchars($category); ?></h2>
                            <p>Click to view and join <?php echo htmlspecialchars($category); ?>.</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseover', () => {
                card.style.transform = 'scale(1.05)';
            });

            card.addEventListener('mouseout', () => {
                card.style.transform = 'scale(1)';
            });
        });
    </script>

    <script src="../js/script.js"></script>
</body>

</html>