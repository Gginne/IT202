<?php require_once(__DIR__ . "/partials/header.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>

<div class="display-4">
    <p class="text-center">Welcome, <?php echo $email; ?></p>
</div>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>