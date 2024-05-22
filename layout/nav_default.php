<?php

$testerOnlinePath = getTesterOnlinePath();
if(isset($_SESSION["logged"])){
    generateNav($testerOnlinePath."login/loggout.php","LOGOUT","w3-right");
    if($_SESSION["logged"] == '2'){
        generateNav($testerOnlinePath."teacher","PULPIT");
        generateNav($testerOnlinePath."teacher/tests_templates","SZABLONY TESTÓW");
        generateNav($testerOnlinePath."teacher/tests_activated","AKTYWOWANE TESTY");
        generateNav($testerOnlinePath."teacher/questions","PYTANIA");
    }
}
else{
    generateNav($testerOnlinePath."/login","LOGIN","w3-right");
}
