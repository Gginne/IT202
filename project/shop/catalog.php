<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
//$balance = getBalance();
$query = "";
$category = "";
$filter = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}

if (isset($_POST["cat_filter"])) {
    $category = $_POST["cat_filter"];
}

if (isset($_POST["price_filter"])) {
    $order = $_POST["price_filter"];
    $filter .=  "ORDER BY price $order";
}

if (empty($query)) {
    $db = getDB();
    $qString = "SELECT id,name, quantity, price, description, user_id from Products WHERE categories like :c $filter LIMIT 10";
    $stmt = $db->prepare($qString);
    $r = $stmt->execute([":c" => "%$category%"]);  
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
} else if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $qString = "SELECT id,name, quantity, price, description, user_id from Products WHERE name like :q and categories like :c $filter LIMIT 10";
    $stmt = $db->prepare($qString);
    $r = $stmt->execute([
        ":q" => "%$query%",
        ":c" => "%$category%"
    ]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<form method="POST" class="mx-auto mb-3" style="width: 60rem;">
    <div class="input-group">
        <div class="input-group-prepend">
        <select class="form-control mx-1" id="price" name="price_filter" value="">
                <option value="">By Price</option>
                <option value="ASC">Ascending</option>
                <option value="DESC">Descending</option>
        </select>
        <select class="form-control mx-1" id="category" name="cat_filter" value="">
                <option value="">By Category</option>
                <option value="sneakers">Sneakers</option>
                <option value="shoes">Shoes</option>
                <option value="velcro">Velcro</option>
        </select>
        
        </div>
        <input class="form-control mx-2" name="query" placeholder="Enter Product..." value="<?php safer_echo($query); ?>"/>
        <span class="input-group-btn">

            <input class="btn btn-primary text-white" type="submit" value="Search" name="search"/>
        </span>
    </div>     
</form>

<div class="results mt-3">
    <?php if (count($results) > 0): ?>
        <div class="row">
            <?php foreach ($results as $r): ?>
                <?php if(is_visible($r["id"]) && $r): ?>
                    <div class="col-sm-3">
                    <div class="card my-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5>
                            <p class="card-text lead"><b>$<?php safer_echo($r["price"]); ?></b> <?= is_logged_in() ? '<small class="float-right text-muted">'.in_cart($r["id"]).' in cart</small>' : "" ?></p>
                            <div>
                                <?php if(is_logged_in()): ?>
                                    <button class="btn btn-white border border-dark" onClick="addOneToCart(<?php safer_echo($r['id']); ?>, <?= in_cart($r['id'])+1; ?>)"
                                    <?= in_cart($r['id']) >= $r['quantity'] ? "disabled" : ""; ?>>Add One</button>
                                <?php endif; ?>
                                <a class="btn btn-white border border-dark" href="product.php?id=<?php safer_echo($r['id']); ?>">More</a>
                            </div>
                        </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<script>
        
    
    function addOneToCart(id, qt) {
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successfully added 1 " + json.cart.name + " to cart");
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
</script>
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>