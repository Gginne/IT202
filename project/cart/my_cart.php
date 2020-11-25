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
            <th scope="col">Product</th>
            <th scope="col">Quantity</th>
            <th scope="col">Unit Price</th>
            <th scope="col">Total</th>
            <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carts as $cart): ?>
            <tr>
                <th scope="row"><?php safer_echo(get_product_name($cart["product_id"])); ?></th>
                <td>
                <input class="mx-1 w-25" id="quantity-<?php safer_echo($cart["product_id"]); ?>" min="0" max="<?= in_stock($cart["product_id"]); ?>" value="<?php safer_echo($cart["quantity"]); ?>" type="number"> 
                <a href="#" onClick="editCart(<?= safer_echo($cart["product_id"]);?>)">Edit</a>
                </td>
                <td>$<?php safer_echo(get_product_price($cart["product_id"])); ?></td>
                <td>$<?php safer_echo(get_product_price($cart["product_id"])*$cart["quantity"]); ?></td>
                <td><a href="#" class="text-danger" onClick="deleteCart(<?= safer_echo($cart["product_id"]);?>)">Delete</a></td>
            </tr>
            <?php endforeach; ?>
            <tbody>
       
    </table>
    <div class="float-right"><button class="btn btn-danger" onClick="deleteCart('all')">Clear All</button></div>
    <?php else: ?>
        <p>Empty cart, <a href="../shop/catalog.php">let's change that</a></p>
    <?php endif; ?>
</div>

<script>

    function makePurchase(){
        alert("TBD")
    }  

    function editCart(id) {
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let qt = Number(document.getElementById(`quantity-${id}`).value)
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert(json.cart.name + " quantity changed to " + qt);
                        location.reload();
                    } else {
                        alert(json.error);
                    }
                }
            }
        };
        
        xhttp.open("POST", "<?php echo getURL("api/addCart.php");?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //map any key/value data similar to query params
        xhttp.send(`id=${id}&qt=${qt}`);

    }
    function deleteCart(id) {
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successfully removed " + (id == "all" ? "all" : json.cart.name) + " from cart");
                        location.reload();
                    } else {
                        alert(json.error);
                    }
                }
            }
        };
        
        xhttp.open("POST", "<?php echo getURL("api/addCart.php");?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //map any key/value data similar to query params
        xhttp.send(`id=${id}&qt=0`);

    }
</script>

<?php require_once(__DIR__ . "/../partials/footer.php"); ?>