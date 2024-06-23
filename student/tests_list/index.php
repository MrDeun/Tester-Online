<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../functions.php");
include('../../login_sql.php');

checkSessionAndRedirect('logged');
checkSessionAndRedirect('user_id');
if($_SESSION["logged"] != 3){
    clear_session_except();
    redirectToIndex("/");
}

$title = "TESTY";
$site_address = "tests_list.php";


include("../../layout/layout.php");