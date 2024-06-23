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

            // Procedura MySQL do pobierania kont na podstawie określonego typu
            $type = 1; // Typ konta, którego chcemy pobrać
            $stmt = $connection->prepare("CALL GetAccountsByType(?)");
            $stmt->bind_param("i", $type);
            $stmt->execute();
            $result = $stmt->get_result();

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
            $stmt->close();
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
                // Procedura MySQL do zmiany hasła
                $stmt = $connection->prepare("CALL ChangePassword(?, ?, ?)");
                $stmt->bind_param("iss", $id, $password_hash, $salt);
                $stmt->execute();
                $stmt->close();
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
            // Procedura MySQL do dodawania nowego konta i organizacji
            $stmt = $connection->prepare("CALL AddAccountAndOrganization(?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $login, $password, $name, $address, $ini);
            $stmt->execute();
            echo "Nowe konto i organizacja zostały dodane";
            $stmt->close();
        }
        $connection->close();
    ?>

    <script>
        function ChangePasswordForm(id, login) {
            document.getElementById("PC_id").value = id;
            document.getElementById("PC_login").value = login;
            document.querySelector('.ChangePasswordForm').classList.toggle('w3-hide');
        }
    </script>
</body>
</html>
