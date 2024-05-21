<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include("../functions.php");
clear_session_except();

redirectToIndex("");