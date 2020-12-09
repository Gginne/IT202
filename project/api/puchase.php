<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}


$user = get_user_id();


$carts = [];

$db = getDB();

$stmt = $db->prepare("SELECT product_id, quantity, price from Carts WHERE user_id = :user");
$r = $stmt->execute([
    ":user" => $user
]);

$carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

//GET LAST ORDER ID 
$stmt = $db->prepare("SELECT MAX(Id) FROM Orders");
$stmt->execute();
$last_ord = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($carts as $c) {

    $name = get_product_name($c["product_id"]);
    //VERIFY CART QUANTITY IN STOCK
    if($c["quantity"] > in_stock($c["id"])){
        $errMsg = "Cannot buy ".$c['quantity']." $name only ".in_stock($c["product_id"])." available"
        $response = ["status" => 400, "error" => $errMsg];
        echo json_encode($response);
        die();
    }

    //COPY CART DETAILS INTO ORDER ITEMS 

    //UPDATE PRODUCTS QUANTITY

}

//DELETE ALL CARTS

if ($r) {
    $response = ["status" => 200, "carts" => $carts];
    echo json_encode($response);
    die();
}
else {
    $e = $stmt->errorInfo();
    $response = ["status" => 400, "error" => $e];
    echo json_encode($response);
    die();
}




?>