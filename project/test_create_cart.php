<?php require_once(__DIR__ . "/partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php 
$db = getDB();
$stmt = $db->prepare("SELECT id,name from Products LIMIT 10");
$r = $stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Create Cart</h3>
    <form method="POST">
        <label>Product:</label>
        <select name="product_id" value="-1" >
            <option value="-1">None</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php safer_echo($product["id"]); ?>">
                    <?php safer_echo($product["name"]); ?>
                    -- $<?php safer_echo(get_product_price($product["id"])); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label>Quantity</label>
        <input type="number" min="1" name="quantity"/>
        <br><br>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $product = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    $price = get_product_price($product);
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Carts (product_id, quantity, price, user_id) VALUES(:product, :quantity, :price, :user)");
    $r = $stmt->execute([
        ":product" => $product,
        ":quantity" => $quantity,
        ":price" => $price,
        ":user" => $user
    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require(__DIR__ . "/partials/footer.php"); ?>