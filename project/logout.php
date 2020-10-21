
<?php 
session_start();
// remove session variables
session_unset();
// destroy the session
session_destroy();
?>
<?php require_once(__DIR__ . "/partials/header.php"); ?>

<?php 

flash("You have been logged out");
die(header("Location: login.php"));

?>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>