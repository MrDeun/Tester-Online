<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hash Password</title>
</head>
<body>
    <h2>Generate Salt and Hash for Password</h2>
    <form method="post">
        <label for="password">Enter Password:</label><br>
        <input type="text" id="password" name="password"><br><br>
        <button type="submit" name="submit">Generate Salt and Hash</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        // Pobranie hasła z formularza
        $password = $_POST['password'];

        // Wygenerowanie soli (32 znaki w przykładzie)
        $salt = bin2hex(random_bytes(16));

        // Obliczenie skrótu hasła
        $password_hash = hash('sha256', $password . $salt);

        // Wyświetlenie soli i skrótu hasła
        echo "<h3>Password</h3>";
        echo "<p>$password</p>";
        echo "<h3>Salt:</h3>";
        echo "<p>$salt</p>";
        echo "<h3>Password Hash:</h3>";
        echo "<p>$password_hash</p>";
    }
    ?>
</body>
</html>
