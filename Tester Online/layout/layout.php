<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Tester Online <?php echo('- '.$title);?></title>
    <link rel="icon" type="image/x-icon" href="">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="<?php echo getTesterOnlinePath(); ?>/wwwroot/css/layout.css">
    <?php
        if(isset($login_form)&&$login_form){
            echo('<link rel="stylesheet" href="wwwroot/css/login_form.css">');
        }
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&display=swap" rel="stylesheet">
    <script src="<?php echo getTesterOnlinePath(); ?>/wwwroot/js/w3.js"></script>
    <script src="<?php echo getTesterOnlinePath(); ?>/wwwroot/js/site.js"></script>
    <style>
        body { background-image: url("<?php echo getTesterOnlinePath(); ?>/wwwroot/images/layout/background.png"); }
    </style>


</head>
<body class="w3-row w3-block" onresize="mainSizeChange()" onload="mainSizeChange()">
    <header class="w3-container w3-center w3-text-white">
        <a href="<?php echo($_SERVER['PHP_SELF']); ?>" ><h1 class="header_h1 w3-jumbo w3-container">TESTER ONLINE</h1></a>
    </header>
    <nav class="w3-container w3-text-black">
        <?php include("nav_default.php");?>
    </nav>
    <main class="w3-container w3-row w3-text-black w3-display-container">
        <div class="w3-container w3-center w3-display-topmiddle w3-block w3-col s12 m11 l9 main w3-row"> 
            <?php
                include($site_address);
                
            ?>
        </div>
    </main>
</body>
</html>