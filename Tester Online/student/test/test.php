<?php
$student_id = 0;
$test_code = "";
$activated_test_id = 0;
$activation_time;
$time = 0;
$test_name = "";
$test_id = 0;
$now = new DateTime();

if(isset($_POST["test_code"])){
    $test_code = $_POST["test_code"];
}
else {
    redirectToIndex("student");
}

$student_id = $_SESSION["user_id"];

$query = "SELECT id, activation_time, test_id FROM activated_tests WHERE test_code = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $test_code);
$stmt->execute();
$result = $stmt->get_result();
$b = mysqli_num_rows($result);
if($b == 1){
    $row = mysqli_fetch_row($result);
    $activated_test_id = $row[0];
    $activation_time = new DateTime($row[1]);  
    $test_id = $row[2];
}
else{
    redirectToIndex("student");
}
$stmt->close();


if($activation_time > $now){
    echo "<h1>TEST JESZCZE SIĘ NIE AKTYWOWAŁ</h1>";
}
else{
    $query = "SELECT link_account_activated_tests_answer.id FROM link_account_activated_tests
        JOIN link_account_activated_tests_answer ON link_account_activated_tests.id = link_account_activated_tests_answer.link_account_activated_test_id
        WHERE activated_test_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $activated_test_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $a = mysqli_num_rows($result);
    $stmt->close();
    if($a > 0){
        echo "<h1>TEST ZOSTAŁ JUŻ WYKONANY</h1>";
    }
    else{
        echo "<form action='test_send.php' method='post'>"; 
        echo "<input type='hidden' name='activated_test_id' value='$activated_test_id'>";
        $query = "SELECT name FROM tests WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $test_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_row($result);
            $test_name = $row[0]; 
        }
        echo "<h1>$test_name</h1>";
        $stmt->close();

        $query = "CALL AssignTestToStudent(?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $activated_test_id, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $i = 0;
        while($row = mysqli_fetch_row($result)){
            $i++;
            $question_id = $row[0];
            mysqli_next_result($connection);
            $query2 = "SELECT text, opened FROM questions WHERE id = ?";
            $stmt2 = $connection->prepare($query2);
            $stmt2->bind_param("i",$question_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $row2 = mysqli_fetch_row($result2);
            
            $question_text = $row2[0];
            $open = $row2[1];
            mysqli_free_result($result2);
            echo "<h5>$i. $question_text</h5>";
            echo "<input type='hidden' name='question_open[]' value='$open'>";
            echo "<input type='hidden' name='question_id[]' value='$question_id'>";
            if($open == 1){
                echo "<textarea name='answer_".$i."[]' class='w3-input'></textarea>";
            }
            else{
                $query2 = "SELECT id, text FROM answers WHERE question_id = ? AND deleted = 0";
                $stmt2 = $connection->prepare($query2);
                $stmt2->bind_param("i",$question_id);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                while($row2 = mysqli_fetch_row($result2)){
                    $answer_id = $row2[0];
                    $answer_text = $row2[1];
                    echo "<p>$answer_text <input type='checkbox' class='w3-check' name='answer_".$i."[]' value='$answer_id'></p>"; 
                }
            }
            
        }
        echo "<button type='submit' class='w3-button w3-green'> Zapisz </button> 
            </form>";
    }

    
}
