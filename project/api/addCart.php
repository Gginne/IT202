<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}


$id = null;
$qt = null;
if (isset($_POST["id"])) {
    $id = $_POST["id"];
}

$price = get_product_price($id);
$user = get_user_id();
$name = get_product_name($id);

if(isset($_POST["qt"])){
    $qt = $_POST["qt"];
    if(is_numeric($id) && ($qt < 0 || $qt > in_stock($id))){
        $errMsg = "Cannot add $qt $name to cart only ".in_stock($id)." in stock";
        $response = ["status" => 400, "error" => $errMsg];
        echo json_encode($response);
        die();
    }
}



$cart = [
    "name" => $name,
    "product_id" => $id,
    "quantity" => $qt,
    "price" => $price,
    "user_id" => $user
];

$db = getDB();
$r = null;


if($qt > 0) {
    $stmt = $db->prepare("INSERT INTO Carts (product_id, quantity, price, user_id) VALUES(:product, :quantity, :price, :user) on duplicate key update quantity = :quantity");
    $r = $stmt->execute([
        ":product" => $cart["product_id"],
        ":quantity" => $cart["quantity"],
        ":price" => $cart["price"],
        ":user" => $cart["user_id"],
    ]);
   
} else {
        if($id == "all"){
            $stmt = $db->prepare("DELETE FROM Carts WHERE user_id=:user"); 
            $r = $stmt->execute([
                ":user" => $cart["user_id"]
            ]);  
        } else{
            $stmt = $db->prepare("DELETE FROM Carts WHERE product_id=:product AND user_id=:user");
            $r = $stmt->execute([
                ":product" => $cart["product_id"],
                ":user" => $cart["user_id"]
            ]); 
        }
} 

if ($r) {
    $response = ["status" => 200, "cart" => $cart];
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