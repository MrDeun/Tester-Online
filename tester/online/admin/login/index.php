<?php
    if(!isset($_POST['logged'])){
        include("admin_login_form.php");
    }
    else if($_POST['logged'] == 1024){
        include("admin_account_dashboard.php");
    }


?>