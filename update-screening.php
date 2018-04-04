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
    $screening_id = trim($_POST['screening_id']);
    $theatre_id = trim($_POST['theatre_id_'.$screening_id]);
    $auditorium_number = trim($_POST['auditorium_number_'.$screening_id]);
    $movie_id = trim($_POST['movie_id_'.$screening_id]);
    $date = trim($_POST['date_'.$screening_id]);
    $time = trim($_POST['time_'.$screening_id]);
    $date_time = $date.' '.$time.':00';
    
    // Prepare an insert statement
    $sql = "UPDATE screening SET
    theatre_id = '$theatre_id', auditorium_number = '$auditorium_number', movie_id = '$movie_id',
    date_time = '$date_time'
    WHERE
    screening_id = $screening_id";

    if (mysqli_query($link, $sql)) {
        // Close connection
        mysqli_close($link);
        header("location: /dashboard");
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}
