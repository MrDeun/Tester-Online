<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
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
</head>
<body>
    <h2>Accounts</h2>
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
    $query = "SELECT accounts.account_id AS id, accounts.Login AS Login, accounts.Password_hash AS Password_hash, accounts.Salt AS Salt FROM accounts WHERE accounts.Type = '1'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        // Wyświetlenie danych w tabeli
        echo "<table>";
        echo "<tr><th>ID</th><th>Login</th><th>Password Hash</th><th>Salt</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["id"]."</td><td>".$row["Login"]."</td><td>".$row["Password_hash"]."</td><td>".$row["Salt"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Brak danych do wyświetlenia";
    }
    
    ?>

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
</body>
</html>
