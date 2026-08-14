<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Booking Confirmation</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  session_start();
  include 'header.php';
  $member_id = $_SESSION['member_id'];
  ?>

  <main>
  <?php
    @ $db = new mysqli('localhost', 'root', '', 'lume');
    if (mysqli_connect_errno()) {
        echo "<p>Error: Could not connect to database.</p>";
        exit;
    }

    $seatSelected = $_POST['seats'];
    $qtySeats = $_POST['qty_seats'];
    $totalAmt = $_POST['total'];
    $showtime_id = $_POST['showtime_id'];
    $selectedSeatIds = array();

    if (!empty($_POST['selected_seats'])) {
        $selectedSeatIds = array_map('intval', explode(',', $_POST['selected_seats']));
        }

    $sql = "SELECT st.*, m.movie_name, m.poster_path, h.hall_name
            FROM showtimes st, movies m, halls h
            WHERE st.movie_id = m.moviesid
            AND st.hall_id = h.hallsid
            AND st.showtimeid = $showtime_id";

    $result = $db->query($sql);
    if ($result && $result->num_rows > 0) {
        $show = $result->fetch_assoc();
    } else {
        echo "<p>Showtime not found.</p>";
        $db->close();
    exit;
    }

    if (!empty($selectedSeatIds)) {
        foreach ($selectedSeatIds as $seat_id) {
            $seat_id = intval($seat_id);
            $showtime_id = intval($showtime_id);

            // check if record alr exists
            $check_sql = "SELECT showtime_seatsid FROM showtime_seats WHERE showtime_id = $showtime_id AND seat_id = $seat_id";
            $check_result = $db->query($check_sql);

            if ($check_result && $check_result->num_rows > 0) {
            // if record exists update to booked
                $update_sql = "UPDATE showtime_seats 
                               SET is_booked = 1 
                               WHERE showtime_id = $showtime_id AND seat_id = $seat_id";
                if (!$db->query($update_sql)) {
                    echo "<p style='color:red;'>Error updating seat $seat_id: " . $db->error . "</p>";
                }
            } else {
                // sinsert new record if not exists
                $insert_sql = "INSERT INTO showtime_seats (showtime_id, seat_id, is_booked) 
                    VALUES ($showtime_id, $seat_id, 1)";
                if (!$db->query($insert_sql)) {
                    echo "<p style='color:red;'>Error inserting seat $seat_id: " . $db->error . "</p>";
                }
            }
            
            $booking_sql = "INSERT INTO bookings (members_id, showtime_id, seat_id) VALUES (?, ?, ?)";
            $stmt = $db->prepare($booking_sql);
            $stmt->bind_param("iii", $member_id, $showtime_id, $seat_id);
            $stmt->execute();
            $stmt->close();
        }
        // booked successful message
        echo "<div class='booking-header'>";
        echo "<h1>Booking Confirmation</h1>";
        echo "<p style='color:green; font-weight:bold;'>Your seats have been successfully booked!</p>";
        echo "</div>";

        // show booking details
        echo "<div class='booking-details'>";
        echo "<img src='" . htmlspecialchars($show['poster_path']) . "' alt='Poster' class='booking-poster'>";
        echo "<div>";
        echo "<h3 class='movie-title'>" . htmlspecialchars($show['movie_name']) . "</h3>";
        echo "<p class='booking-description'>";
        echo "<span><strong>" . htmlspecialchars($show['hall_name']) . "</strong></span>";
        echo "<span><strong>Date: " . date('D, d M Y', strtotime($show['show_date'])) . "</strong></span>";
        echo "<span><strong>Time: " . date('g:i A', strtotime($show['show_time'])) . "</strong></span>";
        echo "<span><strong>Seats: </strong>" . htmlspecialchars($seatSelected) . "</span>";
        echo "<span><strong>Quantity: </strong>" . htmlspecialchars($qtySeats) . "</span>";
        echo "<span><strong>Total: $</strong>" . htmlspecialchars($totalAmt) . "</span>";
        echo "</p>";
        echo "</div>";
        echo "</div>";
    } else {
        // No seats selected and booking unsuccessful
        echo "<div class='booking-header'>";
        echo "<h1>Booking Unsuccessful</h1>";
        echo "<p style='color:red; font-weight:bold;'>No seats were selected.</p>";
        echo "</div>";
    }

    $db->close();
  ?>
  <a href="home.php">
    <button class="return-btn">Return to Home</button>
  </a>
  </main>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>

