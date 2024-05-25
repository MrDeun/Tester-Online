<?php

// Pobranie danych z formularza
$username = $_POST['username'];
$password = $_POST['password'];




$stored_password_hash = "fc15a9a4f261d4fd25f3a5b2b9588a37dc7bfe28730cc0f19a02ef51fbf42f76";
$salt = "e801b626f38fd8230219843389156765";
// Obliczanie skrótu hasła
echo '<form id="redirectForm" action="index.php" method="post">';
$password_hash = hash('sha256', $password . $salt);
if (($username == "admin") && ($password_hash === $stored_password_hash)) {
    // Hasło poprawne, przekazujemy do następnego pliku ID konta       
    echo '<input type="hidden" name="site" value="1024">';
    
}
else{
    // Brak użytkownika o podanym loginie lub niepoprawne hasło
    // Przekazujemy wartość błędu do poprzedniego pliku
    echo '<input type="hidden" name="site" value="login_form">';
    echo '<input type="hidden" name="error" value="1">';

}
echo '</form>';
echo '<script>document.getElementById("redirectForm").submit();</script>';
exit(); // Zakończamy wykonywanie skryptu po przekierowaniu


?>
