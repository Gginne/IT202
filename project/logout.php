
<?php require_once(__DIR__ . "/partials/header.php"); ?>


<?php require_once(__DIR__ . "/partials/header.php"); ?>

<?php 
session_unset();
// destroy the session
session_destroy();
flash("You have been logged out");
die(header("Location: login.php"));

?>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>