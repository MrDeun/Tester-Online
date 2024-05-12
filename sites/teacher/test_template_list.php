<br>
<form class="w3-container w3-center" action="index.php" method="POST">
    <input type="hidden" name="under_site" value="test_template_create">
    <button class="w3-button w3-aqua ">NOWY SZABLONY TESTÓW</button>
</form>
<div class="w3-container">
    <p>Podaj nazwę testu</p>
    <input class="w3-input w3-border w3-padding" type="text" placeholder="Szukaj nazw testów.." id="filterInput" onkeyup="myFunction()">
    <table class="w3-table-all w3-margin-top" id="myTable">
        <tr><th>ID</th><th>Nazwa</th><th>Czas trwania</th><th>Pytania</th><th>Punkty</th><th>Edytuj</th></tr>
        <?php

            // Zapytanie do bazy danych
            $query = "SELECT test.id_test AS ID, test.name AS Name, test.time AS Time, COUNT(questions.id_question) AS Questions, SUM(questions.points) AS Points
                FROM test JOIN link_test_question ON test.id_test = link_test_question.test_id
                JOIN questions ON link_test_question.question_id = questions.id_question
                WHERE test.id_test NOT IN (
                    SELECT test.id_test
                    FROM test
                    WHERE test.account_id <> ".$_SESSION["user_id"]."
                )
                GROUP BY test.id_test, test.name, test.time;";
            $result = $connection->query($query);

            if ($result->num_rows > 0) {
                // Wyświetlenie danych w tabeli
                echo "<tr>
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
                </th>";
            } else {
                echo "<tr><td></td><td></td><td></td><td></td><td></td><td></th>";
            }
        ?>
    </table>
</div>