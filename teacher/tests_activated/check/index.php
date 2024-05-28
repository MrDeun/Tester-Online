<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../../functions.php");
checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
if($_SESSION["logged"] != 2){
    clear_session_except();
    redirectToIndex("/");
}
clear_session_except(["logged","user_id","activated_id"]);
include('../../../login_sql.php');


$title = "SPRAWDŹ TEST";
$site_address = "test_check.php";

include("../../../layout/layout.php");