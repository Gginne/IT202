<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
     $stmt = $db->prepare("SELECT cart.id,cart.quantity,cart.price,Users.username, Product.name as name FROM Carts as cart JOIN Users on cart.user_id = Users.id LEFT JOIN Products Product on Product.id = cart.product_id WHERE cart.id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        
        <div class="card-body">
            <h4 class="card-title">
                <?php safer_echo($result["name"]); ?>
            </h4>
            <div class="card-text">
                <div><b>Quanity:</b> <?php safer_echo($result["quantity"]); ?></div>
                <div><b>Price:</b> <?php safer_echo($result["price"]); ?></div>
                <div><b>Owned by:</b> <?php safer_echo($result["username"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>