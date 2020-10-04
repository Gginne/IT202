<?php
session_start();
?>
<h1>Home</h1>
<ul>
  <li><a href="home.php">Home</a></li>
  <li><a href="login.php">Login</a></li>
  <li><a href="register.php">Register</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
<?php
//we use this to safely get the email to display
$email = "";
if(isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])){
 $email = $_SESSION["user"]["email"]; 
}
?>
<p>Welcome, <?php echo $email;?></p>