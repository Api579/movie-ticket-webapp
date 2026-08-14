<?php 
  @ $db = new mysqli('localhost', 'root', '', 'lume');
  if (mysqli_connect_errno()) {
    echo "<p>Error: Could not connect to database. Please try again later.</p>";
    exit;
  }

  // Get movie ID from URL
  $movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  if ($movie_id == 0) {
    echo "<p>Invalid movie ID.</p>";
    exit;
  }

  // fetch movie info
  $movie_query = "SELECT * FROM movies WHERE moviesid = $movie_id";
  $result = $db->query($movie_query);

  if ($result && $result->num_rows > 0) {
    $movie = $result->fetch_assoc();
  } else {
    echo "<p>Movie not found.</p>";
    $db->close();
    exit;
  }

  // fetch showtimes for movie, ordered by date and time
  $showtimes_query = "SELECT * FROM showtimes WHERE movie_id = $movie_id ORDER BY show_date, show_time";
  $showtimes_result = $db->query($showtimes_query);

  // Organize showtimes by date
  $showtimes_by_date = [];
  if ($showtimes_result) {
    while ($row = $showtimes_result->fetch_assoc()) {
      $date = $row['show_date'];
      $showtimes_by_date[$date][] = $row;
    }
  }

  // Get selected date from URL, if any
  $selected_date = isset($_GET['date']) ? $_GET['date'] : null;

  // Default to first date, if none for selected date
  if (empty($selected_date) && !empty($showtimes_by_date)) {
    $selected_date = array_key_first($showtimes_by_date);
  }

  $db->close();
?>


<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>ticket details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  session_start();
  include 'header.php';
  ?>

  <main>
    <section class="tickets-movie">
        <div class="tickets-overview">
          <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="Movie Poster" class="tickets-poster">
          <div class="tickets-details">
          <h2 class="tickets-title"><?php echo htmlspecialchars($movie['movie_name']); ?></h2>
            <div class="tickets-info">
              <span>Genre: <?php echo htmlspecialchars($movie['genre']); ?></span>
              <span>Runtime: <?php echo htmlspecialchars($movie['runtime']); ?></span>
              <span>Rating: <?php echo htmlspecialchars($movie['rating']); ?></span>
            </div>
          </div>
        </div>

        <section class="tickets-description">
            <div class="description-left">
              <table>
                  <tr>
                      <th>SYNOPSIS</th>
                      <td><?php echo htmlspecialchars($movie['synopsis']); ?></td>
                  </tr>
                  <tr>
                      <th>MAIN CAST</th>
                      <td><?php echo htmlspecialchars($movie['main_cast']); ?></td>
                  </tr>
              </table>
            </div>
            <div class="description-right">
              <table>
                  <tr>
                      <th>LANGUAGE</th>
                      <td><?php echo htmlspecialchars($movie['language']); ?></td>
                  </tr>
                  <tr>
                      <th>RELEASE DATE</th>
                      <td><?php echo htmlspecialchars($movie['release_date']); ?></td>
                  </tr>
              </table>
            </div>
        </section>
    </section>

    <section class="tickets-selection">
      <?php if (!empty($showtimes_by_date)): ?>
        <nav>
          <ul class="date-options">
            <?php foreach ($showtimes_by_date as $date => $showtimes) : ?>
              <li>
                <a href="#" 
                  data-date="<?php echo htmlspecialchars($date); ?>"
                  class="<?php echo $selected_date == $date ? 'active' : ''; ?>">
                  <?php echo date('D, d M', strtotime($date)); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>

        <h4>Timings</h4>
        <div class="time-options-container">
          <?php foreach ($showtimes_by_date as $date => $showtimes): ?>
            <div class="time-options" data-date="<?php echo htmlspecialchars($date); ?>" style="<?php echo $selected_date == $date ? '' : 'display:none;'; ?>">
              <?php foreach ($showtimes as $show): 
                $showtime_id = $show['showtimeid'];
                $show_time_formatted = date('g:i A', strtotime($show['show_time']));
              ?>
                <a href="seats.php?showtime_id=<?php echo $showtime_id; ?>" class="time-btn"><?php echo $show_time_formatted; ?></a>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <p>No showtimes available for this movie yet.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer>&copy; 2025 Lume</footer>
  <script src="tickets.js"></script>
</body>
</html>

