<div class="w3-container">
    <form class="w3-container w3-center w3-margin-top" action="./create" method="POST">
        <button class="w3-button w3-aqua ">NOWY SZABLONY TESTÓW</button>
    </form>
</div>
<div class="w3-container w3-margin-top">
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj tabelę.." onkeyup='w3.filterHTML("#filterTable", ".item", this.value)'>
    <table class="w3-table-all w3-margin-top w3-margin-bottom" id="filterTable" style="width: 100%;">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 45%;">Nazwa</th>
            <th style="width: 14%;">Czas trwania (min)</th>
            <th style="width: 9%;">Pytania</th>
            <th style="width: 9%;">Punkty</th>
            <th style="width: 9%;">Edytuj</th>
            <th style="width: 9%;">Usuń</th>
            
        </tr>
        <?php

            // Zapytanie do bazy danych
            $sql = "CALL GetTestDetails(?)";
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
                    <td>".$row[4]."</td>
                    <td>
                        <form action='$create_template' method='post'>
                            <input type='hidden'  name='template_id' value='".$row[0]."'>
                            <button class='w3-button w3-blue'>Edytuj</button>
                        </form>
                    </td>
                    <td>
                        <form method='post'>
                            <input type='hidden'  name='template_id' value='".$row[0]."'>
                            <button class='w3-button w3-red' name='deleteTemplate'>Usuń</button>
                        </form>
                    </td>
                </tr>";
            } 
            if(!$check){
                echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            }
        ?>
    </table>
</div>