<h1>Konta</h1>
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

    // Zapytanie do bazy danych w celu pobrania danych z tabeli accounts
    $query = "SELECT accounts.account_id AS id, accounts.Login AS Login, account_data.name AS Name, account_data.surname AS Surname, account_data.email AS Email, account_data.number AS Number 
        FROM (accounts INNER JOIN account_data ON accounts.account_id = account_data.account_id) 
        JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id 
        JOIN organisations ON link_organisations_accounts.organisation_id = organisations.organisation_id 
        WHERE (accounts.Type = '2' OR accounts.Type='3') 
        AND organisations.organisation_id = 
        (SELECT organisations.organisation_id FROM accounts JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id JOIN organisations ON link_organisations_accounts.organisation_id = organisations.organisation_id WHERE accounts.account_id = '".$_SESSION["user_id"]."');";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        // Wyświetlenie danych w tabeli
        echo '<table class="w3-table-all">';
        echo "<tr><th>ID</th><th>Login</th><th>Name</th><th>Surname</th><th>Email</th><th>Number</th><th>Change Password</ht></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["id"]."</td><td>".$row["Login"]."</td><td>".$row["Name"]."</td><td>".$row["Surname"]."</td><td>".$row["Email"]."</td><td>".$row["Number"]."</td><td><button onclick=".'"'."ChangePasswordForm(".$row["id"].", '".$row["Login"]."')".'"'.">Zmień Hasło</button></td></tr>";
        }
        echo "</table>";
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
        <button type="submit" name="PC_submit">Dodaj konto</button>
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
        $surname= $_POST['surname'];
        $email = $_POST['email'];
        if(isset($_POST["number"])){
            $number = $_POST['number'];
        }
        else{
            $number = null;
        }

        // Pobranie ostatniego ID z tabeli accounts
        $query = "SELECT MAX(account_id) AS max_id FROM accounts";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $account_id = $row['max_id'] + 1;

        // Generowanie soli
        $salt = bin2hex(random_bytes(16));

        // Obliczanie skrótu hasła
        $password_hash = hash('sha256', $password . $salt);

        // Dodawanie nowego konta do tabeli accounts
        $query = "INSERT INTO accounts (account_id, Login, Password_hash, Salt, Type) VALUES ('$account_id', '$login', '$password_hash', '$salt', '$type')";
        $connection->query($query);

        // Dodawanie nowej odanych do tabeli account_data
        $query = "INSERT INTO account_data (account_id, name, surname, email, number) VALUES ('$account_id', '$name', '$surname', '$email', '$number')";
        $connection->query($query);

        // Pobranie  ID z tabeli organisations
        $query = "SELECT organisations.organisation_id AS id FROM accounts JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id JOIN organisations ON link_organisations_accounts.organisation_id = organisations.organisation_id WHERE accounts.account_id = '".$_SESSION["user_id"]."';";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $organisation_id = $row['id'];

    
        // Dodawanie relacji między kontem a organizacją do tabeli link_organisations_accounts
        $query = "INSERT INTO link_organisations_accounts (account_id, organisation_id) VALUES ('$account_id', '$organisation_id')";
        $connection->query($query);
            echo "Nowe konto zostało dodane <br>";
            echo("Login: $login <br>");
            echo("Hasło: $password <br>");
        }
        $connection->close();
    ?>
</div>
