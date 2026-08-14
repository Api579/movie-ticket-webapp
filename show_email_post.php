<?php
session_start();
include 'dbconnect.php';

$member_id = (int)$_SESSION['member_id'];

$sql = "
    SELECT 
        b.booking_time, 
        st.show_date, 
        st.show_time, 
        m.movie_name, 
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
    die("Error: " . mysqli_error($dbconnect));}
?>


<html>
<head>
    <title>Email Post</title>
</head>
<body>
    <h2>Email Content</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Movie</th>
                <th>Hall</th>
                <th>Date</th>
                <th>Time</th>
                <th>Seats</th>
                <th>Booked On</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['hall_name']); ?></td>
                    <td><?php echo date('D, d M Y', strtotime($row['show_date'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($row['show_time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['seats']); ?></td>
                    <td><?php echo date('d M Y, g:i A', strtotime($row['booking_time'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No bookings to show.</p>
    <?php endif; ?>

    <p><a href="cart.php">Back</a></p>
</body>
</html>
<?php
$dbconnect->close();
?>
