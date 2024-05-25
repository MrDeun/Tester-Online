<h1>TWORZENIE SZABLONU TESTU</h1>
<div class="w3-container">
    <form action="template_sending.php" method="POST" id="add_template">
        <label for="name"><h3>Nazwa testu</h3></label>
        <input type="text" class="w3-input" id="name" name="name" placeholder="Nazwa testu.."  value="<?php if($id !=null)echo $name; ?>" required>
        <label for="time"><h3>Czas trwania testu (min)</h3></label>
        <input type="number" class="w3-input" id="time" name="time" placeholder="0" min="0" max="300" value="<?php if($id !=null)echo $time;else echo "0"?>">
        <label for="groups"><h3>Grupy pytań</h3></label>
        <table id="groups" class="w3-table-all w3-margin-top" style="width:100%;">
                <tr>
                    <th style="width:5%;">ID</th>
                    <th style="width:70%;">Nazwa</th>
                    <th style="width:15%;">Liczba pytań</th>
                    <th style="width:10%;">Usuń</th>
                </tr>
                <?php
                    if($id != null){
                        echo "<input type='hidden' name='question_id' value='$id'>";
                        $i = 0;
                        $query = "SELECT groups.id, groups.name, COUNT(questions.id_question) 
                            FROM groups LEFT JOIN link_group_questions ON groups.id = link_group_questions.group_id 
                            LEFT JOIN questions ON link_group_questions.question_id = questions.id_question 
                            LEFT JOIN link_test_groups ON groups.id = link_test_groups.group_id 
                            WHERE groups.account_id = ".$_SESSION["user_id"]." AND link_test_groups.test_id = $id 
                            GROUP BY groups.id, groups.name 
                            ORDER BY groups.id;";
                        $result = $connection->query($query);                   
                        while ($row = mysqli_fetch_row($result)){
                            $i++;
                            echo "<tr>
                                <td>".$row[0]."</td>
                                <td><input type='hidden' name='groups[]' value='".$row[0]."'>".$row[1]."</td>
                                <td><input type='number' class='w3-input' name='questions_count[]'  min='0' max='".$row[2]."'></td>
                                <td><button type='button' class='w3-button w3-red' onclick='deleteTableRow(this);'>Usuń</button></td>
                            </tr>";
                        }
                    }
                ?>
            </table>

            <select class="w3-select w3-margin-top w3-margin-bottom" id="addGroup">
                <?php                    
                    $query = "SELECT groups.id, groups.name, COUNT(questions.id_question)  
                    FROM groups LEFT JOIN link_group_questions ON groups.id = link_group_questions.group_id 
                    LEFT JOIN questions ON link_group_questions.question_id = questions.id_question 
                    WHERE groups.account_id = ".$_SESSION["user_id"]." 
                    GROUP BY groups.id, groups.name 
                    ORDER BY groups.id;";
                    $result = $connection->query($query); 
                    while ($row = mysqli_fetch_row($result)){                     
                        echo "<option value='".$row[0]."' id='".$row[0]."' name='".$row[1]."' data-max='".$row[2]."'>".$row[1]."</option>";
                            
                    }
                ?>
            </select>
            <button type="button" class="w3-button w3-white w3-margin-top w3-margin-bottom" onclick="addGroupRow('groups', 'addGroup');">Dodaj grupę</button>
            <br>
            <button type="submit" class="w3-button w3-green w3-margin-top w3-margin-bottom">Zapisz</button>  
    </form>
</div>