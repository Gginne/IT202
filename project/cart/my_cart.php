<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
$page = 1;
$per_page = 6;
$carts = [];
$cart_total = 0;
$cart_cost = 0;

if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}

$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total, SUM(price*quantity) as cost FROM Carts c WHERE c.user_id = :user");
$stmt->execute([
    ":user" => get_user_id()
]);

$results = $stmt->fetch(PDO::FETCH_ASSOC);

if($results){
    $cart_cost = $results["cost"];
    $cart_total = (int)$results["total"];
}

$total_pages = ceil($cart_total / $per_page);
$offset = ($page-1) * $per_page;

$stmt = $db->prepare("SELECT product_id, quantity, price FROM Carts WHERE user_id = :user LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":user", get_user_id(), PDO::PARAM_STR);

$r = $stmt->execute();
$e = $stmt->errorInfo();

if($r){
    $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    flash("There was a problem fetching the cart");
}



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
                <td scope="row"><b><?= get_product_name($cart["product_id"]); ?></b> <a href="../shop/product.php?id=<?php safer_echo($cart["product_id"]); ?>">  View</a></td>
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
    <form class="form-inline float-right">
    <div class="form-group">
       <label for="cart-total"><b>Cart Total:  </b></label>
       <input class="form-control mx-2" style="max-width: 7rem;" name="cart-total" id="cart-total" type="text" value="$<?= $cart_cost ?>" readonly>
    </div>
    <div class="form-group mx-2">
        <a class="btn btn-success mx-1" href="./checkout.php">Buy Cart</a>
        <button class="btn btn-danger mx-1" onClick="deleteCart('all')">Clear Cart</button>
        
    </div>
    </form>
    <br><br>
    <nav aria-label="My Carts">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
            </li>
            <?php for($i = 0; $i < $total_pages; $i++):?>
            <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page+1) >= $total_pages?"disabled":"";?>">
                <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php else: ?>
        <p>Empty cart, <a href="../shop/catalog.php">let's change that</a></p>
    <?php endif; ?>
    
</div>

<script>

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
                        location.reload();
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
                        location.reload();
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