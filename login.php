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
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = 'Please enter email.';
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, first_name, middle_name, last_name, email, password, role FROM app_user WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $user_id, $first_name, $middle_name, $last_name, $email, $hashed_password, $role);

                    if (mysqli_stmt_fetch($stmt)) {
                        if ($password === $hashed_password) {
                            /* Password is correct, so start a new session and
                            save the email to the session */
                            session_start();
                            $_SESSION['email'] = $email;
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['first_name'] = $first_name;
                            $_SESSION['middle_name'] = $middle_name;
                            $_SESSION['last_name'] = $last_name;
                            if ($role == 'admin') {
                              $_SESSION['role'] = $role;
                            }
                            header("location: /");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else {
                    // Display an error message if email doesn't exist
                    $email_err = 'No account found with that email.';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
        <br><br><br><br>
        <br><br><br><br>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h2>Login</h2>
              <p>Please fill in your credentials to login.</p>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                      <label>Email</label>
                      <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                      <span class="help-block"><?php echo $email_err; ?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                      <label>Password</label>
                      <input type="password" name="password" class="form-control">
                      <span class="help-block"><?php echo $password_err; ?></span>
                  </div>
                  <div class="form-group">
                      <input type="submit" class="btn btn-primary" value="Login">
                  </div>
                  <p>Don't have an account? <a href="/register">Sign up now</a>.</p>
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
