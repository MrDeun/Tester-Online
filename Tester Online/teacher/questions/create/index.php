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
clear_session_except(["logged","user_id","question_id"]);
include('../../../login_sql.php');
    
$site_address = "question_create.php";
$title = "TWORZENI PYTANIA";
$id = null;$text = "";$open = false;$points = 0;
if(isset($_POST["question_id"])){
    $_SESSION["question_id"] = $_POST["question_id"];
    $id =  $_SESSION["question_id"];
    $query = "SELECT text, opened, points FROM questions WHERE id = $id";
    $result = $connection->query($query);
    $row = mysqli_fetch_row($result);
    $text = $row[0];
    $open = (bool)$row[1];
    $points = $row[2];
}


include("../../../layout/layout.php");
$connection->close();