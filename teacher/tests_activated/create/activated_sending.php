<?php
if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
    redirectToIndex("/teacher/tests_activated/create");
}
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include('../../../login_sql.php');
$id = 0;
$activation_time = 0;

if(isset($_POST["test"])){
    $id = $_POST["test"];
}
if(isset($_POST["test"])){
    $activation_time = $_POST["activation_time"];
}

if($id >= 0){
    $query = "INSERT INTO `activated_tests` (`id`, `activation_time`, `test_id`, `account_id`) VALUES (NULL, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sii", $activation_time, $id, $_SESSION["user_id"]);

    $stmt->execute();
    
}
$connection->close();
include("../../../functions.php");
redirectToIndex("/teacher/tests_activated");