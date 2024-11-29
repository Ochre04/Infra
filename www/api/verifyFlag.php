<?php
session_start();

$db_host = "postgres";
$db_name = "ctf";
$db_username = "ctf";
$db_password = "cce19de2cb307a05ed4f95a2092a98a0c436ac58a303f7fcc7afec2916378cb5";

$conn = new pg_connect("host=$db_host dbname=$db_name user=$db_username password=$db_password");

if (!$conn) {
    die("Verbindung fehlgeschlagen");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $challenge = $_POST['challenge'];
    $flag = $_POST['flag'];
    $username = $_POST['username'];

    // Prepare query
    $sql = "SELECT id FROM flags WHERE challenge = $1 AND flag = $2 AND username = $3";
    $result = pg_prepare($conn, "verify_flag", $sql);

    // Execute query
    $result = pg_execute($conn, "verify_flag", array($challenge, $flag, $username));

    // Check result
    if (pg_num_rows($result) > 0) {
        echo "Flag erfolgreich verifiziert!";
    } else {
        echo "Ungültige Herausforderung, Flagge oder Benutzername.";
    }
}

pg_close($conn);
?>
