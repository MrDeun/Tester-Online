<h4>Ostatnio Aktywowane testy</h4>

<div class="w3-container">
    <table class="w3-table w3-table-all">
        <tr><th>ID</th><th>Nazwa</th><th>Data aktywacji</th><th>Liczba uczestników</th><th>Sprawdź</th></tr>
        <?php

            // Zapytanie do bazy danych
            $query = "SELECT activeted_test.id AS ID, test.name AS Name, activeted_test.activation_time AS Time, COUNT(link_account_activated_test.id) AS Accounts
                FROM activeted_test JOIN test ON activeted_test.test_id=test.id_test
                LEFT JOIN link_account_activated_test ON activeted_test.id = link_account_activated_test.activated_test_id
                WHERE test.id_test NOT IN (
                    SELECT test.id_test
                    FROM test
                    WHERE test.account_id <> ".$_SESSION["user_id"]."
                )
                GROUP BY activeted_test.id, test.name, activeted_test.activation_time
                ORDER BY activeted_test.activation_time DESC
                LIMIT 10;";
            $result = $connection->query($query);

            if ($result->num_rows > 0) {
                // Wyświetlenie danych w tabeli
                echo "<tr>
                    <td>".$row["ID"]."</td>
                    <td>".$row["Name"]."</td>
                    <td>".$row["Time"]."</td>
                    <td>".$row["Accounts"]."</td>
                    <td>
                        <form action='".$_SERVER['PHP_SELF'].">' method='post'>
                            <input type='hidden' name='site' value='activated_test'>
                            <input type='hidden' name='test_id' value='".$row["ID"]."'>.
                            <buttom>Sprawdź</buttom></td>
                </th>";
            } else {
                echo "<tr><td></td><td></td><td></td><td></td><td></td></th>";
            }
        ?>
    </table>
</div>