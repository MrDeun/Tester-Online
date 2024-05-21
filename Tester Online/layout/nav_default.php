<?php

$testerOnlinePath = getTesterOnlinePath();
if(isset($_SESSION["logged"])){
    generateForm($testerOnlinePath."login/loggout.php","LOGOUT","w3-right");
    if($_SESSION["logged"] == '2'){
        generateForm($testerOnlinePath."teacher","PULPIT");
        generateForm($testerOnlinePath."teacher/tests_templates","SZABLONY TESTÓW");
        generateForm($testerOnlinePath."teacher/tests_activated","AKTYWOWANE TESTY");
        generateForm($testerOnlinePath."teacher/questions","PYTANIA");
    }
}
else{
    generateForm($testerOnlinePath."/login","LOGIN","w3-right");
}
