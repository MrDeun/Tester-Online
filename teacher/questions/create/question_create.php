<div class="w3-container w3-margin-bottom w3-row">
    <h1>TWORZENIE PYTANIA</h1>
    <form method="post" class="w3-container w3-left-align" action="question_sending.php">
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
                        echo "<input type='hidden' name='question_id' value='$id'>";
                        $i = 0;
                        $query = "SELECT groups.id AS ID, groups.name AS Name 
                            FROM groups LEFT JOIN link_groups_questions ON groups.id = link_groups_questions.group_id
                            WHERE link_groups_questions.question_id = $id
                            ORDER BY groups.id;";
                        $result = $connection->query($query);                   
                        while ($row = mysqli_fetch_row($result)){
                            $i++;
                            echo "<tr>
                                <td>".$row[0]."</td>
                                <td><input type='hidden' name='groups[]' id='group_$i' value='".$row[0]."'>".$row[1]."</td>
                                <td><button type='button' class='w3-button w3-red' onclick='deleteTableRow(this);'>Usuń</button></td>
                            </tr>";
                        }
                    }
                ?>
            </table>

            <select class="w3-select w3-margin-top w3-margin-bottom" id="addGroup">
                <?php                    
                    $query = "SELECT groups.id AS ID, groups.name AS Name FROM groups WHERE groups.account_id = ".$_SESSION["user_id"]." ORDER BY groups.id;";
                    $result = $connection->query($query); 
                    while ($row = mysqli_fetch_row($result)){                     
                        echo "<option value='".$row[0]."' id='".$row[0]."' name='".$row[1]."'>".$row[1]."</option>";
                            
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
        <div id="closeQuestion"  class="w3-margin-top <?php if($open)echo "w3-hide"; else echo "w3-show"?>">
            <h3>
                <label for="points">Liczba punktów za pytanie: </label>
                <input type="number" name="points" id="points" min="0" max="1000" maxlength="4" <?php if($points != 0){echo "value='$points'";}else {echo "value='0'";} ?>>
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
                        $query = "SELECT answers.id, answers.text, answers.correct FROM answers WHERE answers.question_id = $id AND answers.deleted != 1";
                        $result = $connection->query($query);
                        while ($row = mysqli_fetch_row($result)){
                            $correct = "";
                            if((bool)$row[2]){
                                $correct = "checked";
                            }
                            $i++;
                            echo "<tr>
                                <td>".$row[0]."</td>
                                <td><input type='hidden' name='answers[]' id='answer_$i' value='".$row[1]."'>".$row[1]."</td>
                                <td>
                                    
                                    <input type='checkbox' class='w3-check' name='answers_correct[]' value='".$i."' $correct>
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