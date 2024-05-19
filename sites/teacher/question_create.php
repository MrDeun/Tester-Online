<div class="w3-container w3-margin-bottom w3-row">
    <h1>TWORZENIE PYTANIA</h1>
    <?php 
        $id = null; 
        $text = "";
        $open = false;
        if(isset($_POST["question_id"])){
            $id =  $_POST["question_id"];
        }
    ?>
    <form method="post" class="w3-container w3-left-align">
        <input type="hidden" name="under_site" value="question_sending">
        <div class="w3-margin-top">
            <label for="textQuestion"><h3>Treść pytania</h3></label>
            <textarea class="w3-input w3-animate-input" name="textQuestion" id="textQuestion" style="width:30%" placeholder="Treść pytania.."><?php if($text != ""){echo $text;} ?></textarea>
        </div>
        <div class="w3-margin-top">
            <label for="groups"><h3>Grupy</h3></label>
            <table id="groups" class="w3-table-all w3-margin-top" style="width:100%;">
                <tr>
                    <th style="width:15%;">ID</th>
                    <th style="width:70%;">Nazwa</th>
                    <th style="width:15%;">Usuń</th>
                </tr>
                <?php
                    if($id != null){
                        $i = 0;
                        $query = "SELECT groups.id AS ID, groups.name AS Name 
                            FROM groups LEFT JOIN link_group_questions ON groups.id = link_groups_questions.group_id
                            WHERE link_group_questions.question.id = $id
                            ORDER BY groups.id;";
                        $result = $connection->query($query);                       
                        while ($row = mysqli_fetch_row($result)){
                            $i++;
                            echo "<tr>
                                <td>".$row[0]."</td>
                                <td><input type='hidden' name='groups[]' id='group_$i' value='".$row[1]."'>".$row[1]."</td>
                                <td><button type='button' class='w3-button w3-red' onclick='deleteTableRow(this);'>Usuń</button></td>
                            </tr>";
                        }
                    }
                ?>
            </table>

            <select class="w3-select w3-margin-top w3-margin-bottom" id="addGroup">
                <?php                    
                    $query = "SELECT groups.id AS ID, groups.name AS Name FROM groups ORDER BY groups.id;";
                    $result = $connection->query($query); 
                    while ($row = mysqli_fetch_row($result)){                     
                        echo "<option value='".$row[0]."'>".$row[1]."</option>";
                            
                    }
                ?>
            </select>
            <button type="button" onclick="addGroupRow('groups', 'addGroup');">Dodaj grupę</button>

        </div>
        <div class="w3-margin-top">
            <h3>
                <label for="openCheck">Czy pytanie jest otwarte?</label>
                <input type="checkbox" name="openCheck" id="openCheck" class="w3-check" value="open" onchange="openQuestionToggle('closeQuestion','openQuestion', 'openCheck');" <?php if($open)echo "checked"; ?>>       
            </h3>
        </div>
        <div id="closeQuestion"  class="w3-margin-top w3-show">
            <h3>
                <label for="points">Liczba punktów za pytanie: </label>
                <input type="number" name="points" id="points" min="0" max="1000" maxlength="4">
            </h3>
            <h3>
                Odpowiedzi
            </h3>
            <table id="answers" class="w3-table-all w3-margin-top" style="width:100%;">
                <tr>
                    <th style="width:10%;">ID</th>
                    <th style="width:70%;">Treść</th>
                    <th style="width:5%;">Poprawne?</th>
                    <th style="width:15%">Usuń</th>
                </tr>
                <?php
                    if($id != null){
                        $i = 0;
                        $query = "SELECT answers.answer_id, AS ID, answers.text AS TEXT, answers.correct AS Correct FROM answers WHERE answers.question_id = $id";
                        $result = $connection->query($query);
                        while ($row = mysqli_fetch_row($result)){
                            $i++;
                            echo "<tr>
                                <td>".$row[0]."</td>
                                <td><input type='hidden' name='answers[]' id='answer_$i' value='".$row[1]."'>".$row[1]."</td>
                                <td>
                                    <input type='hidden' name='answer_id[]' id='answer_id_$i' value='".$row[0]."'>
                                    <input type='checkbox' class='w3-check' name='answers_correct[]' value='".$row[0]."'>
                                </td>
                                <td><button type='button' class='w3-button w3-red' onclick='deleteTableRow(this);'>Usuń</button></td>
                            </tr>";
                        }
                    }
                ?>
            </table>
            <input id="addAnswer" class="w3-input w3-margin-top w3-margin-bottom" placeholder="Treść odpowiedzi..">
            <button type="button" onclick="addAnswerRow('answers', 'addAnswer');">Dodaj odpowiedź</button>
        </div>
        <br>
        <button type="submit" name="questionButton" class="w3-button w3-green">Zapisz</button>
    </form>

</div>