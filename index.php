<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /login");
    exit;
}

$show_modal = false;
$show_snackbar = false;

if (isset($_GET['info'])) {
    $show_modal = false;
    $id = $_GET['info'];
    $_SESSION['movie_title'] = $_GET['name'];
    $sql = "SELECT * FROM screening
    JOIN movie ON screening.movie_id = movie.movie_id
    JOIN theatre ON screening.theatre_id = theatre.theatre_id
    JOIN auditorium ON auditorium.theatre_id = screening.theatre_id AND auditorium.auditorium_number = screening.auditorium_number
    WHERE screening.movie_id = $id";
    $screening = mysqli_query($link, $sql);
    if (mysqli_num_rows($screening)) {
        $show_modal = true;
    } else {
        header("location: /");
    }
}

$review_modal = false;
$user_id = $_SESSION['user_id'];
if (isset($_GET['reviews'])) {
    $review_modal = true;
    $movie_id = $_GET['reviews'];
    $already_reviewed = false;
    if (mysqli_num_rows(mysqli_query($link, "SELECT user_id FROM movie_review WHERE user_id = $user_id AND movie_id = $movie_id"))) {
      $already_reviewed = true;
    }
    $_SESSION['movie_title'] = $_GET['name'];
    $sql = "SELECT * FROM movie_review
    JOIN app_user ON movie_review.user_id = app_user.user_id
    WHERE movie_review.movie_id = $movie_id";
    $reviews = mysqli_query($link, $sql);
    $show_modal = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Prepare an insert statement
    $sql = "INSERT INTO ticket
  (user_id, theatre_id, auditorium_number, date_time, number_of_tickets, purchase_date_time)
  VALUES
  (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        $today = date('Y-m-d H:i:s');
        mysqli_stmt_bind_param($stmt, "ssssss", $param_user_id, $param_theatre_id, $param_auditorium_number, $param_date_time, $param_number_of_tickets, $today);

        // Set parameters
        $param_number_of_tickets = trim($_POST["number_of_tickets"]);
        $param_user_id = $_POST["user_id"];
        $param_theatre_id = $_POST["theatre_id"];
        $param_auditorium_number = $_POST["auditorium_number"];
        $param_date_time = $_POST["date_time"];

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to login page
            $show_snackbar = true;
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

$sql = "SELECT
    movie.*,
    temp.rating_avg
    FROM
        movie
    LEFT JOIN(
        SELECT
            movie_id,
            AVG(rating) AS rating_avg
        FROM
            movie_review
        GROUP BY
            movie_id
    ) AS temp
    ON
    temp.movie_id = movie.movie_id";

$results = mysqli_query($link, $sql);

// Close connection
mysqli_close($link);

function convertToHoursMins($time, $format = '%01d h %02d min') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
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
            <li class="active"><a href="/">Home</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><a href="/logout" style="color: red;">Sign Out</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
        <?php while ($row = mysqli_fetch_array($results)) {
    ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-2">
                  <img src="<?php echo $row['poster_url']; ?>" alt="<?php echo $row['title']; ?>" width="100%">
                </div>
                <div class="col-xs-8">
                  <div class="row">
                    <div class="col-xs-8">
                      <h3><?php echo $row['title']; ?></h3>
                    </div>
                    <div class="col-xs-4 text-right">
                      <p class="text-muted" style="margin-top: 20px; margin-bottom: 10px;"><?php echo convertToHoursMins($row['duration_minutes']); ?> | <img src="/img/<?php echo $row['age_rating']; ?>.png" alt="<?php echo $row['age_rating']; ?>" height="12"></p>
                    </div>
                  </div>
                  <hr class="costum-hr no-margin-top">
                  <p><?php echo $row['plot']; ?></p>
                </div>
                <div class="col-xs-2 text-center">
                  <div class="panel panel-default no-margin" style="background-color: #F5F5F5;">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <br>
                          <p><a href="?reviews=<?php echo $row['movie_id']; ?>&name=<?php echo $row['title']; ?>">Reviews</a></p>
                          <p><span class="glyphicon glyphicon-star" aria-hidden="true" style="color: #FBC02D; font-size: 40px;"></span></p>
                          <h3 class="no-margin-top text-muted"><?php echo $row['rating_avg'] ? round($row['rating_avg'],1) : '-'; ?>/5</h3>
                        </div>
                      </div>
                      <br>
                      <div class="row">
                        <div class="col-xs-12">
                          <a class="btn btn-success btn-block" href="?info=<?php echo $row['movie_id']; ?>&name=<?php echo $row['title']; ?>">Tickets</a>
                        </div>
                      </div>
                    </div>
                  </div>


                </div>
              </div>
            </div>
          </div>
          <?php
} ?>
    </div>


<!-- Modal -->
<div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-labelledby="movieModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="movieModalLabel"><?php echo $_SESSION['movie_title']; ?></h4>
      </div>
      <div class="modal-body">
        <div class="max-height" <?php echo (!$review_modal) ? 'hidden' : ''; ?>>
          <table class="table no-margin" <?php echo !mysqli_num_rows($reviews) ? 'hidden' : ''; ?>>
            <thead>
                <tr>
                  <th>User</th>
                  <th>Comments</th>
                  <th>Rating</th>
                </tr>
              </thead>
              <tbody>
                <?php if($review_modal) { while ($row = mysqli_fetch_array($reviews)) {
              ?>
                <tr>
                  <td><?php echo $row['first_name']; ?></td>
                  <td><?php echo $row['comments']; ?></td>
                  <td class="text-center"><?php echo $row['rating']; ?> <span class="glyphicon glyphicon-star" aria-hidden="true" style="color: #FBC02D;"></span></td>
                </tr>
              <?php } } ?>
            </tbody>
          </table>
        </div>
        <div <?php echo ($review_modal) ? 'hidden' : ''; ?>>
          <table class="table no-margin">
            <thead>
                <tr>
                  <th>Theatre</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Number of Tickets</th>
                </tr>
              </thead>
              <tbody>
            <?php if(!$review_modal) { while ($row = mysqli_fetch_array($screening)) {
          ?>

                <tr>
                  <td><?php echo $row['theatre_name']; ?></td>
                  <td><?php echo date("M j, o", strtotime($row['date_time'])); ?></td>
                  <td><?php echo date("g:i A", strtotime($row['date_time'])); ?></td>
                  <td>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" name="number_of_tickets" class="form-control" value="0" min="0" max="10">
                          <input type="hidden" name="user_id" class="form-control" value="<?php echo $_SESSION['user_id']; ?>">
                          <input type="hidden" name="theatre_id" class="form-control" value="<?php echo $row['theatre_id']; ?>">
                          <input type="hidden" name="auditorium_number" class="form-control" value="<?php echo $row['auditorium_number']; ?>">
                          <input type="hidden" name="date_time" class="form-control" value="<?php echo $row['date_time']; ?>">
                        </div>
                        <div class="col-md-6">
                          <input type="submit" class="btn btn-primary" value="Buy">
                        </div>
                      </div>
                    </form>
                  </td>
                </tr>

              <?php
      } } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" <?php echo (!$review_modal || $already_reviewed) ? 'hidden' : ''; ?>>
        <div class="row">
          <div class="col-md-12">
            <form action="/review.php" method="post">
              <div class="row  text-left">
                <div class="col-md-8">
                  <div class="form-group">
                      <label>Your Review</label>
                      <input type="text" name="comments" class="form-control" value="" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Rating</label>
                    <input type="number" name="rating" class="form-control" value="0" min="0" max="5" required>
                  </div>
                  <input type="hidden" name="user_id" class="form-control" value="<?php echo $_SESSION['user_id']; ?>">
                  <input type="hidden" name="movie_id" class="form-control" value="<?php echo $movie_id ?>">
                </div>
                <div class="col-md-2 text-center">
                  <div class="form-group">
                    <label style="color: white;">-</label>
                    <input type="submit" class="btn btn-primary" value="Submit">
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="alert alert-success" id="snackbar">
  <strong>Success!</strong> Your purchase was successful.
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/script.js"></script>

<?php if ($show_modal) {
        ?>
  <script>
    $('#movieModal').modal('show');
  </script>
<?php
    } ?>


<?php if ($show_snackbar) {
        ?>
  <script>
    snackbar();
  </script>
<?php
    } ?>

</body>

</html>
