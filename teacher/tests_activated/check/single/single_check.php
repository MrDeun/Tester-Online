<h1>Sprawdzanie testu: <?php echo $name;?></h1>
<h3>Data testu: <?php echo $activation_time;?></h3>
<h3>Czas trwania testu: <?php echo $time;?> min.</h3>

<h3>Imie i nazwisko ucznia: <?php echo $student_name;?></h3>

<?php
// Uruchom procedurę GetActivatedTestAnswers
mysqli_next_result($connection);
$query = "CALL GetActivatedTestAnswers(?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $student_test_id);
echo $student_test_id;
$stmt->execute();
$result = $stmt->get_result();
$i = 0;
while ($row = mysqli_fetch_row($result)) {
    $answer_id = $row[0];
    $question_text = $row[1];
    $answer_chosen_text = $row[2];
    $i++;
    echo "<h6>$i. \t $question_text \t $answer_chosen_text </h6>";


    // Uruchom procedurę GetAnswerDetails
    mysqli_next_result($connection);
    $query2 = "CALL GetAnswerDetails(?)";
    $stmt2 = $connection->prepare($query2);
    $stmt2->bind_param("i", $answer_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row2 = mysqli_fetch_row($result2)) {
        // Przetwórz wynik procedury GetAnswerDetails
        // Na przykład, możesz wyświetlić odpowiedź
        echo "Answer: " . $row2[0] . "<br>";
    }
    $stmt2->close(); // Zamknięcie wewnętrznego statementu
}

