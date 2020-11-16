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
    <table class="table">
        <thead>
            <tr>
            <th scope="col">Name</th>
            <th scope="col">Quantity</th>
            <th scope="col">Unit Price</th>
            <th scope="col">Total</th>
            <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carts as $cart): ?>
            <tr>
                <th scope="row"><?php safer_echo(get_product_name($cart["product_id"])); ?></th>
                <td><?php safer_echo($cart["quantity"]); ?></td>
                <td>$<?php safer_echo(get_product_price($cart["product_id"])); ?></td>
                <td>$<?php safer_echo(get_product_price($cart["product_id"])*$cart["quantity"]); ?></td>
                <td><a type="button" href="../shop/product.php?id=<?php safer_echo($cart["product_id"]); ?>">Edit</a></td>
            </tr>
            <?php endforeach; ?>
            <tbody>
    </table>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../partials/footer.php"); ?>