<?php

// Procedura MySQL do pobierania ostatnio aktywowanych testów
$query = "CALL GetRecentlyActivatedTests(" . $_SESSION["user_id"] . ")";
$result = $connection->query($query);

echo "<h4>Ostatnio Aktywowane testy</h4>";
echo "<div class='w3-container'>";
echo "<table class='w3-table w3-table-all'>";
echo "<tr><th>ID</th><th>Nazwa</th><th>Data aktywacji</th><th>Liczba uczestników</th><th>Sprawdź</th></tr>";

$check = false;
while ($row = mysqli_fetch_assoc($result)) {
    $check = true;
    // Wyświetlenie danych w tabeli
    echo "<tr>
        <td>".$row["ID"]."</td>
        <td>".$row["Name"]."</td>
        <td>".$row["ActivationTime"]."</td>
        <td>".$row["Participants"]."</td>
        <td>
            <form action='$create_check' method='post'>
                <input type='hidden'  name='activated_id' value='".$row["ID"]."'>
                <button class='w3-button w3-blue'>Sprwawdź</button>
            </form>
        </td>
    </tr>";
};

if ($check == false) {
    echo "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
}

echo "</table>";
echo "</div>";

$connection->close();
