<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /");
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare an insert statement
    $sql = "INSERT INTO movie_review
  (movie_id, user_id, rating, comments)
  VALUES
  (?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssss", $param_movie_id, $param_user_id, $param_rating, $param_comments);

        // Set parameters
        $param_movie_id = trim($_POST['movie_id']);
        $param_user_id = trim($_POST['user_id']);
        $param_rating = trim($_POST['rating']);
        $param_comments = trim($_POST['comments']);
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to login page
            header("location: /");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
