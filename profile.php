<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /login");
    exit;
}

require_once 'config.php';

$purchase_flag = false;
$user_id = $_SESSION['user_id'];

if (isset($_GET['show'])) {
    $sql = "SELECT
    temp.ticket_id,
    temp.theatre_name,
    movie.title,
    temp.number_of_tickets,
    temp.purchase_date_time
    FROM
        movie,
        (
        SELECT
            theatre.theatre_name,
            screening.movie_id,
            ticket.number_of_tickets,
            ticket.purchase_date_time,
            ticket.ticket_id
        FROM
            ticket
        JOIN screening ON screening.theatre_id = ticket.theatre_id
        AND screening.auditorium_number = ticket.auditorium_number
        AND screening.date_time = ticket.date_time
        JOIN theatre ON theatre.theatre_id = ticket.theatre_id
        WHERE
            ticket.user_id = $user_id
    ) AS temp
    WHERE
    temp.movie_id = movie.movie_id";
    $tickets = mysqli_query($link, $sql);
    if (mysqli_num_rows($tickets)) {
        $purchase_flag = true;
    } else {
        header("location: /profile");
    }
}

if (isset($_GET['del'])) {
    $ticket_id = $_GET['del'];
    $sql = "DELETE FROM ticket WHERE ticket_id = $ticket_id";
    if (mysqli_query($link, $sql)) {
        header("location: /profile?show=tickets");
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}


// Define variables and initialize with empty values

$sql = "SELECT * FROM app_user WHERE user_id = $user_id";
$curr_user = mysqli_fetch_assoc(mysqli_query($link, $sql));

$email = $curr_user['email'];
$phone_number = $curr_user['phone_number'];
$first_name = $curr_user['first_name'];
$middle_name = $curr_user['middle_name'];
$last_name = $curr_user['last_name'];
$street_number = $curr_user['street_number'];
$street_name = $curr_user['street_name'];
$city = $curr_user['city'];
$province = $curr_user['province'];
$postalcode = $curr_user['postalcode'];
$credit_card_number = $curr_user['credit_card_number'];
$credit_card_expiry = $curr_user['credit_card_expiry'];
$password = $confirm_password = $curr_user['password'];
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

                mysqli_stmt_bind_result($stmt, $param_user_id);
                mysqli_stmt_fetch($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1 && $param_user_id != $user_id) {
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
        if ($middle_name) {
            $sql = "UPDATE app_user SET
          email = '$email', phone_number = '$phone_number', first_name = '$first_name',
          middle_name = '$middle_name', last_name = '$last_name',
          street_number = '$street_number', street_name = '$street_name', city = '$city',
          province = '$province', postalcode = '$postalcode', password = '$password',
          credit_card_number = '$credit_card_number', credit_card_expiry = '$credit_card_expiry'
          WHERE user_id = $user_id";
        } else {
            $sql = "UPDATE app_user SET
          email = '$email', phone_number = '$phone_number', first_name = '$first_name', last_name = '$last_name',
          street_number = '$street_number', street_name = '$street_name', city = '$city',
          province = '$province', postalcode = '$postalcode', password = '$password',
          credit_card_number = '$credit_card_number', credit_card_expiry = '$credit_card_expiry'
          WHERE user_id = $user_id";
        }

        if (mysqli_query($link, $sql)) {
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['middle_name'] = $middle_name;
            $_SESSION['last_name'] = $last_name;
            // Close connection
            mysqli_close($link);
            header("location: /profile");
        } else {
            echo "Error updating record: " . mysqli_error($link);
        }
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
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand no-padding" href="/" style="padding-top: 5px; padding-left: 50px;"><img src="/img/logo-nav.png" alt="logo" height="40px"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <?php if (isset($_SESSION['role']) || !empty($_SESSION['role'])) { ?>
                <li><a href="/dashboard">Admin Dashboard</a></li>
            <?php } ?>
            <li><a href="/">Home</a></li>
            <li class="active"><a href="/profile">Profile</a></li>
            <li><a href="/logout" style="color: red;">Sign Out</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <div class="panel panel-default">
            <div class="panel-heading"><strong><?php echo $_SESSION['first_name'], ' ', $_SESSION['middle_name'], ' ',$_SESSION['last_name']; ?></strong></div>
            <div class="panel-body">
              <p><a href="/profile">My Profile</a></p>
              <a href="?show=tickets">Purchases</a>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="panel panel-default">
            <div class="panel-body">
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" <?php echo $purchase_flag ? 'hidden' : ''; ?>>
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
                      <input type="submit" class="btn btn-success" value="Update">
                  </div>
              </form>

              <table class="table no-margin" <?php echo (!$purchase_flag) ? 'hidden' : ''; ?>>
                <thead>
                    <tr>
                      <th>Theatre Name</th>
                      <th>Movie Title</th>
                      <th>Number of Tickets</th>
                      <th>Purchase Date</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                <?php if ($purchase_flag) {
    while ($row = mysqli_fetch_array($tickets)) {
        ?>

                          <tr>
                            <td><?php echo $row['theatre_name']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['number_of_tickets']; ?></td>
                            <td><?php echo date("M j, o", strtotime($row['purchase_date_time'])); ?></td>
                            <td>
                              <a href="?del=<?php echo $row['ticket_id']; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            </td>
                          </tr>

                <?php
    }
}?>
              </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/script.js"></script>

</body>

</html>
