<?php require_once "../login-sec/connection.php"; ?>
<?php
session_start();

$Fname = $_SESSION['Fname'];
$Avatar = $_SESSION['Avatar'];

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
   header("Location: ../login-sec/login.php");
   exit();
}

// Get database connection
$conn = getDBConnection();

if (isset($_POST['Deactivate'])) {
   $UserID = $_POST['UserID'];
   $update_status = $conn->prepare("UPDATE users SET Active = 0 WHERE UserID = ?");
   $update_status->bind_param("i", $UserID);
   $update_status->execute();
   $update_status->close();
}

if (isset($_POST['Activate'])) {
   $UserID = $_POST['UserID'];
   $update_status = $conn->prepare("UPDATE users SET Active = 1 WHERE UserID = ?");
   $update_status->bind_param("i", $UserID);
   $update_status->execute();
   $update_status->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
   <Style>
      .back-button {
         display: inline-block;
         width: 7rem;
         padding: 8px 10px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         text-align: left;
      }

      .back-button i {
         margin-right: 1px;
      }

      .back-button:hover {
         background-color: #c72d2d;
      }
   </Style>
</head>

<body>

   <?php require_once "../components/header_coach.php"; ?>
   <a href="dashboard.php" class="btn btn-secondary back-button">
      <i class="fa-solid fa-arrow-left"></i> Back
   </a>


   </header>

   <!-- users accounts section starts  -->

   <section class="accounts">

      <h1 class="heading">users account</h1>

      <div class="box-container">

         <?php
         $conn = getDBConnection();

         // Prepare the SQL statement to join users and admins tables based on classification
         $select_account = $conn->prepare("SELECT users.UserID, CONCAT(users.Fname, ' ', COALESCE(users.Mname, ''), ' ', users.Lname) AS name, 
        users.Gender, users.Classification, users.Email, users.PhoneNum, users.Campus, users.BirthDate, users.Active 
    FROM users INNER JOIN admins ON users.Classification = admins.Classification");
         $select_account->execute();
         $select_account->store_result();

         if ($select_account->num_rows > 0) {
            $select_account->bind_result($UserID, $name, $Gender, $Classification, $Email, $PhoneNum, $Campus, $BirthDate, $Active);
            while ($select_account->fetch()) {
               $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE UserID = ?");
               $count_user_comments->bind_param("i", $UserID);
               $count_user_comments->execute();
               $count_user_comments->store_result();
               $total_user_comments = $count_user_comments->num_rows;

               $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE UserID = ?");
               $count_user_likes->bind_param("i", $UserID);
               $count_user_likes->execute();
               $count_user_likes->store_result();
               $total_user_likes = $count_user_likes->num_rows;

               $is_Active = $Active == 1;

               // Your code to display or process the user data
         ?>

               <div class="box">
                  <p> User ID: <span><?= htmlspecialchars($UserID); ?></span> </p>
                  <p> Name: <span><?= htmlspecialchars($name); ?></span> </p>
                  <p> Gender: <span><?= htmlspecialchars($Gender); ?></span> </p>
                  <p> Classification: <span><?= htmlspecialchars($Classification); ?></span> </p>
                  <p> Email: <span><?= htmlspecialchars($Email); ?></span> </p>
                  <p> Birthdate: <span><?= htmlspecialchars($BirthDate); ?></span> </p>
                  <p> Phone Number: <span><?= htmlspecialchars($PhoneNum); ?></span> </p>
                  <p> Campus: <span><?= htmlspecialchars($Campus); ?></span> </p>
                  <p> Total Comments: <span><?= htmlspecialchars($total_user_comments); ?></span> </p>
                  <p> Total Likes: <span><?= htmlspecialchars($total_user_likes); ?></span> </p>
                  <p> Status: <span><?= $is_Active ? 'Active' : 'Inactive'; ?></span></p>
                  <form action="" method="POST">
                     <input type="hidden" name="UserID" value="<?= htmlspecialchars($UserID); ?>">
                     <?php if ($Active) : ?>
                        <button type="submit" name="Deactivate" onclick="return confirm('Deactivate the account?');" class="delete-btn" style="margin-bottom: .5rem;">Deactivate</button>
                     <?php else : ?>
                        <button type="submit" name="Activate" onclick="return confirm('Activate the account?');" class="option-btn" style="margin-bottom: .5rem;">Activate</button>
                     <?php endif; ?>
                  </form>
               </div>
         <?php
            }
            $select_account->free_result();
         } else {
            echo '<p class="empty">no accounts available</p>';
         }
         $select_account->close();
         $conn->close();
         ?>

      </div>

   </section>

   <!-- users accounts section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>