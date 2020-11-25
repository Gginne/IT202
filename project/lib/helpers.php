<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

function get_product_price($id){
    $db = getDB();
    $stmt = $db->prepare("SELECT price FROM Products WHERE id = :id");
    $r = $stmt->execute([":id" => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if($product){
      return $product["price"];
    }
    return null;
}

function get_product_name($id){
    $db = getDB();
    $stmt = $db->prepare("SELECT name FROM Products WHERE id = :id");
    $r = $stmt->execute([":id" => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if($product){
      return $product["name"];
    }
    return null;
}

function in_stock($id){
    $db = getDB();
    $stmt = $db->prepare("SELECT quantity FROM Products WHERE id = :id");
    $r = $stmt->execute([":id" => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if($product){
      return $product["quantity"];
    }
    return null;
}

function in_cart($prod_id){
    $db = getDB();
    $stmt = $db->prepare("SELECT quantity FROM Carts WHERE product_id = :prod_id and user_id = :user");
    $r = $stmt->execute([
        ":prod_id" => $prod_id,
        ":user" => get_user_id()
    ]);
    if($stmt->rowCount() > 0){
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cart["quantity"];
    }
    return 0;
}

function is_visible($prod_id){
    $db = getDB();
    $stmt = $db->prepare("SELECT visibility, quantity FROM Products WHERE id = :prod_id");
    $r = $stmt->execute([":prod_id" => $prod_id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if(($prod["visibility"] == 1 || has_role("Admin")) && $prod["quantity"] > 0){
        return True;
    }

    return False;
}

function getURL($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/IT202/project/$path";
}

//end flash

?>