<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Logout</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
    include 'header.php';
  ?>
  
  <main>
    <?php
    
    if (session_status() === PHP_SESSION_NONE){
      session_start();
    }
    unset($_SESSION['valid_user']);
    session_destroy();

    echo "<div class='login-signup'>";
    echo "<h1>Logged out</h1>";
    echo "<div class='bottom'>";
    echo "<a href='home.php'>Return to Home</a>";
    echo "</div>";
    ?>
  </main>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>