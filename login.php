<?php
session_start();
include "dbconnect.php";

$self = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $statement = $dbconnect->prepare("SELECT * FROM members WHERE username = ?");
    if (!$statement) {
        die("Database error: " . $dbconnect->error);
    }

    $statement->bind_param("s", $username);
    $statement->execute();
    $result = $statement->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['valid_user'] = $row['username'];
            $_SESSION['member_id'] = $row['membersid'];

            if (file_exists(__DIR__ . '/configureAdmin.php')) {
                require_once __DIR__ . '/configureAdmin.php';
                $_SESSION['is_admin'] = in_array($row['username'], $ADMINS, true);
            } else {
                $_SESSION['is_admin'] = false;
            }

            header("Location: home.php");
            exit;
        }
    }

    $statement->close();
    header("Location: ".$self."?msg=".urlencode("Invalid username or password."));
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  session_start();
  include 'header.php';
  ?>

  <main>
    <div class="login-signup">
    <h1>Login</h1>

    <form method="post" action="">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required placeholder="Enter your username"><br>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required placeholder="Enter your password"><br>

      <input type="submit" value="Login" class="btn">
    </form>

    <div class="bottom">
    <p>
      <a href="signup.php">Sign up for an account</a>
      <a href="logout.php">Logout</a>
    </p>
    </div>
    </div>
  </main>

  <footer>&copy; 2025 Lume</footer>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const URLparams = new URLSearchParams(window.location.search);
    const msg = URLparams.get('msg');
    if (msg) {
      alert(msg);
      if (history.replaceState) {
        history.replaceState(null, '', window.location.origin + window.location.pathname);
      }
    }
  });
  </script>

</body>
</html>

