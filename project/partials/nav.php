<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>
<ul>
    <li><a href="home.php">Home</a></li>
    <?php if(!is_logged_in()):?>
    <li><a href="sample_login.php">Login</a></li>
    <li><a href="sample_reg.php">Register</a></li>
    <?php endif;?>
    <?php if(is_logged_in()):?>
    <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>