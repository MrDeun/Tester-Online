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

if(isset($_POST["deleteTemplate"])){
    $query = "DELETE FROm `link_group_questions` WHERE `question_id` = ".$_POST["template_id"].";";
    $connection->query($query);
    $query = "UPDATE `questions` SET `account_id` = NULL WHERE `questions`.`id_question` = ".$_POST["question_id"].";";
    $connection->query($query);
}
$create_template = getTesterOnlinePath() . "teacher/tests_templates/create/";
$title = "SZABLONY TESTÓW";
$site_address = "test_template_list.php";

include("../../layout/layout.php");