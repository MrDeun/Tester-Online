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

//if(isset($_POST["deleteTemplate"])){
//    $query = "DELETE FROM `link_test_groups` WHERE `test_id` = ".$_POST["template_id"].";";
//    $connection->query($query);
//    $query = "UPDATE `test` SET `account_id` = NULL WHERE `id_test` = ".$_POST["template_id"].";";
//    $connection->query($query);
//}
$create_template = getTesterOnlinePath() . "teacher/tests_activated/create/";
$create_check = getTesterOnlinePath() . "teacher/tests_activated/check/";
$title = "AKTYWOWANE TESTY";
$site_address = "test_activated_list.php";

include("../../layout/layout.php");