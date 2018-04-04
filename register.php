<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (isset($_SESSION['email']) || !empty($_SESSION['email'])) {
    header("location: /");
    exit;
}

// Define variables and initialize with empty values

$email = $phone_number = $first_name = "";
$middle_name = $last_name = $street_number = "";
$street_name = $city = $province = "";
$postalcode = $password = $credit_card_number = "";
$credit_card_expiry = $confirm_password = "";
$email_err = $phone_number_err = $first_name_err = "";
$last_name_err = $street_number_err = $street_name_err = "";
$city_err = $province_err = $postalcode_err = "";
$password_err = $credit_card_number_err = $credit_card_expiry_err = "";
$confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM app_user WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already exist.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = 'Please provide phone number.';
    } else {
        $phone_number = trim($_POST['phone_number']);
    }

    // Validate first name
    if (empty(trim($_POST["first_name"]))) {
        $first_name_err = 'Please provide first name.';
    } else {
        $first_name = trim($_POST['first_name']);
    }

    // Validate middle name
    if (empty(trim($_POST["middle_name"]))) {
        $middle_name = null;
    } else {
        $middle_name = trim($_POST['middle_name']);
    }

    // Validate last name
    if (empty(trim($_POST["last_name"]))) {
        $last_name_err = 'Please provide last name.';
    } else {
        $last_name = trim($_POST['last_name']);
    }

    // Validate street number
    if (empty(trim($_POST["street_number"]))) {
        $street_number_err = 'Please provide street number.';
    } else {
        $street_number = trim($_POST['street_number']);
    }

    // Validate street name
    if (empty(trim($_POST["street_name"]))) {
        $street_name_err = 'Please provide street name.';
    } else {
        $street_name = trim($_POST['street_name']);
    }

    // Validate city
    if (empty(trim($_POST["city"]))) {
        $city_err = 'Please provide city.';
    } else {
        $city = trim($_POST['city']);
    }

    // Validate province
    if (empty(trim($_POST["province"]))) {
        $province_err = 'Please provide province.';
    } else {
        $province = trim($_POST['province']);
    }

    // Validate postal code
    if (empty(trim($_POST["postalcode"]))) {
        $postalcode_err = 'Please provide postal code.';
    } else {
        $postalcode = trim($_POST['postalcode']);
    }

    // Validate credit card number
    if (empty(trim($_POST["credit_card_number"]))) {
        $credit_card_number = 'Please provide credit card number.';
    } else {
        $credit_card_number = trim($_POST['credit_card_number']);
    }

    // Validate credit card expiry
    if (empty(trim($_POST["credit_card_expiry"]))) {
        $credit_card_expiry_err = 'Please provide credit card expiry.';
    } else {
        $credit_card_expiry = trim($_POST['credit_card_expiry']);
    }

    // Check input errors before inserting in database
    if (empty($email_err)
    && empty($phone_number_err)
    && empty($first_name_err)
    && empty($last_name_err)
    && empty($street_number_err)
    && empty($street_name_err)
    && empty($city_err)
    && empty($province_err)
    && empty($postalcode_err)
    && empty($credit_card_number_err)
    && empty($credit_card_expiry_err)
    && empty($password_err)
    && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO app_user
        (email, phone_number, first_name, middle_name, last_name, street_number, street_name, city, province, postalcode, password, credit_card_number, credit_card_expiry)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_email, $param_phone_number, $param_first_name, $param_middle_name, $param_last_name, $param_street_number, $param_street_name, $param_city, $param_province, $param_postalcode, $param_password, $param_credit_card_number, $param_credit_card_expiry);

            // Set parameters
            $param_email = $email;
            $param_phone_number = $phone_number;
            $param_first_name = $first_name;
            $param_middle_name = $middle_name;
            $param_last_name = $last_name;
            $param_street_number = $street_number;
            $param_street_name = $street_name;
            $param_city = $city;
            $param_province = $province;
            $param_postalcode = $postalcode;
            $param_credit_card_number = $credit_card_number;
            $param_credit_card_expiry = date("Y-m-d", strtotime($credit_card_expiry));
            $param_password = $password;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: /login");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>OMTS</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="/css/style.css">
</head>

<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 text-center">
        <br><br><br><br>
        <h1>Welcome!</h1>
        <br>
        <img src="/img/logo.png" alt="logo" class="responsive">
        <h2>Online Movie Ticket Service</h2>
        <p>Your One Stop Shop!</p>
        <br><br><br><br>
      </div>
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="text" name="email"class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($phone_number_err)) ? 'has-error' : ''; ?>">
                  <label>Phone Number</label>
                  <input type="tel" name="phone_number" class="form-control" value="<?php echo $phone_number; ?>" required>
                  <span class="help-block"><?php echo $phone_number_err; ?></span>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                      <label>First Name</label>
                      <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
                      <span class="help-block"><?php echo $first_name_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Middle Name</label>
                      <input type="text" name="middle_name" class="form-control" value="<?php echo $middle_name; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                  <label>Last Name</label>
                  <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" required>
                  <span class="help-block"><?php echo $last_name_err; ?></span>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                      <label>Street Number</label>
                      <input type="number" name="street_number" class="form-control" value="<?php echo $street_number; ?>" required>
                      <span class="help-block"><?php echo $street_number_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group <?php echo (!empty($street_name_err)) ? 'has-error' : ''; ?>">
                      <label>Street Name</label>
                      <input type="text" name="street_name" class="form-control" value="<?php echo $street_name; ?>" required>
                      <span class="help-block"><?php echo $street_name_err; ?></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                      <label>City</label>
                      <input type="text" name="city" class="form-control" value="<?php echo $city; ?>" required>
                      <span class="help-block"><?php echo $city_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group <?php echo (!empty($province_err)) ? 'has-error' : ''; ?>">
                      <label>Province</label>
                      <input type="text" name="province" class="form-control" value="<?php echo $province; ?>" required>
                      <span class="help-block"><?php echo $province_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group <?php echo (!empty($postalcode_err)) ? 'has-error' : ''; ?>">
                      <label>Postal Code</label>
                      <input type="text" name="postalcode" class="form-control" value="<?php echo $postalcode; ?>" required>
                      <span class="help-block"><?php echo $postalcode_err; ?></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group <?php echo (!empty($credit_card_number_err)) ? 'has-error' : ''; ?>">
                      <label>Credit Card Number</label>
                      <input type="number" name="credit_card_number" class="form-control" value="<?php echo $credit_card_number; ?>" min="1000000000" max="9999999999999999" required>
                      <span class="help-block"><?php echo $credit_card_number_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group <?php echo (!empty($credit_card_expiry_err)) ? 'has-error' : ''; ?>">
                      <label>Credit Card Expiry</label>
                      <input type="date" name="credit_card_expiry" class="form-control" value="<?php echo $credit_card_expiry; ?>" required>
                      <span class="help-block"><?php echo $credit_card_expiry_err; ?></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                      <label>Password</label>
                      <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" required>
                      <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                      <label>Confirm Password</label>
                      <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" required>
                      <span class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>
                <p>Already have an account? <a href="/login">Login here</a>.</p>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>

</html>
