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

//GET LAST ORDER ID AND DELETE IF CANCELED ORDER
$stmt = $db->prepare("SELECT id FROM Orders ORDER BY id DESC LIMIT 1");
$stmt->execute();
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$last_ord = $res["id"];

if(isset($_POST["cancel"])){
    $stmt = $db->prepare("DELETE FROM Orders WHERE id=:id");
    $d = $stmt->execute([":id" => $last_ord]);
    if($d){
        $response = ["status" => 200, "msg" => "Cancelled order"];
        echo json_encode($response);
        die();
    } else {
        $response = ["status" => 200, "error" => "Processing error"];
        echo json_encode($response);
        die();
    }
    
    
}

foreach ($carts as $c) {

    $name = get_product_name($c["product_id"]);
    $stock = in_stock($c["product_id"]);
    //VERIFY CART QUANTITY IN STOCK
    if($c["quantity"] > in_stock($c["product_id"])){
        $errMsg = "Cannot buy ".$c['quantity']." $name only $stock in stock";
        $response = ["status" => 400, "error" => $errMsg];
        echo json_encode($response);
        die();
    }

    //COPY CART DETAILS INTO ORDER ITEMS 
    $stmt = $db->prepare("INSERT INTO OrderItems (product_id, quantity, unit_price, user_id, orderRef) VALUES(:product, :quantity, :price, :user, :order)");
    $r = $stmt->execute([
        ":product" => $c["product_id"],
        ":quantity" => $c["quantity"],
        ":price" => $c["price"],
        ":user" => $user,
        ":order" => $last_ord
    ]);

    if($r){
        //UPDATE PRODUCTS QUANTITY
        $diff = $stock - $c["quantity"];
        $vis = $diff > 0 ? 1 : 0;
        $stmt = $db->prepare("UPDATE Products set quantity=:quantity, visibility=:visibility where id=:id");
        $stmt->execute([
            ":quantity" => $diff,
            ":visibility" => $vis,
            ":id" => $c["product_id"]
        ]);
    } else {
        $errMsg = "Couldn't process order";
        $response = ["status" => 400, "error" => $errMsg];
        echo json_encode($response);
        die();
    }  
}

//DELETE ALL CARTS

if ($r) {
    $stmt = $db->prepare("DELETE FROM Carts where user_id=:user");
    $stmt->execute([
        ":user" => $user
    ]);
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