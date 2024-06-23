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

$title = "TEST";
$site_address = "test.php";


include("../../layout/layout.php");