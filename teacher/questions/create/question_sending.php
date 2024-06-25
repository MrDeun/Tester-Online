<?php
    if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
        redirectToIndex("/teacher/questions/create");
    }
    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }
    include('../../../login_sql.php');
    $id = 0;
    $text = "";
    $groups;
    $open = false;
    $points = 0;
    $answers;
    $answers_correct = null;
    $answers_id;
    $answers_text;
    
    // Pobierz dane z formularza POST
    if(isset($_POST["question_id"])){
        $id = $_POST["question_id"];
    }
    $text = $_POST["textQuestion"];
    if(isset($_POST["groups"])){
        $groups = $_POST["groups"];
    }   
    $points = $_POST["points"];
    if (isset($_POST["openCheck"]) && $_POST["openCheck"] == 'open') {
        $open = true;
    }
    if (isset($_POST["answers_correct"]) && is_array($_POST["answers_correct"])) {
        $answers_correct = $_POST["answers_correct"];
        echo "OK";
    }
    if (isset($_POST["answer_id"]) && is_array($_POST["answer_id"])) {
        $answers_id = $_POST["answer_id"];
    }
    if (isset($_POST["answers_text"]) && is_array($_POST["answers_text"])) {
        $answers_text = $_POST["answers_text"];
        
    }
    
    if($id == 0){
        // Tworzenie nowego pytania
        $query = "INSERT INTO `questions` (`opened`, `text`, `points`, `account_id`) VALUES ( ?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("isii", $open, $text, $points, $_SESSION["user_id"]);
        $stmt->execute();
        
        // Pobierz nowy ID pytania
        $id = $stmt->insert_id;
        
        // Dodawanie grup do pytania
        foreach ($groups as $group) {
            $query = "INSERT INTO `link_groups_questions` (`id`, `question_id`, `group_id`) VALUES (NULL, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ii", $id, $group);
            $stmt->execute();
        }
        
        if(!$open){
            // Dodawanie odpowiedzi do pytania
            foreach ($answers_text as $key => $answer) {              
                if($answers_correct != null){
                    $correct = in_array($key + 1, $answers_correct) ? 1 : 0;
                }
                else{
                    $correct = 0;
                }
                $query = "INSERT INTO `answers` (`id`, `text`, `correct`, `question_id`) VALUES (NULL, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sii", $answer, $correct, $id);
                $stmt->execute();
            }
        }
    }
    elseif ($id >= 0) {
        // Aktualizacja istniejącego pytania
        $query = "UPDATE `questions` SET `opened` = ?, `text` = ?, `points` = ? WHERE `id` = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("isii", $open, $text, $points, $id);
        $stmt->execute();
    
        // Aktualizacja grup do pytania - najpierw usuń stare, potem dodaj nowe
        $query = "DELETE FROM `link_groups_questions` WHERE `question_id` = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        foreach ($groups as $group) {
            $query = "INSERT INTO `link_groups_questions` (`id`, `question_id`, `group_id`) VALUES (NULL, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ii", $id, $group);
            $stmt->execute();
        }
    
        // Aktualizacja odpowiedzi do pytania tylko, gdy $open jest fałszem
        if (!$open) {
            // Usuń wszystkie odpowiedzi jeśli $open jest fałszem
            $query = "UPDATE `answers` SET `deleted` = 1 WHERE `question_id` = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            // Dodawanie nowych odpowiedzi z formularza
            foreach ($answers_text as $key => $answer) {              
                if($answers_correct != null){
                    $correct = in_array($key + 1, $answers_correct) ? 1 : 0;
                }
                else{
                    $correct = 0;
                }
                $query = "INSERT INTO `answers` (`id`, `text`, `correct`, `question_id`) VALUES (NULL, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sii", $answer, $correct, $id);
                $stmt->execute();
            }
        }
    }
    
    $connection->close();
    include("../../../functions.php");
    redirectToIndex("/teacher/questions");
    
