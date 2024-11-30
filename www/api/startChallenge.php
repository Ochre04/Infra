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

function generateRandomString($length = 10)
{
	return substr(
		str_shuffle(
			str_repeat(
				$x =
					"0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
				ceil($length / strlen($x))
			)
		),
		1,
		$length
	);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$request = json_decode(file_get_contents("php://input"), true);

	if (isset($request["jwt"])) {
		$jwt = $request["jwt"];
		$challenge = $request["challenge"];

		if (true) {
			$stmt = $pdo->prepare(
				"INSERT INTO active_challenges (id, challenge, flag) VALUES (:id, :challenge, :flag)"
			);

			$id = generateRandomString();
			$flag = generateRandomString();

			$stmt->bindParam(":id", $id);
			$stmt->bindParam(":challenge", $challenge);
			$stmt->bindParam(":flag", $flag);

			$stmt->execute();

			$result = (object) [
				"success" => true,
				"args" => [
					"id" => $id,
				],
			];
			echo json_encode($result);
			// TODO: do not start it multiple times
		} else {
			$result = (object) [
				"success" => false,
				"msg" => "Invalid JWT!",
			];
			echo json_encode($result);
		}
	} else {
		$result = (object) [
			"success" => false,
			"msg" => "Nicht alle Parameter angegeben.",
		];
		echo json_encode($result);
	}
}

?>
