<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
$testing = false;
if (isset($_GET["test"])) {
    $testing = true;
}

//TODO check if user can afford
//get number of eggs in ownership
//first egg is free
//each egg extra is base_cost * #_of_eggs
//$eggs_owned = 0;
//$base_cost = 10;
//$cost = $eggs_owned * $base_cost;
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

if(isset($_POST["add"])){
    $qt = $_POST["quantity"]
}

$user = get_user_id();


?>