<h1>Konta</h1>
<?php

// Sprawdzenie czy udało się połączyć z bazą danych
if ($connection->connect_error) {
    die("Błąd połączenia z bazą danych: " . $connection->connect_error);
}

// Procedura MySQL do pobierania kont nauczycieli i uczniów
$query = "CALL GetAccounts(" . $_SESSION["user_id"] . ")";
$result = $connection->query($query);

// Pobranie i zwolnienie wyniku procedury MySQL
$connection->next_result();

if ($result->num_rows > 0) {
    // Wyświetlenie danych w tabeli
    echo '<div class="w3-container">';
    echo '<table class="w3-table-all">';
    echo "<tr><th>ID</th><th>Login</th><th>Name</th><th>Surname</th><th>Email</th><th>Number</th><th>Change Password</th></tr>";
    while ($row = $result->fetch_assoc()) {
        // Wyświetlenie danych w tabeli
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["Login"]."</td>
                <td>".$row["Name"]."</td>
                <td>".$row["Surname"]."</td>
                <td>".$row["Email"]."</td>
                <td>".$row["Number"]."</td>
                <td><button onclick=".'"'."ChangePasswordForm(".$row["id"].", '".$row["Login"]."')".'"'.">Zmień Hasło</button></td>
            </tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "Brak danych do wyświetlenia";
}
?>

<div class="w3-container ChangePasswordForm w3-hide">
    <h2>Zmiana hasła</h2>
    <form method="post">
        <input type="hidden" name="PC_id" value="0">
        <label for="PC_login">Login:</label><br>
        <input type="text" id="PC_login" name="PC_login" readonly><br>
        <label for="PC_password">Hasło:</label><br>
        <input type="password" id="PC_password" name="PC_password" required><br>
        <span class="show-password w3-button" onclick="togglePassword('PC_password')">Pokaż hasło</span><br>
        <button type="submit" name="PC_submit" id="PC_submit">Dodaj konto</button>
    </form>
    <?php
        if(isset($_POST['PC_submit'])){
            $id = $_POST["PC_id"];
            $login = $_POST['PC_login'];
            $password = $_POST['PC_password'];
            // Generowanie soli
            $salt = bin2hex(random_bytes(16));

            // Obliczanie skrótu hasła
            $password_hash = hash('sha256', $password . $salt);

            // Dodawanie nowego konta do tabeli accounts
            $query = "UPDATE `accounts` SET `Password_hash` = '$password_hash', `Salt` = '$salt' WHERE `accounts`.`account_id` = $id";
            $connection->query($query);

            // Pobranie i zwolnienie wyniku procedury MySQL
            $connection->next_result();
        }
    ?>
</div>


<div class="w3-container">
    <h2>Dodaj nowe konto</h2>
    <form method="post">
        <label for="type">Typ:</label><br>
        <select id="type" name="type">
            <option value="2">Nauczyciel</option>
            <option value="3">Uczeń</option>
        </select><br>
        <label for="login">Login:</label><br>
        <input type="text" id="login" name="login" required><br>
        <label for="password">Hasło:</label><br>
        <input type="password" id="password" name="password" required><br>
        <span class="show-password w3-button" onclick="togglePassword('password')">Pokaż hasło</span><br>  
        <label for="name">Imię:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="surname">Nazwisko:</label><br>
        <input type="text" id="surname" name="surname" required><br>
        <label for="email">E-mail:</label><br>
        <input type="text" id="email" name="email" required><br>
        <label for="number">Numer:</label><br>
        <input type="number" id="number" name="number"><br><br>
        <button type="submit" name="submit">Dodaj konto</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        // Pobranie danych z formularza
        $login = $_POST['login'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        if (isset($_POST["number"])) {
            $number = $_POST['number'];
        } else {
            $number = null;
        }
        $type = $_POST['type'];

        // Generowanie soli w PHP
        $salt = bin2hex(random_bytes(16));

        // Hashowanie hasła w PHP z użyciem wygenerowanej soli
        $password_hash = hash('sha256', $password . $salt);

        // Wywołanie procedury MySQL z użyciem wygenerowanej soli
        $query = "CALL AddNewAccount('$login', '$password_hash', '$salt', '$name', '$surname', '$email', '$number', '$type', '".$_SESSION["user_id"]."')";
        $connection->query($query);

        // Obsługa wyniku procedury MySQL
        while ($connection->next_result()) { 
            if (!$connection->more_results()) break;
        }

        echo "Nowe konto zostało dodane <br>";
        echo "Login: $login <br>";
        echo "Hasło: $password <br>";
    }
    ?>

</div>
