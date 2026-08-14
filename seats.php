<?php 
  session_start();
  include 'dbconnect.php';

  @ $db = new mysqli('localhost', 'root', '', 'lume');

  if (mysqli_connect_errno()) {
    echo "<p>Error: Could not connect to database. Please try again later.</p>";
    exit;
  }

  // Get showtime_id from URL
  $showtime_id = isset($_GET['showtime_id']) ? intval($_GET['showtime_id']) : 0;
  if (!$showtime_id) {
    echo "<p>Invalid showtime.</p>";
    exit;
  }

  // Fetch showtime info
  $showtime_query = "SELECT * FROM showtimes WHERE showtimeid = $showtime_id";
  $showtime_result = $db->query($showtime_query);
  if (!$showtime_result || $showtime_result->num_rows == 0) {
    echo "<p>Showtime not found.</p>";
    exit;
  }
  $showtime = $showtime_result->fetch_assoc();

  // Fetch movie info using movie_id from showtime
  $movie_id = intval($showtime['movie_id']);
  $movie_query = "SELECT * FROM movies WHERE moviesid = $movie_id";
  $movie_result = $db->query($movie_query);
  if (!$movie_result || $movie_result->num_rows == 0) {
    echo "<p>Movie not found.</p>";
    exit;
  }
  $movie = $movie_result->fetch_assoc();

  // Fetch hall info using hall_id from showtime
  $hall_id = intval($showtime['hall_id']);
  $hall_query = "SELECT * FROM halls WHERE hallsid = $hall_id";
  $hall_result = $db->query($hall_query);
  if (!$hall_result || $hall_result->num_rows == 0) {
    echo "<p>Hall not found.</p>";
    exit;
  }
  $hall = $hall_result->fetch_assoc();

  // Fetch all seats for this hall
  $hall_sid = intval($hall['hallsid']);
  $seats_query = "SELECT seatsid, row_label, seat_number FROM seats WHERE hall_id = $hall_sid ORDER BY row_label, seat_number";
  $seats_result = $db->query($seats_query);

  $allSeats = [];
  if ($seats_result) {
    while ($row = $seats_result->fetch_assoc()) {
      $allSeats[] = $row;
    }
  }

  // Fetch booked seats for this showtime
  $booked_query = "SELECT seat_id FROM showtime_seats WHERE showtime_id = $showtime_id AND is_booked = 1";
  $booked_result = $db->query($booked_query);

  $bookedSeats = [];
  if ($booked_result) {
    while ($row = $booked_result->fetch_assoc()) {
      $bookedSeats[] = $row['seat_id'];
    }
  }

  // Organize seats by row and mark booked ones
  $seats = [];
  foreach ($allSeats as $seat) {
    $isBooked = in_array($seat['seatsid'], $bookedSeats) ? 1 : 0;
    $seats[$seat['row_label']][] = [
      'seatsid' => $seat['seatsid'],
      'row_label' => $seat['row_label'],
      'seat_number' => $seat['seat_number'],
      'is_booked' => $isBooked
    ];
  }

  $db->close();
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Seat Selection</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  session_start();
  include 'header.php';
  ?>

  <main>
    <section>
      <div class="selection-overview">
        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="Poster" class="selection-poster">
        <div>
          <h3 class="movie-title"><?php echo htmlspecialchars($movie['movie_name']); ?></h3>
          <p class="selection-description">
            <span><?php echo htmlspecialchars($hall['hall_name']); ?></span>
            <span><?php echo date('D, d M', strtotime($showtime['show_date'])); ?></span>
            <span><?php echo date('g:i A', strtotime($showtime['show_time'])); ?></span>
          </p>
        </div>
      </div>
    </section>

    <section class="seat-selection">
      <div class="screen">Screen</div>
        <div class="seats-grid">
        <?php foreach ($seats as $row_label => $seatRow): ?>
        <div class="row" data-row="<?php echo htmlspecialchars($row_label); ?>">
          <?php foreach ($seatRow as $seat): 
            $class = 'seat';
            $disabled = '';
            if ($seat['is_booked']) {
                $class .= ' unavailable';
                $disabled = ' disabled';
            }
            $seatLabel = htmlspecialchars($seat['row_label'] . $seat['seat_number']);
          ?>
        
          <button 
              type="button"
              class="<?php echo $class; ?>" 
              data-seat-id="<?php echo $seat['seatsid']; ?>" 
              data-seat-label="<?php echo $seatLabel; ?>"
              <?php echo $disabled; ?>>
              <?php echo $seatLabel; ?>
            </button>
           <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
        </div>

        <div class="seat-legend">
            <h4>Legend</h4>
            <div class="legend-items">
                <div><span class="available"></span> Available</div>
                <div><span class="selected"></span> Selected</div>
                <div><span class="unavailable"></span> Unavailable</div>
            </div>
        </div>
    </section>

    <div class="seat-summary">
        <form action="booking_confirmation.php" method="POST" id="bookingForm">
            <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
            <input type="hidden" name="selected_seats" id="selected_seats">

            <h3>Booking Summary</h3>
            <table border="1">
                <tr>
                    <th>Selected Seats</th>
                    <th>Quantity</th>
                    <th>Total ($)</th>
                </tr>
                <tr>
                    <td><input type="text" name="seats" id="seats" readonly></td>
                    <td><input type="number" name="qty_seats" id="qty_seats" value="0" readonly></td>
                    <td><input type="text" name="total" id="total" value="0.00" readonly></td>
                </tr>
            </table>
            <input type="submit" value="Checkout" class="checkout-button">
        </form>

        <script>
        var isLoggedIn = <?php echo isset($_SESSION['member_id']) ? 'true' : 'false'; ?>;

        document.getElementById("bookingForm").addEventListener("submit", function(event) {
            if (!isLoggedIn) {
                alert("Please login to book tickets.");
                event.preventDefault();
                return false;
            }
        });
        </script>

    </div>
  </main>

  <script type="text/javascript" src="seats.js"></script>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>