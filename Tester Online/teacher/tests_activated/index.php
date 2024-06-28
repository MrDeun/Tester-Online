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


$create_template = getTesterOnlinePath() . "teacher/tests_activated/create/";
$create_check = getTesterOnlinePath() . "teacher/tests_activated/check/";
$title = "AKTYWOWANE TESTY";
$site_address = "test_activated_list.php";

include("../../layout/layout.php");