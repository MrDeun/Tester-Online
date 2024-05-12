<form class="w3-container w3-right" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="hidden" name="site" value="<?php echo $logged == true ? 'home' : 'login_form'?>">
    <input type="hidden" name="under_site" value="">
    <button class="w3-button w3-aqua"><?php echo $logged == true ? 'LOGOUT' : 'LOGIN'?></button>
</form>
<form class="w3-container w3-left" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="hidden" name="under_site" value="">
    <button class="w3-button w3-aqua <?php echo $_SESSION["site"] != "logged_teacher" ? "w3-hide" : ""?>">PULPIT</button>
</form>
<form class="w3-container w3-left" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="hidden" name="under_site" value="test_template_list">
    <button class="w3-button w3-aqua <?php echo $_SESSION["site"] != "logged_teacher" ? "w3-hide" : ""?>">SZABLONY TESTÓW</button>
</form>
<form class="w3-container w3-left" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST">
    <input type="hidden" name="under_site" value="test_actived_list">
    <button class="w3-button w3-aqua <?php echo $_SESSION["site"] != "logged_teacher" ? "w3-hide" : ""?>">AKTYWOWANE TESTY</button>
</form>