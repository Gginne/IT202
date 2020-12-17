
<?php require_once(__DIR__ . "/partials/header.php"); ?>

<?php 
if(!is_logged_in()){
    die(header("Location: ./login.php"));
} else {
    die(header("Location: ./home.php"));
}
?>
   
<?php require_once(__DIR__ . "/partials/footer.php"); ?>

