<?php 
    if(isset($_POST["deleteRow"])){
        $query = "DELETE FROm `groups` WHERE `id` = ".$_POST["group_id"].";";
        $connection->query($query);
    }
    if(isset($_POST["addGroupButton"])){
        $name = $_POST["addGroupName"];
        $query = "INSERT INTO `groups` (`id`, `name`, `account_id`) VALUES (NULL, '$name', ".$_SESSION["user_id"].");";
        $connection->query($query);
    }
?>
<div class="w3-container">
    <h1>Grupy pytań</h1>
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj grupy.." onkeyup='w3.filterHTML("groupsTable", ".groupItem", this.value)'>
    <table class="w3-table-all w3-margin-top w3-centered" id="groupsTable" style="width: 100%;">
        <tr>
            <th style="width: 10%;">ID</th>
            <th style="witdh: 605%;">Nazwa</th>
            <th style="width: 15%;">Liczba pytań</th>
            <th style="width: 15%;">Usuń</th>
        </tr>
        <?php
            
            $check = false;
            $query = "SELECT groups.id AS ID, groups.name AS Name, COUNT(questions.id_question) AS Questions 
                FROM groups LEFT JOIN link_group_questions ON groups.id = link_group_questions.group_id
                LEFT JOIN questions ON link_group_questions.question_id = questions.id_question 
                WHERE groups.account_id = ".$_SESSION["user_id"]." 
                GROUP BY groups.id, groups.name;";
            $result = $connection->query($query);

            while ($row = mysqli_fetch_row($result)){
                $check = true;
                echo "<tr class='groupItem'>
                    <td>".$row[0]."</td>
                    <td>".$row[1]."</td>
                    <td>".$row[2]."</td>
                    <td><form method='post'><input type='hidden' name='group_id' value='".$row[0]."'><button name='deleteRow' type='submit'>Usuń</button</form></td>
                </tr>";
            }
            
            if(!$check){
                echo "<tr class='groupItem'><td></td><td></td><td></td><td></td></tr>";
            }
            $check = false;
        ?>
    </table>
    
    <form class="w3-container" method="post">
        <h4>Dodaj grupę</h4>
        <input type="text" class="w3-input" name="addGroupName" placeholder="Nazwa grupy..">
        <button type="submit" name="addGroupButton">Dodaj</button>
    </form>
    <?php
        
    ?>
</div>
<div>
    <h1>Pytania</h1>
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj pytania.." onkeyup='w3.filterHTML("questionsTable", ".guestionItem", this.value)'>
    <table class="w3-table-all w3-margin-top w3-centered" id="questionsTable" style="width: 100%;">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 10%;">Grupa</th>
            <th style="width: 70%;">Treść</th>                      
            <th style="width: 10%;">Punkty</th>
            <th style="width: 15%;">Edytuj</th>
        </tr>
        <?php
            $query = "SELECT questions.id_question AS ID, questions.text AS Text, questions.points AS Points FROM `questions`;";
            $result = $connection->query($query);

            while ($row = mysqli_fetch_row($result)){
                $check = true;
                $query2 = "SELECT group_id AS GroupID FROM link_group_questions WHERE question_id = ".$row[0].";";
                $result2 = mysqli_query($connection,$query2);

                
                echo "<tr class='questionItem'>
                    <td>".$row[0]."</td>  
                    <td>";
                while ($row2 = mysqli_fetch_row($result2)) {
                    echo $row2[0].", ";
                }
                
                echo "</td>
                    <td>".$row[1]."</td>
                    <td>".$row[2]."</td>
                    <td>
                        <form method='post' action='$create_question'>
                            <input type='hidden' name='question_id' value='".$row[0]."'>
                            <input type='hidden' name='under_site' value='question_create'>
                            <button type='submit'>Edytuj</button>
                        </form>
                    </td>
                </tr>";
            }
            if(!$check){
                echo "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
            }
            $check = false;
        ?>      
    </table>
    <form class="w3-container w3-margin-top" method="post">
        <input type='hidden' name='under_site' value='question_create'>
        <button type="submit" name="addQuestionButton">Dodaj pytanie</button>
    </form>
</div>