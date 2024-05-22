<?php
if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
    redirectToIndex("/teacher/tests_templates/create");
}
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include('../../../login_sql.php');
$id = 0;
$name = "";
$time = 0;
$groups;
$questions_count;

if(isset($_POST["template_id"])){
    $id = $_POST["template_id"];
}
$name = $_POST["name"];
$time = $_POST["time"];
if(isset($_POST["groups"])){
    $groups = $_POST["groups"];
}
if(isset($_POST["questions_count"])){
    $questions_count = $_POST["questions_count"];
}

if($id == 0){
    $query = "INSERT INTO `test` (`name`, `time`, `account_id`) VALUES ( ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sii", $name, $time, $_SESSION["user_id"]);
    $stmt->execute();

    $id = $stmt->insert_id;

    foreach ($groups as $key => $group) {
        $query = "INSERT INTO `link_test_groups` (`id`, `test_id`, `group_id`, `question_count`) VALUES (NULL, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iii", $id, $group, $questions_count[$key]);
        $stmt->execute();
    }
}
elseif($id >= 0){
    $query = "UPDATE `test` SET `name` = ?, `time` = ? WHERE `id_test` = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sii", $name, $time, $id);
    $stmt->execute();
    
    $query = "DELETE FROM `link_test_groups` WHERE `test_id` = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    foreach ($groups as $key => $group) {
        $query = "INSERT INTO `link_test_groups` (`id`, `test_id`, `group_id`, `question_count`) VALUES (NULL, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iii", $id, $group, $questions_count[$key]);
        $stmt->execute();
    }
}
$connection->close();
include("../../../functions.php");
redirectToIndex("/teacher/tests_templates");