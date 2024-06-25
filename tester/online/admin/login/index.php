<?php

    session_start();
    if(!isset($_SESSION["site"])){
        $_SESSION["site"] = "home";
    }
    if(isset($_POST["site"])){
        $_SESSION["site"] = $_POST["site"];       
    }
    switch ($_SESSION["site"]) {
        case '1024':
            $title = "ADMIN";
            $logged = true;
            break;
        default:
            $title = "LOGIN";
            $logged = false;
            break;
    }
    include('functions.php');
    include("layout/layout.php"); 