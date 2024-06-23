<h1>TWORZENIE SZABLONU TESTU</h1>
<div class="w3-container">
    <form action="template_sending.php" method="POST" id="add_template">
        <label for="name"><h3>Nazwa testu</h3></label>
        <input type="text" class="w3-input" id="name" name="name" placeholder="Nazwa testu.." required  value="<?php if($id !=null)echo $name; ?>">
        <label for="time"><h3>Czas trwania testu (min)</h3></label>
        <input type="number" class="w3-input" id="time" name="time" placeholder="0" min="0" max="300" value="0" value="<?php if($id !=null)echo $time;else echo "0"?>">
        
        <label for="groups"><h3>Grupy pytań</h3></label>
        <table id="groups" class="w3-table-all w3-margin-top" style="width:100%;">
            <tr>
                <th style="width:5%;">ID</th>
                <th style="width:70%;">Nazwa</th>
                <th style="width:15%;">Liczba pytań</th>
                <th style="width:10%;">Usuń</th>
            </tr>
            <?php
            // Reset kursora wyniku
            mysqli_next_result($connection);

            if($id != null){
                echo "<input type='hidden' name='question_id' value='$id'>";
                $i = 0;
                $query = "CALL SelectQuestionGroupsForTest(" . $_SESSION["user_id"] . ", " . $id . ")";
                $result = $connection->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $i++;
                        echo "<tr>
                            <td>".$row["id"]."</td>
                            <td><input type='hidden' name='groups[]' value='".$row["id"]."'>".$row["name"]."</td>
                            <td><input type='number' class='w3-input' name='questions_count[]'  min='0' max='".$row["question_count"]."'></td>
                            <td><button type='button' class='w3-button w3-red' onclick='deleteTableRow(this);'>Usuń</button></td>
                        </tr>";
                    }
                }
                // Zwolnienie wyników z poprzedniego zapytania
                mysqli_free_result($result);
            }
            ?>

        </table>

        <select class="w3-select w3-margin-top w3-margin-bottom" id="addGroup">
            <?php

                // Reset kursora wyniku
                mysqli_next_result($connection);
                $result = $connection->query("CALL SelectQuestionGroups(" . $_SESSION["user_id"] . ")");
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='".$row["id"]."' id='".$row["id"]."' name='".$row["name"]."' data-max='".$row["question_count"]."'>".$row["name"]."</option>";
                    }
                }
                
                // Zwolnienie wyników z poprzedniego zapytania
                mysqli_free_result($result);
            ?>
        </select>
        <button type="button" class="w3-button w3-white w3-margin-top w3-margin-bottom" onclick="addGroupRow('groups', 'addGroup');">Dodaj grupę</button>
        <br>
        <button type="submit" class="w3-button w3-green w3-margin-top w3-margin-bottom">Zapisz</button>  
    </form>
</div>
