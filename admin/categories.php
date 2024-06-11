<?php
require_once "../login-sec/connection.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['Fname']) || !isset($_SESSION['Avatar'])) {
    header("Location: ../login-sec/login.php");
    exit();
}

$Fname = $_SESSION['Fname'] ?? '';
$Lname = $_SESSION['Lname'] ?? '';
$AdminID = $_SESSION['AdminID'] ?? '';
$Avatar = $_SESSION['Avatar'] ?? '';

$conn = getDBConnection();
$message = '';
$message_class = '';

if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    if (!empty($category_name)) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE Category = ?");
        $stmt->bind_param('s', $category_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO categories (Category) VALUES (?)");
            $stmt->bind_param('s', $category_name);
            if ($stmt->execute()) {
                $message = 'Category added successfully!';
                $message_class = 'alert-success';
            } else {
                $message = 'Error adding category!';
                $message_class = 'alert-error';
            }
            $stmt->close();
        } else {
            $message = 'Category already exists!';
            $message_class = 'alert-error';
        }
    } else {
        $message = 'Category name cannot be empty!';
        $message_class = 'alert-error';
    }
}

if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("DELETE FROM categories WHERE CategoryID = ?");
    $stmt->bind_param('i', $category_id);
    if ($stmt->execute()) {
        $message = 'Category deleted successfully!';
        $message_class = 'alert-success';
    } else {
        $message = 'Error deleting category!';
        $message_class = 'alert-error';
    }
    $stmt->close();
}

$categories = $conn->query("SELECT * FROM categories");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .category-management {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: solid #c72d2d;
            border-radius: 1rem;
            background-color: #ffff;
        }

        .category-management h4, .category-management h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .category-management form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .category-management form input[type="text"] {
            width: 80%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .category-management form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #c72d2d;
            color: white;
            cursor: pointer;
        }

        .category-management ul {
            list-style: none;
            padding: 0;
        }

        .category-management ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #c72d2d;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .category-management ul li form {
            margin: 0;
            display: inline;
        }

        .category-management ul li form input[type="submit"] {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #c72d2d;
            color: white;
            cursor: pointer;
        }

        .alert {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #4CAF50;
            color: white;
        }

        .alert-error {
            background-color: #c72d2d;
            color: white;
        }
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
<?php require_once "../components/header_coach.php"; ?>

<a href="dashboard.php" class="btn btn-secondary back-button">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>

<section class="category-management">
    <h4 class="heading">Manage Categories</h4>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form action="" method="post">
        <h5>Add New Category</h5>
        <input type="text" name="category_name" maxlength="100" required placeholder="Category Name" class="box">
        <input type="submit" name="add_category" value="Add Category" class="btn">
    </form>

    <h2 class="heading">Existing Categories</h2>
    <ul>
        <?php while ($row = $categories->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($row['Category']); ?>
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="category_id" value="<?php echo $row['CategoryID']; ?>">
                    <input type="submit" name="delete_category" value="Delete" class="btn btn-danger">
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
</section>

</body>
</html>
