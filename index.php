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

/*


    if(!isset($_SESSION["site"])){
        $_SESSION["site"] = "home";
    }  
    if(isset($_POST["site"])){
        $_SESSION["site"] = $_POST["site"];
        
    }
    
    if(!isset($_SESSION["under_site"])){
        $_SESSION["under_site"] = "";
    }
    if(isset($_POST["under_site"])){
        if(isset($_POST["under_site"])){
            $_SESSION["under_site"] = $_POST["under_site"];
        }

    }
    
    





    switch ($_SESSION["site"]) {
        case 'login_form':
            
            
            $logged = false;
            
            
            break;
        case 'logged_manager':
            clear_session_except(['site','user_id']);
            $title = "PULPIT";
            $logged = 1;
            $site_address = "sites/logged_manager.php";           
            break;
        case 'logged_teacher':
            $logged = 2;
            switch($_SESSION["under_site"]){
                case 'test_template_list':
                    clear_session_except(['site','user_id','under_site']);
                    $site_address = "sites/teacher/test_template_list.php";
                    $title = "SZABLONY TESTÓW";
                    break;
                case 'test_activated_list':
                    clear_session_except(['site','user_id','under_site']);
                    $site_address = "sites/teacher/test_activated_list.php";
                    $title = "AKTYWOWANE TESTY";
                    break;
                case 'activated_test':
                    clear_session_except(['site','user_id','under_site']);
                    $site_address = "sites/teacher/activated_test.php";
                    $title = "AKTYWOWANE TESTY";
                    break;
                case 'test_template_create':
                    clear_session_except(['site','user_id','under_site','test_id']);
                    $site_address = "sites/teacher/test_template_create.php";
                    $title = "STWÓZ TEST";
                    break;
                case 'questions':
                    clear_session_except(['site','user_id','under_site']);
                    $site_address = "sites/teacher/questions.php";
                    $title = "PYTANIA";
                    break;
                case 'question_create':
                    clear_session_except(['site','user_id','under_site','question_id']);
                    $site_address = "sites/teacher/question_create.php";
                    $title = "STWÓRZ PYTANIE";                  
                    if(isset($_POST["question_id"])){
                        $_SESSION["question_id"] =  $_POST["question_id"];
                    }
                    break;
                case 'question_sending':
                    if(isset($_POST['questionButton'])){
                        clear_session_except(['site','user_id','under_site']);
                        include("sites/teacher/question_sending.php");
                        $site_address = "sites/teacher/questions.php";
                        $title = "PYTANIA";
                    }
                    else{
                        clear_session_except(['site','user_id','under_site','question_id']);
                        $site_address = "sites/teacher/question_create.php";
                        $title = "STWÓRZ PYTANIE";
                    }
                    break;                   
                default:
                    clear_session_except(['site','user_id','under_site']);
                    $site_address = "sites/teacher/dashboard.php";
                    $title = "PULPIT";
                    break;
            }
            break;
        case 'logged_student':
            $title = "PULPIT";
            $logged = 3;
            
            break;
        default:
            
            
            
            break;
    } 
    */
    
