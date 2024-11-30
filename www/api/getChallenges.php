<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$challenges = array_diff(scandir("../Flags"), [".", ".."]);
	$result = (object) [
		"success" => true,
		"args" => [
			"challenges" => $challenges,
		],
	];
	echo json_encode($result);
}
?>
