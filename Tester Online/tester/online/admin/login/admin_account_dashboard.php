<style>
    table {
        border-collapse: collapse;
        width: 100%;
        
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
</style>

<h2>Accounts</h2>
<div style="overflow-x: auto;">
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
    $query = "SELECT accounts.account_id AS id, accounts.Login AS Login, organisations.Name AS Name, organisations.initials AS Initials, organisations.Address As Address 
        FROM accounts JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id 
        JOIN organisations ON link_organisations_accounts.organisation_id = organisations.organisation_id 
        WHERE accounts.Type = '1';";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        // Wyświetlenie danych w tabeli
        echo "<table>";
        echo "<tr><th>ID</th><th>Login</th><th>Name</th><th>Initials</th><th>Address</th><th>Change Password</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id"]."</td>
                    <td>".$row["Login"]."</td>
                    <td>".$row["Name"]."</td>
                    <td>".$row["Initials"]."</td>
                    <td>".$row["Address"]."</td>
                    <td><button onclick=".'"'."ChangePasswordForm(".$row["id"].", '".$row["Login"]."')".'"'.">Zmień Hasło</button></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "Brak danych do wyświetlenia";
    }
    
?>
</div>
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
<h2>Dodaj nowe konto i organizację</h2>
<form method="post">
    <input type="hidden" name="logged" value="1024">
    <label for="name">Nazwa organizacji:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="name">Inicjały organizacji:</label><br>
    <input type="text" id="ini" name="ini"><br>
    <label for="address">Adres organizacji:</label><br>
    <input type="text" id="address" name="address"><br>
    <label for="login">Login:</label><br>
    <input type="text" id="login" name="login"><br>
    <label for="password">Hasło:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <button type="submit" name="submit">Dodaj konto i organizację</button>
</form>

    
<?php
    if (isset($_POST['submit'])) {
        // Pobranie danych z formularza
        $name = $_POST['name'];
        $ini = $_POST['ini'];
        $address = $_POST['address'];
        $login = $_POST['login'];
        $password = $_POST['password'];

        // Pobranie ostatniego ID z tabeli accounts
        $query = "SELECT MAX(account_id) AS max_id FROM accounts";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $account_id = $row['max_id'] + 1;

        // Zapisanie loginu i hasła do pliku hasla.txt
        $file = fopen("hasla.txt", "a");
        fwrite($file, "Login: " . $login . ", Password: " . $password . "\n");
        fclose($file);

        // Generowanie soli
        $salt = bin2hex(random_bytes(16));

        // Obliczanie skrótu hasła
        $password_hash = hash('sha256', $password . $salt);

        // Dodawanie nowego konta do tabeli accounts
        $query = "INSERT INTO accounts (account_id, Login, Password_hash, Salt, Type) VALUES ('$account_id', '$login', '$password_hash', '$salt', '1')";
        $connection->query($query);

        // Pobranie ostatniego ID z tabeli organisations
        $query = "SELECT MAX(organisation_id) AS max_id FROM organisations";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $organisation_id = $row['max_id'] + 1;

        // Dodawanie nowej organizacji do tabeli organisations
        $query = "INSERT INTO organisations (organisation_id, Name, Address, initials) VALUES ('$organisation_id', '$name', '$address', '$ini')";
        $connection->query($query);

        // Dodawanie relacji między kontem a organizacją do tabeli link_organisations_accounts
        $query = "INSERT INTO link_organisations_accounts (account_id, organisation_id) VALUES ('$account_id', '$organisation_id')";
        $connection->query($query);

        echo "Nowe konto i organizacja zostały dodane";
    }
    $connection->close();
?>

