<?php
$db_host = "postgres";
$db_name = "ctf";
$db_username = "ctf";
$db_password = "cce19de2cb307a05ed4f95a2092a98a0c436ac58a303f7fcc7afec2916378cb5";

$myPDO = new PDO('pgsql:host=$db_host;dbname=$db_name', $db_username, $db_password);

$username = $_POST["username"];
$password = $_POST["password"];
?>
