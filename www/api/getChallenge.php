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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$request = json_decode(file_get_contents("php://input"), true);

	if (isset($_GET["id"])) {
		$id = $_GET["id"];

		$stmt = $pdo->prepare(
			"SELECT challenge FROM active_challenges WHERE id=:id"
		);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!is_null($row)) {
			$challenge = $row["challenge"];

			header("Content-Type: application/zip");
			header(
				"Content-Disposition: attachment; filename=\"$challenge.zip\""
			);
			header("Content-Length: " . filesize("$challenge.zip"));

			$zip = new ZipArchive();
			$zip->open("/$challenge.zip", ZipArchive::CREATE);
			foreach (glob("/var/www/Infra/www/Flags/$challenge/*") as $file) {
				$zip->addFile($file);
			}
			$zip->close();

			// Send the file to the browser
			readfile("/$challenge.zip");

			unlink("/$challenge.zip");
		} else {
			$result = (object) [
				"success" => false,
				"msg" => "Laufende Challenge \"$id\" existiert nicht.",
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
