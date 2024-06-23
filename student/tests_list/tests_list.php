<div class="w3-container w3-margin-top">
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj tabelę.." onkeyup='w3.filterHTML("#filterTable", ".item", this.value)'>
    <table class="w3-table-all w3-margin-top w3-margin-bottom" id="filterTable" style="width: 100%;">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 45%;">Nazwa</th>
            <th style="width: 25%;">Data</th>
            <th style="width: 25%;">Punkty</th>
            
        </tr>
        <?php

            // Zapytanie do bazy danych
            $sql = "SELECT activated_tests.id, tests.name, activated_tests.activation_time, link_account_activated_tests.points FROM activated_tests LEFT JOIN tests ON activated_tests.test_id = tests.id JOIN link_account_activated_tests ON activated_tests.id = link_account_activated_tests.activated_test_id WHERE link_account_activated_tests.account_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $_SESSION["user_id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $check = false;
            //if ($result->num_rows > 0) {
            while($row = mysqli_fetch_row($result)){
                $check = true;
                // Wyświetlenie danych w tabeli
                echo "<tr class='item'>
                    <td>".$row[0]."</td>
                    <td>".$row[1]."</td>
                    <td>".$row[2]."</td>
                    <td>".$row[3]."</td>
                </tr>";
            } 
            if(!$check){
                echo "<tr><td></td><td></td><td></td><td></td></tr>";
            }
        ?>
    </table>
</div>