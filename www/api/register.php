<?php
$db_host = "postgres";
$db_name = "ctf";
$db_username = "ctf";
$db_password = "cce19de2cb307a05ed4f95a2092a98a0c436ac58a303f7fcc7afec2916378cb5";

$pdo = new PDO("pgsql:host=$db_host;dbname=$db_name", $db_username, $db_password);

$init = "
  CREATE TABLE IF NOT EXISTS users (
    username TEXT PRIMARY KEY NOT NULL,
    password TEXT NOT NULL
  );
  CREATE TABLE IF NOT EXISTS active_challenges (
    id TEXT PRIMARY KEY NOT NULL,
    flag TEXT NOT NULL,
    username TEXT NOT NULL
  );
";
$pdo->exec($init);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // user input
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // pw check
    if ($password !== $password_confirm) {
	$result = (object) array(
          'status' => true,
          'msg' => "Die Passwörter stimmen nicht überein.",
        );
        echo json_encode($result);
        exit;
    }

    // encrypt pw
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);;

    // data insert
    $sql = "INSERT INTO users (username, password) VALUES (:username, :hashed_password)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':hashed_password', $hashed_password);

    if ($stmt->execute() === true) {
      header("Location: /api/login.php");
      exit();
    } else {
	$result = (object) array(
          'status' => true,
          'msg' => "Benutzer existiert bereits.",
        );
        echo json_encode($result);
    }
}
?>
