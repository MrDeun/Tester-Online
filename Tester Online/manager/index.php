<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../functions.php");
checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
clear_session_except(["logged","user_id","question_id"]);
include('../login_sql.php');
    
$site_address = "logged_manager.php";
$title = "PULPIT";




include("../layout/layout.php");
$connection->close();