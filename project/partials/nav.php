<?php
require_once(__DIR__ . "/../lib/helpers.php");
?>
    

<nav class="navbar navbar-expand-lg navbar-light bg-white mb-3 px-4">
  <a class="navbar-brand" href="home.php">Store202</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
      <a class="nav-item nav-link" href="home.php">Home <span class="sr-only">(current)</span></a>
      <?php if(!is_logged_in()):?>
      <a class="nav-item nav-link" href="login.php">Login</a>
      <a class="nav-item nav-link" href="register.php">Register</a>
      <?php endif;?>
      <?php if(is_logged_in()):?>
      <a class="nav-item nav-link" href="logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
