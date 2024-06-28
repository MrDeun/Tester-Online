<?php
include('../functions.php');
if(session_status() == PHP_SESSION_NONE){
    session_start();
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
                redirectToIndex("");
            break;
        }
    }
}
clear_session_except(['user_id']);

$title = "LOGIN";
$site_address = "login_form.php";

include("../layout/layout.php");