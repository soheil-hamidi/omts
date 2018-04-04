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
    header("location: /login");
    exit;
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM app_user WHERE user_id = $id";
    if (mysqli_query($link, $sql)) {
        header("location: /dashboard");
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}

$show_modal = false;
$history_flag = false;

if (isset($_GET['history'])) {
    $user_id = $_GET['history'];
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
        $show_modal = true;
        $history_flag = true;
    } else {
        header("location: /dashboard");
    }
}

$sql = "SELECT * FROM app_user WHERE role != 'admin'";
$results = mysqli_query($link, $sql);

$sql_theatre = "SELECT * FROM theatre";
$theatre_results = mysqli_fetch_all(mysqli_query($link, $sql_theatre), MYSQLI_ASSOC);

$sql_screening = "SELECT
    screening.*,
    movie.title,
    theatre.theatre_name
    FROM
        screening
    JOIN movie ON movie.movie_id = screening.movie_id
    JOIN theatre ON theatre.theatre_id = screening.theatre_id ORDER BY movie_id";
$screening_results = mysqli_query($link, $sql_screening);

$sql_movie_supplier = "SELECT * FROM movie_supplier";
$movie_supplier = mysqli_query($link, $sql_movie_supplier);

$sql_movie = "SELECT * FROM movie";
$movie_results = mysqli_fetch_all(mysqli_query($link, $sql_movie), MYSQLI_ASSOC);

$sql_production_company = "SELECT * FROM production_company";
$production_company = mysqli_query($link, $sql_production_company);

$sql_director = "SELECT * FROM director";
$director = mysqli_query($link, $sql_director);

$sql_actor = "SELECT * FROM actor";
$actor = mysqli_query($link, $sql_actor);

$popular_sql = "SELECT
    temp.theatre_name,
    movie.title,
    SUM(temp.number_of_tickets) AS total_number_of_tickets_sold
    FROM
        movie,
        (
        SELECT
            theatre.theatre_name,
            screening.movie_id,
            ticket.number_of_tickets
        FROM
            ticket
        JOIN screening ON screening.theatre_id = ticket.theatre_id AND screening.auditorium_number = ticket.auditorium_number AND screening.date_time = ticket.date_time
        JOIN theatre ON theatre.theatre_id = ticket.theatre_id
    ) AS temp
    WHERE
        temp.movie_id = movie.movie_id
    GROUP BY
        movie.title
    ORDER BY
        total_number_of_tickets_sold
    DESC
    LIMIT 1";

$popular = mysqli_query($link, $popular_sql);
$most_popular_theatre = "";
$most_popular_movie = "";

if (mysqli_num_rows($popular)) {
  $popular_row = mysqli_fetch_row($popular);
  $most_popular_theatre = $popular_row[0];
  $most_popular_movie = $popular_row[1];
}

