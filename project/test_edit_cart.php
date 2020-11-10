<?php require_once(__DIR__ . "/partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $product = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    $price = get_product_price($product);
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE Carts set product_id=:product, quantity=:quantity, price=:price where id=:id");
        $r = $stmt->execute([
            ":product" => $product,
            ":quantity" => $quantity,
            ":price" => $price,
            ":id" => $id
        ]);
        if ($r) {
            flash("Updated successfully with id: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Carts where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
//get eggs for dropdown
$db = getDB();
$stmt = $db->prepare("SELECT id,name from Products LIMIT 10");
$r = $stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Edit Cart</h3>
    <form method="POST">
    <label>Product:</label>
        <select name="product_id" value="<?php echo $result["product_id"]; ?>" >
            <option value="-1">None</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php safer_echo($product["id"]); ?>" 
                <?php echo ($result["product_id"] == $product["id"] ? 'selected="selected"' : ''); ?>
                >
                    <?php safer_echo($product["name"]); ?>
                    -- $<?php safer_echo(get_product_price($product["id"])); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label>Quantity: </label>
        <input type="number" min="1" name="quantity" value="<?php echo $result["quantity"]; ?>"/>
        <br><br>
        <input type="submit" name="save" value="Update"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>