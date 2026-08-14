<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lume";

$dbconnect = new mysqli($servername, $username, $password, $dbname);

if ($dbconnect->connect_error) {
    die("Connection failed: " . $dbconnect->connect_error);
}
?>

