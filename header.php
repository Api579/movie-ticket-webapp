<?php
if (session_status() === PHP_SESSION_NONE){
  session_start();}
?>
<header>
  <h1 class="cinema-name">Lume</h1>
  <nav>
    <ul class="nav-bar">
      <li><a href="home.php">Home</a></li>
      <li><a href="movies.php">Movies</a></li>
      <li><a href="cart.php">Tickets</a></li>
      <li><a href="admin.php">Admin</a></li>
      <?php if (isset($_SESSION['valid_user'])): ?>
        <li><a class="btn" href="logout.php">Logout</a></li>
      <?php else: ?>
        <li><a class="btn" href="login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>
