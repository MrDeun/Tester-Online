<h1>AKTYWACJA TESTU</h1>
<div class="w3-container">
    <form action="activated_sending.php" method="POST" id="add_activated">
        <label for="test"><h3>Wybierz test do aktywacji</h3></label> 
        <select class="w3-select w3-margin-top w3-margin-bottom" name="test" required>
            <?php                    
                $query = "SELECT `tests`.`id`, `tests`.`name`
                FROM `tests`
                WHERE `tests`.`account_id` = ".$_SESSION["user_id"].";";
                $result = $connection->query($query); 
                while ($row = mysqli_fetch_row($result)){                     
                    echo "<option value='".$row[0]."' id='".$row[0]."' name='".$row[1]."'>".$row[0].". ".$row[1]."</option>";
                            
                }
            ?>
        </select>
        <label for="activation_time"><h3>Czas aktywacji</h3></label>   
        <input type="datetime-local" class="w3-input w3-margin-bottom" name="activation_time" id="activation_time" min="<?php echo date('Y-m-d\TH:i'); ?>" max="2100-12-12T00:00" required>
        <button type="submit" class="w3-button w3-green w3-margin-top w3-margin-bottom">Zapisz</button>
    </form>
</div>