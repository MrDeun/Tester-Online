<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include('functions.php');
if(isset($_SESSION['logged'])){
    switch ($_SESSION['logged']) {
        case '1':
            redirectToIndex("/manager");
            break;
        case '2':
            redirectToIndex("/teacher");
            break;
        case '3':
            redirectToIndex("/student");
        default:
            redirectToIndex("/");
            break;
    }
}


clear_session_except([]);
$title = "HOME";
$logged = false;
$site_address = "sites/home.html";


include("layout/layout.php"); 

