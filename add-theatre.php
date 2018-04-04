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
    $sql = "INSERT INTO theatre
    (theatre_name, phone_number, street_number, street_name, city, province, postalcode)
    VALUES
    (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sssssss", $param_theatre_name, $param_phone_number, $param_street_number, $param_street_name, $param_city, $param_province, $param_postalcode);

        // Set parameters
        $param_theatre_name = trim($_POST['theatre_name']);
        $param_phone_number = trim($_POST['phone_number']);
        $param_street_number = trim($_POST['street_number']);
        $param_street_name = trim($_POST['street_name']);
        $param_city = trim($_POST['city']);
        $param_province = trim($_POST['province']);
        $param_postalcode = trim($_POST['postalcode']);
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to login page
            header("location: /dashboard");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
