<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../functions.php");
checkSessionAndRedirect('logged', 1);
checkSessionAndRedirect('user_id', 1);
clear_session_except(["logged","user_id"]);
include('../login_sql.php');
    
$site_address = "dashboard.php";

include("../layout/layout.php");