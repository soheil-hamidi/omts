<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['role']) || empty($_SESSION['role'])) {
    header("location: /");
    exit;
}

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /");
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare an insert statement
    $sql = "INSERT INTO movie
    (poster_url, title, duration_minutes, age_rating, plot, start_date, movie_supplier_id, end_date)
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssssssss", $param_poster_url, $param_title, $param_duration_minutes, $param_age_rating, $param_plot, $param_start_date, $param_movie_supplier_id, $param_end_date);

        // Set parameters
        $param_poster_url = trim($_POST['poster_url']);
        $param_title = trim($_POST['title']);
        $param_duration_minutes = trim($_POST['duration_minutes']);
        $param_age_rating = trim($_POST['age_rating']);
        $param_plot = trim($_POST['plot']);
        $param_start_date = trim($_POST['start_date']);
        $param_movie_supplier_id = trim($_POST['movie_supplier_id']);
        $param_end_date = trim($_POST['end_date']);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {

            $last_id = mysqli_insert_id($link);

            foreach ($_POST['actor_ids'] as $actor_id) {
              $sql_plays = "INSERT INTO plays
              (actor_id, movie_id)
              VALUES
              ($actor_id, $last_id)";
              if (!mysqli_query($link, $sql_plays)) {
                echo "Something went wrong. Please try again later.";
              }
            }

            $director_id = trim($_POST['director_id']);
            $sql_directs = "INSERT INTO directs
            (director_id, movie_id)
            VALUES
            ($director_id, $last_id)";
            if (!mysqli_query($link, $sql_directs)) {
              echo "Something went wrong. Please try again later.";
            }

            $production_company_id = trim($_POST['production_company_id']);
            $sql_production_company = "INSERT INTO makes
            (production_company_id, movie_id)
            VALUES
            ($production_company_id, $last_id)";
            if (!mysqli_query($link, $sql_production_company)) {
              echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
            // Redirect to login page
            header("location: /dashboard");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
