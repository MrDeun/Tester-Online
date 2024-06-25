<form class="w3-container"  method="POST">
    <input type="hidden" name="site" value="<?php echo $logged == true ? 'home' : 'login_form'?>">
    <button class="w3-button w3-right w3-aqua"><?php echo $logged == true ? 'LOGOUT' : 'LOGIN'?></button>
</form>