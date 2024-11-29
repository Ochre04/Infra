<?php
session_start();

$db_host = "postgres";
$db_name = "ctf";
$db_username = "ctf";
$db_password = "cce19de2cb307a05ed4f95a2092a98a0c436ac58a303f7fcc7afec2916378cb5";

// Connect
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $sql = "SELECT id, username, password FROM users WHERE username = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ss", $username, $username); // 'ss' -> two strings

        // Execute statement
        $stmt->execute();

        // Bind res vars
        $stmt->bind_result($id, $username, $hashed_password);

        if ($stmt->fetch()) {
            // check pw
            if (password_verify($password, $hashed_password)) {
                // set session variables
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php"); // redirect to dashboard
                exit();
            } else {
                echo "Falsches Passwort.";
            }
        } else {
            echo "Benutzername oder E-Mail nicht gefunden.";
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Datenbankfehler: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login für das CTF</h2>
    <form method="post" action="login.php">
        <label for="username">Benutzername oder E-Mail:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Einloggen">
    </form>
</body>
</html>
