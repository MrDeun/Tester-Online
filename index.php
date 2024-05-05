<?php
    session_start();
    if(!isset($_SESSION["site"])){
        $_SESSION["site"] = "home";
    }
    if(isset($_POST["site"])){
        $_SESSION["site"] = $_POST["site"];
        if(isset($_POST["user_id"])){
            $_SESSION["user_id"] = $_POST["user_id"];
        }
        

    }
    switch ($_SESSION["site"]) {
        case 'login_form':
            $title = "LOGIN";
            $logged = false;
            break;
        case 'logged_manager':
            $title = "DASHBOARD";
            $logged = 1;
            
            break;
        case 'logged_teacher':
            $title = "DASHBOARD";
            $logged = 2;
            break;
        case 'logged_student':
            $title = "DASHBOARD";
            $logged = 3;
            break;
        default:
            $title = "HOME";
            $logged = false;
            break;
    }

    
    
    
    include("layout/layout.php"); 
    
?>