<?php
    session_start();
    include('login_sql.php');

    if(!isset($_SESSION["site"])){
        $_SESSION["site"] = "home";
    }  
    if(isset($_POST["site"])){
        $_SESSION["site"] = $_POST["site"];
        if(isset($_POST["user_id"])){
            $_SESSION["user_id"] = $_POST["user_id"];
        }
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
            $_SESSION["title"] = "LOGIN";
            $logged = false;
            $_SESSION["site_address"] = "sites/login_form.php";
            break;
        case 'logged_manager':
            $_SESSION["title"] = "PULPIT";
            $logged = 1;
            $_SESSION["site_address"] = "sites/logged_manager.php";
            break;
        case 'logged_teacher':
            $logged = 2;
            switch($_SESSION["under_site"]){
                case 'test_template_list':
                    $_SESSION["site_address"] = "sites/teacher/test_template_list.php";
                    $_SESSION["title"] = "SZABLONY TESTÓW";
                    break;
                case 'test_activated_list':
                    $_SESSION["site_address"] = "sites/teacher/test_activated_list.php";
                    $_SESSION["title"] = "AKTYWOWANE TESTY";
                    break;
                case 'activated_test':
                    $_SESSION["site_address"] = "sites/teacher/activated_test.php";
                    $_SESSION["title"] = "AKTYWOWANE TESTY";
                    break;
                case 'test_template_create':
                    $_SESSION["site_address"] = "sites/teacher/test_template_create.php";
                    $_SESSION["title"] = "STWÓZ TEST";
                    break;
                case 'questions':
                    $_SESSION["site_address"] = "sites/teacher/questions.php";
                    $_SESSION["title"] = "PYTANIA";
                    break;
                case 'question_create':
                    $_SESSION["site_address"] = "sites/teacher/question_create.php";
                    $_SESSION["title"] = "STWÓRZ PYTANIE";
                    break;
                case 'question_sending':
                    if(isset($_POST['questionButton'])){
                        include("sites/teacher/question_sending.php");
                        $_SESSION["site_address"] = "sites/teacher/questions.php";
                        $_SESSION["title"] = "PYTANIA";
                    }
                    else{
                        $_SESSION["site_address"] = "sites/teacher/question_create.php";
                        $_SESSION["title"] = "STWÓRZ PYTANIE";
                    }
                    break;                   
                default:
                    $_SESSION["site_address"] = "sites/teacher/dashboard.php";
                    $_SESSION["title"] = "PULPIT";
                    break;
            }
            break;
        case 'logged_student':
            $_SESSION["title"] = "PULPIT";
            $logged = 3;
            
            break;
        default:
            $_SESSION["title"] = "HOME";
            $logged = false;
            $_SESSION["site_address"] = "sites/home.html";
            break;
    } 

    include("layout/layout.php"); 
    $connection->close();
    
