<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
//$balance = getBalance();
$query = "";
$category = "";
$order = "";
$filter = "";
$results = [];
$total = 0;
$page = 1;
$per_page = 8;

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

if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}


if (isset($_POST["search"]) || empty($query)) {
    $db = getDB();
    $qString = "SELECT id, name, quantity, price, description, user_id from Products WHERE visibility = 1 and name like :q and categories like :c $filter LIMIT :offset, :count";
    $qTotal = "SELECT count(*) as total from Products p WHERE p.visibility = 1 and p.name like :q and p.categories like :c";
    if(has_role("Admin")){
        $qString = "SELECT id, name, quantity, price, description, user_id from Products WHERE name like :q and categories like :c $filter LIMIT :offset, :count";
        $qTotal = "SELECT count(*) as total from Products p WHERE p.name like :q and p.categories like :c"; 
    }  

    $stmt = $db->prepare($qTotal);
    $stmt->execute([
        ":q" => "%$query%",
        ":c" => "%$category%"
    ]);

    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    if($results){
        $total = (int)$results["total"];
    }
    $total_pages = ceil($total / $per_page);
    $offset = ($page-1) * $per_page;

    
    $stmt = $db->prepare($qString);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $stmt->bindValue(":q", "%$query%", PDO::PARAM_STR);
    $stmt->bindValue(":c", "%$category%", PDO::PARAM_STR);
    $r = $stmt->execute();

    $e = $stmt->errorInfo();
    if($e[0] != "00000"){
        flash(var_export($e, true), "alert");
    } else if($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        flash("There was a problem fetching the results");
    }
    
}

?>
<form method="POST" class="mx-auto mb-3" style="width: 60rem;">
    <div class="input-group">
        <div class="input-group-prepend">
        <select class="form-control mx-1" id="price" name="price_filter" value="">
                <option value="">By Price</option>
                <option value="ASC" <?php echo ($order == "ASC" ? 'selected="selected"' : ''); ?> >Ascending</option>
                <option value="DESC" <?php echo ($order == "DESC" ? 'selected="selected"' : ''); ?> >Descending</option>
        </select>
        <select class="form-control mx-1" id="category" name="cat_filter" value="">
                <option value="">By Category</option>
                <option value="sneakers" <?php echo ($category == "sneakers" ? 'selected="selected"' : ''); ?> >Sneakers</option>
                <option value="shoes" <?php echo ($category == "shoes" ? 'selected="selected"' : ''); ?> >Shoes</option>
                <option value="velcro" <?php echo ($category == "velcro" ? 'selected="selected"' : ''); ?> >Velcro</option>
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
                <?php if($r): ?>
                    <div class="col-sm-3">
                    <div class="card my-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5>
                            <p class="card-text lead"><b>$<?php safer_echo($r["price"]); ?></b> <?= is_logged_in() ? '<small class="float-right text-muted">'.(in_stock($r["id"]) > 0 ? in_cart($r["id"]).' in cart' : "Out of Stock")."</small>" : "" ?></p>
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
    <nav aria-label="Products">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page+1) > $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
            </ul>
        </nav>
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