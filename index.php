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
            $title = "LOGIN";
            $logged = false;
            $_SESSION["site_address"] = "sites/login_form.php";
            include('login_sql.php');
            break;
        case 'logged_manager':
            $title = "DASHBOARD";
            $logged = 1;
            $_SESSION["site_address"] = "sites/logged_manager.php";
            include('login_sql.php');
            break;
        case 'logged_teacher':
            $title = "DASHBOARD";
            $logged = 2;
            include('login_sql.php');
            switch($_SESSION["under_site"]){
                case 'test_template_list':
                    $_SESSION["site_address"] = "sites/teacher/test_template_list.php";
                    break;
                case 'test_activated_list':
                    $_SESSION["site_address"] = "sites/teacher/test_activated_list.php";
                    break;
                case 'activated_test':
                    $_SESSION["site_address"] = "sites/teacher/activated_test.php";
                    break;
                case 'test_template_create':
                    $_SESSION["site_address"] = "sites/teacher/test_template_create.php";
                    break;
                default:
                    $_SESSION["site_address"] = "sites/teacher/dashboard.php";
                    break;
            }
            break;
        case 'logged_student':
            $title = "DASHBOARD";
            $logged = 3;
            include('login_sql.php');
            break;
        default:
            $title = "HOME";
            $logged = false;
            $_SESSION["site_address"] = "sites/home.html";
            break;
    } 


    include("layout/layout.php"); 
    
