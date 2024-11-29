<?php
$servername = "localhost"; // server
$username = "root"; // user
$password = ""; // pw
$dbname = "ctf_website"; // db

// connect
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // user input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password_confirm = mysqli_real_escape_string($conn, $_POST['password_confirm']);
    
    // pw check
    if ($password !== $password_confirm) {
        echo "Die Passwörter stimmen nicht überein.";
        exit;
    }

    // encrypt pw
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // data insert
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registrierung erfolgreich!";
    } else {
        echo "Fehler: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren</title>
</head>
<body>
    <h2>Registrieren für das CTF</h2>
    <form method="post" action="register.php">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_confirm">Passwort bestätigen:</label>
        <input type="password" id="password_confirm" name="password_confirm" required><br><br>

        <input type="submit" value="Registrieren">
    </form>
</body>
</html>
