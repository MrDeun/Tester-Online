<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../../functions.php");
checkSessionAndRedirect('logged', 2);
checkSessionAndRedirect('user_id', 2);
clear_session_except(["logged","user_id"]);
include('../../login_sql.php');
    
$site_address = "test_template_list.php";

include("../../layout/layout.php");