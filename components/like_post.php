<?php

// Handle like post action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post'])) {
    $PostID = $_POST['PostID'];
    $UserID = $_SESSION['UserID'];

    $conn = getDBConnection();
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the user has already liked the post
    $check_like = $conn->prepare("SELECT * FROM likes WHERE UserID = ? AND PostID = ?");
    $check_like->bind_param("ii", $UserID, $PostID);
    $check_like->execute();
    $result_check_like = $check_like->get_result();

    if ($result_check_like->num_rows > 0) {
        // User has already liked the post, so unlike it
        $unlike_post = $conn->prepare("DELETE FROM likes WHERE UserID = ? AND PostID = ?");
        $unlike_post->bind_param("ii", $UserID, $PostID);
        $unlike_post->execute();
        $unlike_post->close();
    } else {
        // User has not liked the post yet, so like it
        $like_post = $conn->prepare("INSERT INTO likes (UserID, PostID) VALUES (?, ?)");
        $like_post->bind_param("ii", $UserID, $PostID);
        $like_post->execute();
        $like_post->close();
    }

    $check_like->close();
    $conn->close();
    
    // Redirect to the same page to avoid form resubmission
    header("Location: #like");
    exit();
}