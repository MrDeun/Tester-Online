<div class="w3-container">
    <form class="w3-container w3-center w3-margin-top" action="./create" method="POST">
        <input type="hidden" name="under_site" value="test_template_create">
        <button class="w3-button w3-aqua ">NOWY SZABLONY TESTÓW</button>
    </form>
</div>
<div class="w3-container w3-margin-top">
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Filtruj tabelę.." onkeyup='w3.filterHTML("filterTable", ".item", this.value)'>
    <table class="w3-table-all w3-margin-top" id="filterTable" style="width: 100%;">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 45%;">Nazwa</th>
            <th style="width: 20%;">Czas trwania (min)</th>
            <th style="width: 10%;">Pytania</th>
            <th style="width: 10%;">Punkty</th>
            <th style="width: 10%;">Edytuj</th>
        </tr>
        <?php

            // Zapytanie do bazy danych
            $query = "SELECT test.id_test AS ID, test.name AS Name, test.time AS Time, COUNT(questions.id_question) AS Questions, SUM(questions.points) AS Points
                FROM test JOIN link_test_groups ON test.id_test = link_test_groups.test_id
                JOIN groups ON link_test_groups.group_id = groups.id
                JOIN link_group_questions ON groups.id = link_group_questions.group_id
                JOIN questions ON link_group_questions.question_id = questions.id_question
                WHERE test.id_test NOT IN (
                    SELECT test.id_test
                    FROM test
                    WHERE test.account_id <> ".$_SESSION["user_id"]."
                )
                GROUP BY test.id_test, test.name, test.time;";
            $result = $connection->query($query);

            if ($result->num_rows > 0) {
                // Wyświetlenie danych w tabeli
                echo "<tr class='item'>
                    <td>".$row["ID"]."</td>
                    <td>".$row["Name"]."</td>
                    <td>".$row["Time"]."</td>
                    <td>".$row["Questions"]."</td>
                    <td>".$row["Poits"]."</td>
                    <td>
                        <form action='index.php' method='post'>
                            <input type='hidden' name='site' value='activated_test'>
                            <input type='hidden' name='test_id' value='".$row["ID"]."'>.
                            <buttom>Edytuj</buttom></td>
                </tr>";
            } else {
                echo "<tr><td></td><td></td><td></td><td></td><td></td><td></th>";
            }
        ?>
    </table>
</div>