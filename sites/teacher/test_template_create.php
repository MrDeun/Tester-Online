<h1>TWORZENIE SZABLONU TESTU</h1>
<div class="w3-container">
    <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST" id="add_template">
        <label for="name"><h3>Nazwa testu</h3></label>
        <input form="add_template" type="text" class="w3-input" id="name" name="name" placeholder="Nazwa testu..">
        <label for="time"><h3>Czas trwania testu (min)</h3></label>
        <input form="add_template" type="number" class="w3-input" id="time" name="time" placeholder="0">
        <label for="categories"><h3>Kategorie pytań</h3></label>
        <table form="add_template" class="w3-table w3-table-all" id="categories" name="categories">
            <tr><th>Nazwa</th><th>Ilość pytań</th></tr>
            <?php

            ?>
            <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST" id="add_cat">
                <tr>
                    <td><input form="add_cat" type="text" class="input" id="name_new" name="name_new" placeholder="Nazwa.."></td>
                    <td><input form="add_cat" type="number" class="input" max="0" value="0"></td>
                </tr>
                <tr>
                    <td colspan="2"><input form="add_cat" type="submit" class="w3-input" value="Dodaj katogorię"></td>
                </tr>
            </form>
        </table>

        
    </form>
</div>