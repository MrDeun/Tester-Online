<div class="w3-container">
    <form class="w3-container w3-center w3-margin-top" action="./create" method="POST">
        <button class="w3-button w3-aqua ">AKTYWUJ TEST</button>
    </form>
</div>
<div class="w3-container w3-margin-top">
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj tabelę.." onkeyup='w3.filterHTML("#filterTable", ".item", this.value)'>
    <table class="w3-table-all w3-margin-top w3-margin-bottom" id="filterTable" style="width: 100%;">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 42%;">Nazwa</th>
            <th style="width: 17%;">Czas aktywacji</th>
            <th style="width: 13%;">Czas trwania (min)</th>
            <th style="width: 13%;">Kod testu</th>
            <th style="width: 10%;">Sprawdź</th>
        </tr>
        <?php

            // Zapytanie do bazy danych
            $query = "SELECT `activeted_test`.`id`, `test`.`name`, `activeted_test`.`activation_time`, `test`.`time`, `activeted_test`.`test_code`
                FROM `activeted_test` 
                LEFT JOIN `test` ON `activeted_test`.`test_id` = `test`.`id_test`
                WHERE `activeted_test`.`account_id` = ".$_SESSION["user_id"].";";
            $result = $connection->query($query);
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
                    <td>".$row[4]."</td>
                    <td>
                        <form action='$create_check' method='post'>
                            <input type='hidden'  name='template_id' value='".$row[0]."'>
                            <button class='w3-button w3-blue'>Sprwawdź</button>
                        </form>
                    </td>
                </tr>";
            } 
            if(!$check){
                echo "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
            }
        ?>
    </table>
</div>