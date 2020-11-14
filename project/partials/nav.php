<nav class="navbar navbar-expand-lg navbar-light bg-white p-3">
  <a class="navbar-brand logo" href="#">Store202</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse px-3" id="navbarSupportedContent">
    <ul class="navbar-nav ">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("home.php");?>">Home <span class="sr-only">(current)</span></a>
      </li>
      <?php if(!is_logged_in()):?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("login.php");?>">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("register.php");?>">Register</a>
      </li>
      <?php endif; ?>
      <?php if (has_role("Admin")): ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Test
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?php echo getURL("test/test_create_product.php");?>">Create Products</a>
          <a class="dropdown-item" href="<?php echo getURL("test/test_list_product.php");?>">View Products</a>
          <a class="dropdown-item" href="<?php echo getURL("test/test_create_cart.php");?>">Create Carts</a>
          <a class="dropdown-item" href="<?php echo getURL("test/test_list_cart.php");?>">View Carts</a>
          
        </div>
      </li>
      <?php endif; ?>
      <?php if(is_logged_in()):?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("shop/index.php");?>">Catalog</a>
      </li>
      <?php endif; ?>
      <?php if(is_logged_in()):?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("profile.php");?>">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo getURL("logout.php");?>">Logout</a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

