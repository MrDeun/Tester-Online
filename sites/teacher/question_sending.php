<?php
    $id = 0;
    $text = "";
    $groups = [];
    $open = false;
    $points = 0;
    $answers = [];
    $answers_correct = [];
    $answer_ids = [];
    
    // Pobierz dane z formularza POST
    $text = $_POST["textQuestion"];
    $groups = $_POST["groups"];
    $points = $_POST["points"];
    if (isset($_POST["openCheck"]) && $_POST["openCheck"] == 'open') {
        $open = true;
    }
    if (isset($_POST["answers_correct"]) && is_array($_POST["answers_correct"])) {
        $answers_correct = $_POST["answers_correct"];
    }
    
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
    
    // Dodawanie odpowiedzi do pytania
    foreach ($_POST["answers"] as $key => $answer) {
        $correct = in_array($key, $answers_correct) ? 1 : 0;
        $query = "INSERT INTO `answers` (`answer_id`, `text`, `correct`, `question_id`) VALUES (NULL, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sii", $answer, $correct, $id);
        $stmt->execute();
    }
    
    