// Close connection
mysqli_close($link);

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>OMTS</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
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
                <li class="active"><a href="/dashboard">Admin Dashboard</a></li>
            <?php } ?>
            <li><a href="/">Home</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><a href="/logout" style="color: red;">Sign Out</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container">
  <div class="row">
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Statistics</strong></div>
        <div class="panel-body">
          <p>Most Popular Movie:</p>
          <p><strong style="color: green;"><?php echo $most_popular_movie; ?></strong></p>
          <p>Most Popular Theatre:</p>
          <p><strong style="color: green;"><?php echo $most_popular_theatre; ?></strong></p>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="panel-group" id="accordion">
        <div class="panel panel-info">
          <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            <h4 class="panel-title">
              <a>Members</a>
            </h4>
          </div>
          <div id="collapse1" class="panel-collapse collapse">
            <div class="panel-body no-padding panel-body-height">
              <table class="table table-striped no-margin">
                <thead>
                    <tr>
                      <th>Firstname</th>
                      <th>Lastname</th>
                      <th>Email</th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                <?php while ($row = mysqli_fetch_array($results)) {
        ?>
                		<tr>
                			<td><?php echo $row['first_name']; ?></td>
                			<td><?php echo $row['last_name']; ?></td>
                      <td><?php echo $row['email']; ?></td>
                      <td>
                        <a href="?history=<?php echo $row['user_id']; ?>">Purchase History</a>
                      </td>
                			<td>
                				<a href="?del=<?php echo $row['user_id']; ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                			</td>
                		</tr>

                	<?php
    } ?>
            </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="panel panel-success">
          <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            <h4 class="panel-title">
              <a>Theatres</a>
            </h4>
          </div>
          <div id="collapse2" class="panel-collapse collapse">
            <div class="panel-body no-padding panel-body-height">
              <div class="row no-margin">
                <div class="col-md-12">
                  <br>
                  <form action="/add-theatre.php" method="post">
                    <div class="row no-margin text-left">
                      <div class="col-md-2 no-padding-right">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="theatre_name" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                            <label>Phone #</label>
                            <input type="tell" name="phone_number" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-1 no-padding">
                        <div class="form-group">
                            <label>ST #</label>
                            <input type="number" name="street_number" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                            <label>ST Name</label>
                            <input type="text" name="street_name" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-1 no-padding">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-1 no-padding-right">
                        <div class="form-group">
                            <label>Prov.</label>
                            <input type="text" name="province" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Postal Code</label>
                          <input type="text" name="postalcode" class="form-control" value="" required>
                        </div>
                        <input type="hidden" name="user_id" class="form-control" value="<?php echo $_SESSION['user_id']; ?>">
                        <input type="hidden" name="movie_id" class="form-control" value="">
                      </div>
                      <div class="col-md-1 no-padding">
                        <div class="form-group">
                          <label style="color: white;">---</label>
                          <input type="submit" class="btn btn-success" value="Add">
                        </div>
                      </div>
                    </div>
                  </form>

                </div>
              </div>
              <br>
              <hr class="no-margin">
              <table class="table table-striped no-margin">
                <thead>
                    <tr>
                      <th>Name</th>
                      <th>Phone #</th>
                      <th>ST #</th>
                      <th>ST Name</th>
                      <th>City</th>
                      <th>Prov.</th>
                      <th>Postal Code</th>
                      <th></th>
                    </tr>
                  </thead>
                    <tbody>
                <?php foreach ($theatre_results as $row) {
        ?>          <form action="/update-theatre.php" method="post">
                      <input type="hidden" name="theatre_id" class="form-control" value="<?php echo $row['theatre_id']; ?>" required>
                  		<tr>
                  			<td><input type="text" name="theatre_name_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['theatre_name']; ?>" required></td>
                  			<td><input type="tel" name="phone_number_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['phone_number']; ?>" required></td>
                        <td><input type="number" name="street_number_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['street_number']; ?>" required></td>
                        <td><input type="text" name="street_name_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['street_name']; ?>" required></td>
                        <td><input type="text" name="city_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['city']; ?>" required></td>
                        <td><input type="text" name="province_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['province']; ?>" required></td>
                        <td><input type="text" name="postalcode_<?php echo $row['theatre_id']; ?>" class="form-control" value="<?php echo $row['postalcode']; ?>" required></td>
                        <td><input type="submit" class="btn btn-info" value="Update"></td>
                  		</tr>
                    </form>
                	<?php
    } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="panel panel-warning">
          <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
            <h4 class="panel-title">
              <a>Movies</a>
            </h4>
          </div>
          <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body no-padding">
              <div class="panel panel-default" style="border:none; box-shadow: none;">
                <div class="panel-body">

                  <form action="/add-movie.php" method="post">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Title</label>
                          <input type="text" name="title" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Duration</label>
                          <input type="number" name="duration_minutes" min="3" max="400" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Age Rating</label>
                          <select class="form-control" name="age_rating">
                              <option value="PG">PG</option>
                              <option value="G">G</option>
                              <option value="PG-13">PG-13</option>
                              <option value="R">R</option>
                              <option value="NC-17">NC-17</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Poster URL</label>
                          <input type="text" name="poster_url" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Start Date</label>
                          <input type="date" name="start_date" class="form-control" value="" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>End Date</label>
                          <input type="date" name="end_date" class="form-control" value="" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Plot</label>
                          <textarea type="text" name="plot" class="form-control" rows="3" value="" required></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Actors</label>
                          <select class="form-control selectpicker" data-live-search="true" data-size="5" name="actor_ids[]" multiple>
                            <?php while ($row = mysqli_fetch_array($actor)) { ?>
                            <option value="<?php echo $row['actor_id']; ?>"><?php echo $row['first_name']; ?> <?php echo $row['middle_name']; ?> <?php echo $row['last_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Director</label>
                          <select class="form-control selectpicker" data-size="5" data-live-search="true" name="director_id">
                            <?php while ($row = mysqli_fetch_array($director)) { ?>
                            <option value="<?php echo $row['director_id']; ?>"><?php echo $row['first_name']; ?> <?php echo $row['middle_name']; ?> <?php echo $row['last_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Production Company</label>
                          <select class="form-control selectpicker" data-size="5" data-live-search="true" name="production_company_id">
                            <?php while ($row = mysqli_fetch_array($production_company)) { ?>
                            <option value="<?php echo $row['production_company_id']; ?>"><?php echo $row['production_company_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Movie Supplier Company</label>
                          <select class="form-control selectpicker" data-size="5" data-live-search="true" name="movie_supplier_id">
                            <?php while ($row = mysqli_fetch_array($movie_supplier)) { ?>
                            <option value="<?php echo $row['movie_supplier_id']; ?>"><?php echo $row['company_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label style="color: white;">-</label>
                          <input type="submit" class="btn btn-success btn-block" value="Add">
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-danger">
          <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
            <h4 class="panel-title">
              <a>Shows</a>
            </h4>
          </div>
          <div id="collapse4" class="panel-collapse collapse">
            <div class="panel-body no-padding panel-body-height">
              <table class="table table-striped no-margin">
                <thead>
                    <tr>
                      <th>Theatre Name</th>
                      <th>Aud #</th>
                      <th>Movie</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th></th>
                    </tr>
                  </thead>
                    <tbody>
                <?php while ($row = mysqli_fetch_array($screening_results)) {
        ?>          <form action="/update-screening.php" method="post">
                      <input type="hidden" name="screening_id" value="<?php echo $row['screening_id']; ?>">
                  		<tr>
                  			<td>
                          <div class="form-group">
                            <select class="form-control" data-size="5" data-live-search="true" name="theatre_id_<?php echo $row['screening_id']; ?>">
                              <?php foreach ($theatre_results as $theatre_row) { ?>
                              <option value="<?php echo $theatre_row['theatre_id']; ?>" <?php echo $theatre_row['theatre_id'] == $row['theatre_id'] ? 'selected' : ''; ?>><?php echo $theatre_row['theatre_name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </td>
                  			<td><input type="number" name="auditorium_number_<?php echo $row['screening_id']; ?>" class="form-control" value="<?php echo $row['auditorium_number']; ?>" min="1" max="10" required></td>
                        <td>
                          <div class="form-group">
                            <select class="form-control" data-size="5" data-live-search="true" name="movie_id_<?php echo $row['screening_id']; ?>">
                              <?php foreach ($movie_results as $movie_row) { ?>
                              <option value="<?php echo $movie_row['movie_id']; ?>" <?php echo $movie_row['movie_id'] == $row['movie_id'] ? 'selected' : ''; ?>><?php echo $movie_row['title']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </td>
                        <td><input type="date" name="date_<?php echo $row['screening_id']; ?>" class="form-control" value="<?php echo date("Y-m-d", strtotime($row['date_time'])); ?>" required></td>
                        <td><input type="time" name="time_<?php echo $row['screening_id']; ?>" class="form-control" value="<?php echo date("H:i", strtotime($row['date_time'])); ?>" required></td>
                        <td><input type="submit" class="btn btn-info" value="Update"></td>
                  		</tr>
                    </form>
                	<?php
    } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="mainModalLabel">Purchase History</h4>
      </div>
      <div class="modal-body">
        <table class="table no-margin" <?php echo (!$history_flag) ? 'hidden' : ''; ?>>
          <thead>
              <tr>
                <th>Theatre Name</th>
                <th>Movie Title</th>
                <th>Number of Tickets</th>
                <th>Purchase Date</th>
              </tr>
            </thead>
            <tbody>
          <?php while ($row = mysqli_fetch_array($tickets)) {
        ?>

                    <tr>
                      <td><?php echo $row['theatre_name']; ?></td>
                      <td><?php echo $row['title']; ?></td>
                      <td><?php echo $row['number_of_tickets']; ?></td>
                      <td><?php echo date("M j, o", strtotime($row['purchase_date_time'])); ?></td>
                    </tr>

          <?php
    }?>
        </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<script src="/js/script.js"></script>
<?php if ($show_modal) {
        ?>
  <script>
    $('#mainModal').modal('show');
  </script>
<?php
    } ?>
</body>

</html>
