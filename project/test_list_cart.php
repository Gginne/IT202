<?php require_once(__DIR__ . "/partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT cart.product_id,cart.quantity,cart.id, product.name as product, Users.username from Carts as cart JOIN Users on cart.user_id = Users.id LEFT JOIN Products as product on cart.product_id = product.id WHERE cart.product_id like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
$db = getDB();
$stmt = $db->prepare("SELECT id,name from Products LIMIT 10");
$res = $stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h3>List Carts</h3>
<form method="POST">
<label>Product:</label>
        <select name="query" value="-1">
            <option value="-1">None</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php safer_echo($product["id"]); ?>" >
                    <?php safer_echo($product["name"]); ?>
                    -- $<?php safer_echo(get_product_price($product["id"])); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <input type="submit" value="Search" name="search"/>
    <br><br>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Product:</div>
                        <div><?php safer_echo($r["product"]); ?></div>
                    </div>
                    <div>
                        <div>Quantity:</div>
                        <div><?php safer_echo($r["quantity"]); ?></div>
                    </div>
                    <div>
                        <div>Price:</div>
                        <div><?php echo get_product_price($r["product_id"]); ?></div>
                    </div>
            
                    <div>
                        <div>Owner:</div>
                        <div><?php safer_echo($r["username"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="test_edit_cart.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="test_view_cart.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>