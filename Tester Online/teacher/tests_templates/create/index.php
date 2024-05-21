<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../../functions.php");
checkSessionAndRedirect('logged', 3);
checkSessionAndRedirect('user_id', 3);
clear_session_except(["logged","user_id","template_id"]);
include('../../../login_sql.php');
    
$site_address = "test_template_create.php";

include("../../../layout/layout.php");