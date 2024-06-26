<?php
if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
    redirectToIndex("student/");
}
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../functions.php");
include('../../login_sql.php');

checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
if($_SESSION["logged"] != 3){
    clear_session_except();
    redirectToIndex("/");
}

$student_id = $_SESSION["user_id"];
$link_acccount_activated_tests_id = 0;
$questions_open = $_POST["question_open"];
$questions_id = $_POST["question_id"];
$size_of_arrays = sizeof($questions_open);
$answers = array();
for ($i=0; $i <= $size_of_arrays; $i++) { 
    if (isset($_POST["answer_".($i+1)])) {
        $answers[$i] = $_POST["answer_".($i+1)];
    }
    else{
        $answers[$i] = null;
    }
    
}
$activated_test_id = $_POST["activated_test_id"];



$query = "SELECT id FROM link_account_activated_tests
    WHERE account_id = ? AND activated_test_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ii", $student_id, $activated_test_id);
$stmt->execute();
$result = $stmt->get_result();
$link_acccount_activated_tests_id = mysqli_fetch_row($result)[0];
$stmt->close();
for ($i=0; $i < $size_of_arrays; $i++) { 
    if($answers[$i] == null){
        continue;
    }
    $question_id = $questions_id[$i];
    $answer_id = 0;
    if($questions_open[$i] == 1){
        $answer_text = $answers[$i][0];
        $query = "INSERT INTO `answers` (`text`, `question_id`) VALUES (?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("si", $answer_text, $question_id);
        $stmt->execute();
        $answer_id = $stmt->insert_id;
        $stmt->close();
        $query = "INSERT INTO `link_account_activated_tests_answer` (`link_account_activated_test_id`, `answer_id`, `question_id`) 
            VALUES (?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iii", $link_acccount_activated_tests_id, $answer_id, $question_id);
        $stmt->execute();
    }
    else{
        
        foreach ($answers[$i] as $answer) {
            $answer_id = $answer;
            $query = "INSERT INTO `link_account_activated_tests_answer` (`link_account_activated_test_id`, `answer_id`, `question_id`) 
                VALUES (?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("iii", $link_acccount_activated_tests_id, $answer_id, $question_id);
            $stmt->execute();
        }
    }
    
}
redirectToIndex("student");






