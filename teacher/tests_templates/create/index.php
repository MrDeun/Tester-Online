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
clear_session_except(["logged","user_id","template_id"]);
include('../../../login_sql.php');

$id = null;
if(isset($_POST["template_id"])){
    $_SESSION["template_id"] = $_POST["template_id"];
    $id =  $_SESSION["template_id"];
    $query = "SELECT name, time FROM test WHERE id_test = $id";
    $result = $connection->query($query);
    $row = mysqli_fetch_row($result);
    $name = $row[0];
    $time = $row[1];
    
}
$title = "TWORZENIE SZABLONÓW";
$site_address = "test_template_create.php";

include("../../../layout/layout.php");