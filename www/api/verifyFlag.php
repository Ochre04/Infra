<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$request = json_decode(file_get_contents("php://input"), true);

	if (
		isset($request["jwt"]) &&
		isset($request["challenge"]) &&
		isset($request["flag"])
	) {
		$jwt = $request["jwt"];
		$challenge = $request["challenge"];
		$flag = $request["flag"];

		// TODO: JWT
		if (true) {
			$stmt = $pdo->prepare(
				"SELECT * FROM active_challenges WHERE challenge=:challenge AND flag=:flag"
			);
			$stmt->bindParam(":challenge", $challenge);
			$stmt->bindParam(":flag", $flag);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!is_null($row)) {
				// TODO: Add challenge to history
				$result = (object) [
					"success" => true,
					"args" => [
						"challenge" => $challenge,
					],
				];
				echo json_encode($result);
			} else {
				$result = (object) [
					"success" => false,
					"msg" => "Die Flag \"$flag\" für die Challenge \"$challenge\" ist nicht korrekt!",
				];
				echo json_encode($result);
			}
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
