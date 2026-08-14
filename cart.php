<?php
session_start();
include 'header.php';
include 'dbconnect.php';

if (!isset($_SESSION['member_id'])) {
    echo "<script>alert('Please log in to view your bookings.'); window.location.href='login.php';</script>";
    exit;
}

$member_id = (int)$_SESSION['member_id'];

$sql = "
    SELECT 
        b.bookingid,
        b.booking_time, 
        st.show_date, 
        st.show_time, 
        m.movie_name, 
        m.poster_path, 
        h.hall_name,
        GROUP_CONCAT(CONCAT(s.row_label, s.seat_number) ORDER BY s.row_label, s.seat_number SEPARATOR ', ') AS seats
    FROM bookings b
    JOIN showtimes st ON b.showtime_id = st.showtimeid
    JOIN movies m ON st.movie_id = m.moviesid
    JOIN halls h ON st.hall_id = h.hallsid
    JOIN seats s ON b.seat_id = s.seatsid
    WHERE b.members_id = $member_id
    GROUP BY b.booking_time, b.showtime_id, b.members_id
    ORDER BY b.booking_time DESC
";

$result = mysqli_query($dbconnect, $sql);

if (!$result) {
    die("Error fetching bookings: " . mysqli_error($dbconnect));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bookings</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <main>
    <div class="booking-header">
      <h1>My Bookings</h1>
      <?php if ($result->num_rows > 0): ?>

        <form method="post" action="show_email_post.php" style="margin-top:10px;">
          <button type="submit" name="preview_email">send email</button>
        </form>
      <?php endif; ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="booking-list">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="booking-card">
            <img src="<?php echo htmlspecialchars($row['poster_path']); ?>" alt="Poster" class="mybooking-poster">
            <div>
              <h3><?php echo htmlspecialchars($row['movie_name']); ?></h3>
              <div class="mybooking-details">
                <p>
                  <strong>Hall:</strong> <?php echo htmlspecialchars($row['hall_name']); ?><br>
                  <strong>Date:</strong> <?php echo date('D, d M Y', strtotime($row['show_date'])); ?><br>
                  <strong>Time:</strong> <?php echo date('g:i A', strtotime($row['show_time'])); ?><br>
                  <strong>Seat:</strong> <?php echo htmlspecialchars($row['seats']); ?><br>
                  <small style="display: block; margin-top: 10px;">
                    Booked on <?php echo date('d M Y, g:i A', strtotime($row['booking_time'])); ?>
                  </small>
                </p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="booking-list">
        <p>No bookings yet.</p>
      </div>
    <?php endif; ?>
  </main>
  <footer>&copy; 2025 Lume</footer>
</body>
</html>
<?php
$dbconnect->close();
?>