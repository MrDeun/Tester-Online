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