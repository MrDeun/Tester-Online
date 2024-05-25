<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../functions.php');
$site = getTesterOnlinePath();
// Dane do połączenia z bazą danych MySQL
$host = 'localhost'; // Adres hosta bazy danych
$username = 'root'; // Nazwa użytkownika bazy danych
$password = ''; // Hasło użytkownika bazy danych
$database = 'tester'; // Nazwa bazy danych

// Nawiązanie połączenia z bazą danych
$connection = new mysqli($host, $username, $password, $database);

// Sprawdzenie czy udało się połączyć z bazą danych
if ($connection->connect_error) {
    die("Błąd połączenia z bazą danych: " . $connection->connect_error);
}

// Pobranie danych z formularza
$username = $_POST['username'];
$password = $_POST['password'];

// Zabezpieczenie przed atakami SQL Injection
$username = mysqli_real_escape_string($connection, $username);

// Zapytanie do bazy danych w celu pobrania soli dla danego użytkownika
$query = "SELECT account_id, Password_hash, Salt, Type FROM accounts WHERE Login='$username'";
$result = $connection->query($query);

if ($result->num_rows > 0) {
    // Znaleziono użytkownika, sprawdzamy hasło
    $row = $result->fetch_assoc();
    $user_id = $row['account_id'];
    $stored_password_hash = $row['Password_hash'];
    $salt = $row['Salt'];
    $type = $row['Type'];

    // Obliczanie skrótu hasła
    $password_hash = hash('sha256', $password . $salt);
    if ($password_hash === $stored_password_hash) {
        // Hasło poprawne, przekazujemy do następnego pliku ID konta
        $_SESSION['user_id'] = $user_id;
        $_SESSION['logged'] = $type;
        header('Location: '.$site);
        exit();
    } else {
        // Hasło niepoprawne
        header('Location: '.$site.'login?error=2');
        exit();
    }
} else {
    // Brak użytkownika o podanym loginie
    header('Location: '.$site.'login?error=1');
    $connection->close();
    exit();
}







