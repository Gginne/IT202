
    
<nav>
  <p class="brand">Store202</p>
  <ul class="nav">
    <li><a href="<?php echo getURL("home.php");?>">Home</a></li>
    <?php if(!is_logged_in()):?>
      <li><a href="<?php echo getURL("login.php");?>">Login</a></a></li>
      <li><a href="<?php echo getURL("register.php");?>">Register</a></li>
    <?php endif; ?>
    <?php if (has_role("Admin")): ?>
      <li><a href="<?php echo getURL("test/test_create_product.php");?>">Create Products</a></li>
      <li><a href="<?php echo getURL("test/test_list_product.php");?>">View Products</a></li>
      <li><a href="<?php echo getURL("test/test_create_cart.php");?>">Create Carts</a></li>
      <li><a href="<?php echo getURL("test/test_list_cart.php");?>">View Carts</a></li>
    <?php endif; ?>
    <?php if(is_logged_in()):?>
      <li><a href="<?php echo getURL("profile.php");?>">Profile</a></li>
      <li><a href="<?php echo getURL("logout.php");?>">Logout</a></li>
    <?php endif; ?>
  </ul>
</nav>
