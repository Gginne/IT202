<?php require_once(__DIR__ . "/partials/header.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>


<h1>Welcome, <?php echo $email; ?></h1>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>