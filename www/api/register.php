<?php
$db_host = "postgres";
$db_name = "ctf";
$db_username = "ctf";
$db_password =
	"cce19de2cb307a05ed4f95a2092a98a0c436ac58a303f7fcc7afec2916378cb5";

$pdo = new PDO(
	"pgsql:host=$db_host;dbname=$db_name",
	$db_username,
	$db_password
);

$pdo->exec("
  CREATE TABLE IF NOT EXISTS users (
    username TEXT PRIMARY KEY NOT NULL,
    password TEXT NOT NULL
  );
  CREATE TABLE IF NOT EXISTS active_challenges (
    id TEXT PRIMARY KEY NOT NULL,
    challenge TEXT NOT NULL,
    flag TEXT NOT NULL
  );
");

function login($pdo, $username, $password)
{
	$stmt = $pdo->prepare(
		"SELECT password FROM users WHERE username=:username"
	);
	$stmt->bindParam(":username", $username);

	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!is_null($row)) {
		$password_hash = $row["password"];
		if (password_verify($password, $password_hash)) {
			$result = (object) [
				"success" => true,
				"args" => [
					"jwt" => "nevergonna",
				],
			];
			echo json_encode($result);
		} else {
			$result = (object) [
				"success" => false,
				"msg" => "Falsches Passwort.",
			];
			echo json_encode($result);
		}
	} else {
		$result = (object) [
			"success" => false,
			"msg" => "Benutzer existiert nicht.",
		];
		echo json_encode($result);
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$request = json_decode(file_get_contents("php://input"), true);

	if (
		isset($request["username"]) &&
		isset($request["password"]) &&
		isset($request["password_confirm"])
	) {
		$username = $request["username"];
		$password = $request["password"];
		$password_confirm = $request["password_confirm"];

		$stmt = $pdo->prepare(
			"SELECT username FROM users WHERE username=:username"
		);
		$stmt->bindParam(":username", $username);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		#if (is_null($row)) {
		if ($password !== $password_confirm) {
			$result = (object) [
				"success" => false,
				"msg" => "Die Passwörter stimmen nicht überein.",
			];
			echo json_encode($result);
			exit();
		}

		$hashed_password = password_hash($password, PASSWORD_ARGON2I);

		$stmt = $pdo->prepare(
			"INSERT INTO users (username, password) VALUES (:username, :hashed_password)"
		);
		$stmt->bindParam(":username", $username);
		$stmt->bindParam(":hashed_password", $hashed_password);

		$stmt->execute();
		login($pdo, $username, $password);
		/*} else {
			$result = (object) [
				"success" => false,
				"msg" => "Der Account existiert bereits.",
			];
			echo json_encode($result);
		}*/
	} else {
		$result = (object) [
			"success" => false,
			"msg" => "Nicht alle Parameter angegeben.",
		];
		echo json_encode($result);
	}
}
?>
