<?php
    include('../../../login_sql.php');
    $id = 0;
    $text = "";
    $groups;
    $open = false;
    $points = 0;
    $answers;
    $answers_correct;
    $answers_id;
    
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
    }
    if (isset($_POST["answer_id"]) && is_array($_POST["answer_id"])) {
        $answers_id = $_POST["answer_id"];
    }
    
    if($id == 0){
        // Tworzenie nowego pytania
        $query = "INSERT INTO `questions` (`id_question`, `opened`, `text`, `points`) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iisi", $id, $open, $text, $points);
        $stmt->execute();
        
        // Pobierz nowy ID pytania
        $id = $stmt->insert_id;
        
        // Dodawanie grup do pytania
        foreach ($groups as $group) {
            $query = "INSERT INTO `link_group_questions` (`id`, `question_id`, `group_id`) VALUES (NULL, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ii", $id, $group);
            $stmt->execute();
        }
        
        if(!$open){
            // Dodawanie odpowiedzi do pytania
            foreach ($_POST["answers"] as $key => $answer) {
                $correct = in_array($key, $answers_correct) ? 1 : 0;
                $query = "INSERT INTO `answers` (`answer_id`, `text`, `correct`, `question_id`) VALUES (NULL, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sii", $answer, $correct, $id);
                $stmt->execute();
            }
        }
    }
    if ($id != 0) {
        // Aktualizacja istniejącego pytania
        $query = "UPDATE `questions` SET `opened` = ?, `text` = ?, `points` = ? WHERE `id_question` = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("isii", $open, $text, $points, $id);
        $stmt->execute();
    
        // Aktualizacja grup do pytania - najpierw usuń stare, potem dodaj nowe
        $query = "DELETE FROM `link_group_questions` WHERE `question_id` = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        foreach ($groups as $group) {
            $query = "INSERT INTO `link_group_questions` (`id`, `question_id`, `group_id`) VALUES (NULL, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ii", $id, $group);
            $stmt->execute();
        }
    
        // Aktualizacja odpowiedzi do pytania tylko, gdy $open jest fałszem
        if (!$open) {
            // Usuń wszystkie odpowiedzi jeśli $open jest fałszem
            $query = "DELETE FROM `answers` WHERE `question_id` = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $i = 0;
            // Dodawanie nowych odpowiedzi z formularza
            foreach ($_POST["answers"] as $key => $answer) {              
                $correct = in_array($key + 1, $answers_correct) ? 1 : 0;
                $query = "INSERT INTO `answers` (`answer_id`, `text`, `correct`, `question_id`) VALUES (NULL, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sii", $answer, $correct, $id);
                $stmt->execute();
                $i++;
            }
        }
    }
    
    $connection->close();
    include("../../../functions.php");
    redirectToIndex("/teacher/questions");
    
