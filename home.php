<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  session_start();
  include 'header.php';
  ?>

  <main>
    <section class="banner">
        <h1>Experience Movies Like Never Before</h1>
        <img src="media/fantastic4_banner.jpg" alt="Featured" class="banner-img">
    </section>

    <section class="gallery-box">
      <a href="movies.php" class="btn-browse">Browse Movies</a>
      <div class="posters">
        <?php
          include 'dbconnect.php';


          $sql = "SELECT poster_path FROM movies ORDER BY moviesid LIMIT 5";
          $result = $dbconnect->query($sql);

          if ($result) {
            while ($row = $result->fetch_assoc()) {

              $poster = htmlspecialchars($row['poster_path']);
              $alt = htmlspecialchars($row['movie_name']);

              echo "<img src=\"$poster\" alt=\"$alt\" class=\"poster\">";
            }
          } else {
            echo "<p>No movies found.</p>";
          }

          $dbconnect->close();
        ?>
      </div>
    </section>
  </main>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>

