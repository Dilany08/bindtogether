<?php require_once "../login-sec/connection.php"; ?>
<?php 
session_start();

// Start session and check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'];
$Lname = $_SESSION['Lname'];
$Avatar = $_SESSION['Avatar'];
$AdminID = $_SESSION['AdminID'] ?? '';
$PostID = $_SESSION['PostID'] ?? '';

// Get database connection
$conn = getDBConnection();

if (isset($_POST['Deactivate'])) {
   $AdminID = $_POST['AdminID'];
   $update_status = $conn->prepare("UPDATE admins SET Active = 0 WHERE AdminID = ?");
   $update_status->bind_param("i", $AdminID);
   $update_status->execute();
   $update_status->close();
}

if (isset($_POST['Activate'])) {
   $AdminID = $_POST['AdminID'];
   $update_status = $conn->prepare("UPDATE admins SET Active = 1 WHERE AdminID = ?");
   $update_status->bind_param("i", $AdminID);
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
   <title>admins accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
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
   </style>

</head>
<body>
<?php require_once "../components/headerSuperAdmin.php"; ?>

<a href="super_admin.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

<!-- admins accounts section starts  -->

<section class="accounts">

   <h1 class="heading">Admins and Coaches account</h1>

   <div class="box-container">

   <div class="box" style="order: -2;">
      <p>Register New Admin</p>
      <a href="../login-sec/signup-admin.php" class="option-btn" style="margin-bottom: .5rem;">register</a>
   </div>

   <?php
      $select_account = $conn->prepare("SELECT AdminID, Fname, Mname, Lname, Email, Gender, PhoneNum, BirthDate, Campus, Role, Active FROM admins WHERE Role IN ('Performers and Artists Director', 'Sports Director', 'Student Performers Officer', 'Student Athletes Officer', 'Coach in Performers and Artists', 'Coach in Sports')");
      $select_account->execute();
      $result_account = $select_account->get_result();
      
      if ($result_account->num_rows > 0) {
          while ($fetch_accounts = $result_account->fetch_assoc()) {
              $count_admin_posts = $conn->prepare("SELECT * FROM posts WHERE AdminID = ?");
              $count_admin_posts->bind_param("i", $fetch_accounts['AdminID']);
              $count_admin_posts->execute();
              $total_admin_posts = $count_admin_posts->get_result()->num_rows;
              $count_admin_posts->close();
      
              $is_active = $fetch_accounts['Active'] == 1;
            ?>
        <div class="box" style="order: <?php echo htmlspecialchars($fetch_accounts['AdminID'] == $AdminID ? '-1' : ''); ?>;">
            <p> Admin ID: <span><?php echo htmlspecialchars($fetch_accounts['AdminID']); ?></span> </p>
            <p> Name: <span><?php echo htmlspecialchars($fetch_accounts['Fname']) . ' ' . htmlspecialchars($fetch_accounts['Mname']) . ' ' . htmlspecialchars($fetch_accounts['Lname']); ?></span> </p>
            <p> Email: <span><?php echo htmlspecialchars($fetch_accounts['Email']); ?></span> </p>
            <p> Gender: <span><?php echo htmlspecialchars($fetch_accounts['Gender']); ?></span> </p>
            <p> Phone Number: <span><?php echo htmlspecialchars($fetch_accounts['PhoneNum']); ?></span> </p>
            <p> Birth Date: <span><?php echo htmlspecialchars($fetch_accounts['BirthDate']); ?></span> </p>
            <p> Campus: <span><?php echo htmlspecialchars($fetch_accounts['Campus']); ?></span> </p>
            <p> Position: <span><?php echo htmlspecialchars($fetch_accounts['Role']); ?></span></p>
            <p> Total Posts: <span><?php echo htmlspecialchars($total_admin_posts); ?></span> </p>
            <p> Status: <span><?php echo $is_active ? 'Active' : 'Inactive'; ?></span></p>
                <form action="" method="POST">
                    <input type="hidden" name="AdminID" value="<?php echo htmlspecialchars($fetch_accounts['AdminID']); ?>">
                    <?php if ($is_active): ?>
                        <button type="submit" name="Deactivate" onclick="return confirm('Deactivate the account?');" class="delete-btn" style="margin-bottom: .5rem;">Deactivate</button>
                    <?php else: ?>
                        <button type="submit" name="Activate" onclick="return confirm('Activate the account?');" class="option-btn" style="margin-bottom: .5rem;">Activate</button>
                    <?php endif; ?>
                </form>
        </div>
        <?php
    }
} else {
    echo '<p class="empty">No accounts available</p>';
}
?>
    </div>
</section>
<!-- admins accounts section ends -->

</body>
</html>
