<?php
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
        echo '<form id="redirectForm" action="index.php" method="post">';
        switch ($type) {
            case 1:
                echo '<input type="hidden" name="site" value="logged_manager">';
                break;
            case 2:
                echo '<input type="hidden" name="site" value="logged_teacher">';
                break;
            case 3:
                echo '<input type="hidden" name="site" value="logged_student">';
                break;
            default:
                # code...
                break;
        }
        echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
        
    }
    else{
        // Brak użytkownika o podanym loginie lub niepoprawne hasło
        // Przekazujemy wartość błędu do poprzedniego pliku
        echo '<form id="redirectForm" action="index.php" method="post">';
        echo '<input type="hidden" name="site" value="login_form">';
        echo '<input type="hidden" name="error" value="2">';
    
    }
}
else{
    // Brak użytkownika o podanym loginie lub niepoprawne hasło
    // Przekazujemy wartość błędu do poprzedniego pliku
    echo '<form id="redirectForm" action="index.php" method="post">';
    echo '<input type="hidden" name="site" value="login_form">';
    echo '<input type="hidden" name="error" value="1">';

}
$connection->close();
echo '</form>';
echo '<script>document.getElementById("redirectForm").submit();</script>';
exit(); // Zakończamy wykonywanie skryptu po przekierowaniu


?>
