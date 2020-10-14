<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>
    
<nav>
  <p class="brand">Store202</p>
  <ul class="nav">
    <li><a href="home.php">Home</a></li>
    <?php if(!is_logged_in()):?>
    <li><a href="login.php">Login</a></a></li>
    <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if(is_logged_in()):?>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
  </ul>
</nav>
