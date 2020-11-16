<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php

$db = getDB();
$stmt = $db->prepare("SELECT product_id, quantity, price FROM Carts WHERE user_id = :user");
$r = $stmt->execute([
    ":user" => get_user_id()
]);
$carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h3><?= get_username() ?>'s Cart</h3>
<div class="results mt-3">
    <?php if (count($carts) > 0): ?>
        <ul class="list-group list-group-flush">
            <?php foreach ($carts as $cart): ?>
                <li class="list-group-item">
                    <div>
                        <span>Product:</span>
                        <div><?php safer_echo(get_product_name($cart["product_id"])); ?></div>
                    </div>
                    <div>
                        <span>Quantity:</span>
                        <div><?php safer_echo($cart["quantity"]); ?></div>
                    </div>
                    <div>
                        <span>Price:</span>
                        <div><?php echo get_product_price($cart["product_id"]); ?></div>
                    </div>
            
                    <div>
                        <a type="button" href="product.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../partials/footer.php"); ?>