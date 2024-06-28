<?php
function clear_session_except($keep_keys = []) {
    // Iteruj przez wszystkie zmienne sesji
    foreach ($_SESSION as $key => $value) {
        // Jeśli klucz nie znajduje się w tablicy $keep_keys, usuń go
        if (!in_array($key, $keep_keys)) {
            unset($_SESSION[$key]);
        }
    }
}
function checkSessionAndRedirect($sessionVar) {
    // Sprawdź stan sesji i czy zmienna sesji istnieje
    if (session_status() == PHP_SESSION_NONE || !isset($_SESSION[$sessionVar])) {
        clear_session_except();
        // Zbuduj ścieżkę do katalogu x poziomów wyżej
        $path = getTesterOnlinePath();
        // Przekieruj użytkownika do zbudowanej ścieżki
        header("Location: $path");
        exit();
    }
}
function getTesterOnlinePath() {
    // Pobieramy adres hosta
    $host = $_SERVER['HTTP_HOST'];

    // Pobieramy schemat żądania (HTTP lub HTTPS)
    $scheme = $_SERVER['REQUEST_SCHEME'];

    // Pobieramy ścieżkę do katalogu Tester Online
    $Path = dirname($scheme . '://' . $host . $_SERVER['PHP_SELF']);

    // Usuwamy /d/xampp/htdocs z ścieżki
    $Path = str_replace("/d/xampp/htdocs", "", $Path);


    // Znajdujemy pozycję katalogu "Tester%20Online"
    $position = strpos($Path, "/Tester Online");

    // Jeżeli znaleziono pozycję, to zwracamy część ścieżki od początku do miejsca znalezienia katalogu "Tester%20Online" włącznie
    if ($position !== false) {
        $testerOnlinePath = substr($Path, 0, $position + strlen("/Tester%20Online") - 1);
    } else {
        // Jeżeli nie znaleziono, to zwracamy całą ścieżkę
        $testerOnlinePath = "localhost/";
    }
    return $testerOnlinePath;
}



function redirectToIndex($folderPath) {
    // Pobierz ścieżkę do katalogu TesterOnline
    $testerOnlinePath = getTesterOnlinePath();


    // Utwórz ścieżkę do pliku index.php w podanym folderze
    $indexPath = $testerOnlinePath . $folderPath;
    // Przekieruj użytkownika do pliku index.php w podanym folderze
    header("Location: $indexPath");
    exit();
}


function generateNav($action, $buttonName, $formClass = "w3-left") {
    echo "<a class='w3-container $formClass' href='$action'>";
    echo "<button class='w3-button w3-aqua' type='submit'>$buttonName</button>";
    echo "</a>";
}


