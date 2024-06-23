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
clear_session_except(["logged","user_id","activated_id"]);
include('../../../login_sql.php');

$id = null;
if(isset($_POST["activated_id"])){
    $_SESSION["activated_id"] = $_POST["activated_id"];      
}
elseif (!isset($_SESSION["activated_id"])) {
    redirectToIndex("/teacher/tests_activated");
}
$id =  $_SESSION["activated_id"];
$query = "CALL GetActivatedTestInfo($id)";
$result = $connection->query($query);
$row = mysqli_fetch_row($result);
$name = $row[0];
$time = $row[1];
$activation_time = $row[2];
$create_check = getTesterOnlinePath() . "teacher/tests_activated/check/single/";


$title = "SPRAWDŹ TEST";
$site_address = "test_check.php";

include("../../../layout/layout.php");