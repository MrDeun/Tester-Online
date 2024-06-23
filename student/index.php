<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../functions.php");
checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
if($_SESSION["logged"] != 3){
    clear_session_except();
    redirectToIndex("/");
}
clear_session_except(["logged","user_id"]);
include('../login_sql.php');
$title = "PULPIT";
$site_address = "dashboard.php";
$test_address = getTesterOnlinePath() . "student/test/";

include("../layout/layout.php");