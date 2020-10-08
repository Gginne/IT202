
<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php 
session_unset();
// destroy the session
session_destroy();
echo "You're logged out (proof by dumping the session)<br>";
echo "<pre>" . var_export($_SESSION, true) . "</pre>";
?>

<a href="home.php">Home</a>