<h1>Sprawdzanie testu: <?php echo $name;?></h1>
<h3>Data testu: <?php echo $activation_time;?></h3>
<h3>Czas trwania testu: <?php echo $time;?> min.</h3>

<h3>Imie i nazwisko ucznia: <?php echo $student_name;?></h3>

<?php
// Uruchom procedurę GetActivatedTestQuestions
mysqli_next_result($connection);
$query = "CALL GetActivatedTestQuestions(?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $student_test_id);
echo $student_test_id;
$stmt->execute();
$result = $stmt->get_result();
$i = 0;
while ($row = mysqli_fetch_row($result)) {
    $question_text = $row[0];
    $question_id = $row[1];
    $question_open = $row[2];
    $question_points = $row[3];
    $i++;
    echo "<div>$i. \t $question_text \t  Punkty: $question_points";

    // Uruchom procedurę GetActivatedTestAnswer
    mysqli_next_result($connection);
    $query2 = "CALL GetActivatedTestAnswer(?, ?)";
    $stmt2 = $connection->prepare($query2);
    $stmt2->bind_param("ii", $question_id, $student_test_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $answers = [];
    while ($row2 = mysqli_fetch_row($result2)) {
        $answers[] = [
            'answer_chosen_text' => $row2[0],
            'correct' => $row2[1],
            'id' => $row2[2],
            'points' => $row2[3]
        ];
        
    }
    $stmt2->close(); // Zamknięcie wewnętrznego statementu

    // Uruchom procedurę GetAnswerDetails
    mysqli_next_result($connection);
    $query3 = "CALL GetAnswerDetails(?)";
    $stmt3 = $connection->prepare($query3);
    $stmt3->bind_param("i", $question_id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    while ($row3 = mysqli_fetch_row($result3)) {
        $x = false; $y = false;
        // Iteracja przez wszystkie odpowiedzi w tablicy $answers
        foreach ($answers as $answer) {
            $answer_chosen_text = $answer['answer_chosen_text'];
            if($question_open == 1){
                if($answer_chosen_text == $row3[0]){
                    $answer_id = $answer['id'];
                    if($answer['points'] != NULL)$points = $answer['points'];
                    else $points = 0;
                    echo "<p><textarea readonly>".$row3[0]."</textarea></p>";
                    echo "<label for='points'>Punkty: </label>";
                    echo "<form method='post'>
                            <input type='number' name='points' id='points' class='w3-input' value='$points'>
                            <input type='hidden' name='answer_id' value='$answer_id'>
                            <button type='submit'>Zapisz</button>
                        </form>";
                    break;
            
                }
            }
            else{

                $correct = $answer['correct'];
                // Przetwórz wynik procedury GetAnswerDetails
                
                if ($answer_chosen_text == $row3[0]) {
                    $y = true;
                    if ($correct == 1) {
                        $x = true;
                        break;
                    }
                }
                
            }
            
        }
        if($question_open == 0){
            echo "<p ";
            if($y){
                echo "class='";
                if($x){
                    echo "w3-green";
                }
                else{
                    echo "w3-red";
                }
                echo "'";
            }
            echo ">";
            echo $row3[0];
            echo "</p>";
        }
    }
    $stmt3->close(); // Zamknięcie wewnętrznego statementu
    echo "</div>";

    
}

if (isset($_POST['answer_id']) && isset($_POST['points'])) {
    $answer_id = $_POST['answer_id'];
    $points = $_POST['points'];

    $query = "UPDATE link_account_activated_tests_answer SET points = ? WHERE answer_id = ?";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $points, $answer_id);
    $stmt->execute();
}