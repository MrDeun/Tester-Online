<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../functions.php");
checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
if($_SESSION["logged"] != 2){
    clear_session_except();
    redirectToIndex("/");
}
clear_session_except(["logged","user_id"]);
include('../../login_sql.php');
$title = "PYTANIA";
$create_question = getTesterOnlinePath() . "teacher/questions/create/";
$site_address = "questions.php";

if(isset($_POST["deleteRow"])){
    $query = "DELETE FROm `groups` WHERE `id` = ".$_POST["group_id"].";";
    $connection->query($query);  
}
if(isset($_POST["deleteQuestion"])){
    $query = "DELETE FROm `link_group_questions` WHERE `question_id` = ".$_POST["question_id"].";";
    $connection->query($query);
    $query = "UPDATE `questions` SET `account_id` = NULL WHERE `questions`.`id_question` = ".$_POST["question_id"].";";
    $connection->query($query);
}
if(isset($_POST["addGroupButton"])){
    $name = $_POST["addGroupName"];
    $query = "INSERT INTO `groups` (`id`, `name`, `account_id`) VALUES (NULL, '$name', ".$_SESSION["user_id"].");";
    $connection->query($query);
}

include("../../layout/layout.php");