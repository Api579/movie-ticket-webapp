<?php

session_start();

$db = new mysqli('localhost', 'root', '', 'lume');

$allowed = ['now_showing', 'coming_soon'];

$category = $_GET['category'] ?? 'now_showing';
if (!in_array($category, $allowed, true)) {
  $category = 'now_showing';
}

$sql = "
  SELECT moviesid, movie_name, synopsis, poster_path, parsed_date
  FROM (
    SELECT m.moviesid, m.movie_name, m.synopsis, m.poster_path,

      CASE
        WHEN m.release_date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
          THEN STR_TO_DATE(m.release_date, '%Y-%m-%d')
        ELSE STR_TO_DATE(REPLACE(m.release_date, 'Sept', 'Sep'), '%a, %e %b %Y')
      END AS parsed_date

    FROM movies AS m

  ) AS m_parsed
  WHERE
  
    (CASE
       WHEN m_parsed.parsed_date IS NOT NULL AND m_parsed.parsed_date <= CURDATE() THEN 'now_showing'
       ELSE 'coming_soon'
     END) = ?

  ORDER BY m_parsed.parsed_date IS NULL, m_parsed.parsed_date ASC, m_parsed.movie_name ASC
";

$statement = $db->prepare($sql);
$statement->bind_param('s', $category);
$statement->execute();
$result = $statement->get_result();
$movies = $result->fetch_all(MYSQLI_ASSOC);
$statement->close();
$db->close();
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Movies</title>
  <link rel="stylesheet" href="style.css">

</head>
<body>

  <?php
    if (file_exists(__DIR__ . '/header.php')) {
      require __DIR__ . '/header.php';
    }
  ?>

  <main>
    <nav>
      <ul class="movie-nav">
        <li><a href="movies.php?category=now_showing"
               class="<?php echo $category==='now_showing' ? 'active' : ''; ?>">Now Showing</a></li>
        <li><a href="movies.php?category=coming_soon"
               class="<?php echo $category==='coming_soon' ? 'active' : ''; ?>">Coming Soon</a></li>
      </ul>
    </nav>

    <?php if (!empty($movies)): ?>
      <?php foreach ($movies as $movie): ?>
        <div class="movie-box">
          <img
            src="<?php echo htmlspecialchars($movie['poster_path'] ?: 'media/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>"
            alt="Poster for <?php echo htmlspecialchars($movie['movie_name'], ENT_QUOTES, 'UTF-8'); ?>"
            class="movie-poster">
          <div>
            <h3 class="movie-title"><?php echo htmlspecialchars($movie['movie_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="movie-description"><?php echo htmlspecialchars($movie['synopsis'], ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="tickets.php?id=<?php echo urlencode($movie['moviesid']); ?>" class="btn-tickets">Get Tickets</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center;">No movies found in this category.</p>
    <?php endif; ?>
  </main>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>
