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
    $theatre_id = trim($_POST['theatre_id']);
    $theatre_name = trim($_POST['theatre_name_'.$theatre_id]);
    $phone_number = trim($_POST['phone_number_'.$theatre_id]);
    $street_number = trim($_POST['street_number_'.$theatre_id]);
    $street_name = trim($_POST['street_name_'.$theatre_id]);
    $city = trim($_POST['city_'.$theatre_id]);
    $province = trim($_POST['province_'.$theatre_id]);
    $postalcode = trim($_POST['postalcode_'.$theatre_id]);

    // Prepare an insert statement
    $sql = "UPDATE theatre SET
    theatre_name = '$theatre_name', phone_number = '$phone_number', street_number = '$street_number',
    street_name = '$street_name', city = '$city', province = '$province', postalcode = '$postalcode'
    WHERE
    theatre_id = $theatre_id";

    if (mysqli_query($link, $sql)) {
        // Close connection
        mysqli_close($link);
        header("location: /dashboard");
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}
