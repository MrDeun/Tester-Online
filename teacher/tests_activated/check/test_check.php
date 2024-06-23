<h1>Sprawdzanie testu: <?php echo $name;?></h1>
<h3>Data testu: <?php echo $activation_time;?></h3>
<h3>Czas trwania testu: <?php echo $time;?> min.</h3>

<div class="w3-container">
    <table class="w3-table-all w3-margin-top w3-margin-bottom">
        <tr>
            <th style="width:40%">Uczeń</th>
            <th style="width:30%">Liczba punktów</th>
            <th style="width:10%">Sprawdzone?</th>
            <th style="width:20%">Sprawdź</th>
        </tr>

        <?php
            // Reset kursora wyniku
            mysqli_next_result($connection);

            $query = "CALL GetTestAccountData($id);";
            $result = $connection->query($query);
            $check = false;
            while($row = mysqli_fetch_row($result)){
                $check = true;
                $checked = "";
                if($row[3] == 1){
                    $checked = "checked";
                }
                // Wyświetlenie danych w tabeli
                echo "<tr class='item'>
                    <td>".$row[0]." ".$row[1]."</td>
                    <td>".$row[2]."</td>
                    <td>
                        <input type='checkbox' readonly $checked>
                    </td>
                    <td>
                        <form action='$create_check' method='post'>
                            <input type='hidden'  name='student_name' value='".$row[0]." ".$row[1]."'>
                            <input type='hidden'  name='student_test_id' value='".$row[4]."'>
                            <input type='hidden'  name='activated_id' value='$id'>
                            <button type='submit' class='w3-button w3-blue'>Sprawdź</button>
                        </form>
                    </td>
                </tr>";
            } 
            if(!$check){
                echo "<tr><td></td><td></td><td></td><td></td></tr>";
            }
        ?>


    </table>
</div>