<?php
session_start();
include "dbconnect.php";

$self = $_SERVER['PHP_SELF']; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];

    if (!$email || !$username || !$password || !$passwordConfirm) {
        header("Location: $self?msg=".urlencode("All fields are required."));
        exit;
    }

    // php server side validation: if email/user exist
    $check = $dbconnect->prepare("SELECT * FROM members WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        $check->close();
        header("Location: $self?msg=".urlencode("Username or email already exists."));
        exit;
    }
    $check->close();

    // hash password, tehn insert values into db table
    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $statement = $dbconnect->prepare("INSERT INTO members (username,email,password) VALUES (?,?,?)");
    $statement->bind_param("sss", $username, $email, $hashedpassword);
    if ($statement->execute()) {
        $statement->close();
        header("Location: login.php?msg=".urlencode("Sign up successful! Please log in."));
        exit;
    } else {
        $statement->close();
        header("Location: $self?msg=".urlencode("Sign up unsuccessful."));
        exit;
    }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Signup</title>
  <link rel="stylesheet" href="style.css">
</head>
<script type="text/javascript" src="formvalidation.js"></script>
<body>

  <?php
  session_start();
  include 'header.php';
  ?>

  <main>
    <div class="login-signup">
    <h1>Sign Up</h1>

    <?php
    if (isset($_GET['msg'])) {
        echo '<script>alert("' . htmlspecialchars($_GET['msg']) . '");</script>';
    }
    ?>

    <form id="signupForm" method="post" action="">
      <label for="email">E-mail:</label>
      <input type="email" name="email" id="email" placeholder="Enter your Email here"><br>

      <label for="username">Username:</label>
      <input type="text" name="username" id="username" placeholder="Enter your username here"><br>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" placeholder="Enter your password here"><br>

      <label for="passwordConfirm">Re-enter Password:</label>
      <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm your password"><br>
    
      <input type="submit" value="Sign Up" class="btn">
    </form>

    <div class="bottom">
    <p>
        <a href="home.php">Back to home</a>
        <a href="login.php">Login</a>
        <a href="logout.php">Logout</a>
    </p>
    </div>
    </div>
  </main>

  <footer>&copy; 2025 Lume</footer>
</body>
</html>

