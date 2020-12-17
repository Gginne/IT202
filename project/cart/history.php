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
$per_page = 8;
$orders = [];
$total = 0;
$cost = 0;
$user = null;

$category = "";
$price = "";
$order = "";
$date = "oi.created=oi.created";
$range = "";

if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}
if(isset($_GET["id"]) && has_role("Admin")){
    $user = $_GET["id"];
} else if(!has_role("Admin")){
    $user = get_user_id();
}
if (isset($_POST["cat_filter"])) {
    $category = $_POST["cat_filter"];
}

if (isset($_POST["date_filter"])) {
    $range = $_POST["date_filter"];
    $t = "";
    if($range == "hour"){
        $t = "HOUR, -1,"; 
    } else if ($range == "day"){
        $t = "DAY, -1,";
    } else if ($range == "week"){
        $t = "DAY, -7,"; 
    } else if ($range == "month"){
        $t = "MONTH, -1,"; 
    } else if ($range == "year") {
        $t = "YEAR, -1,"; 
    }
    if($range != ""){
        $date = "oi.created BETWEEN TIMESTAMPADD($t CURRENT_TIMESTAMP()) AND CURRENT_TIMESTAMP()";
    }
}

if (isset($_POST["price_filter"])) {
    $order = $_POST["price_filter"];
    $price .=  "ORDER BY oi.unit_price*oi.quantity $order";
}


$db = getDB();
$stmt = null;
$qtotal = null;
if(has_role("Admin") && $user == null){
    #SELECT ALL ORDERS
    $stmt = $db->prepare("SELECT p.categories, o.created, o.user_id, o.address, o.payment_method, oi.product_id, oi.quantity, oi.unit_price FROM OrderItems AS oi JOIN Orders AS o ON oi.orderRef=o.id JOIN Products AS p ON oi.product_id=p.id  WHERE p.categories like :c AND $date $price LIMIT :offset, :count");
    $qtotal = $db->prepare("SELECT count(*) as total, sum(oi.unit_price*oi.quantity) as cost FROM OrderItems AS oi JOIN Products AS p ON oi.product_id=p.id WHERE p.categories like :c AND $date");
} else{
    #SELECT USER'S ORDERS
    $stmt = $db->prepare("SELECT p.categories, o.created, o.user_id, o.address, o.payment_method, oi.product_id, oi.quantity, oi.unit_price FROM OrderItems AS oi JOIN Orders AS o ON oi.orderRef=o.id JOIN Products AS p ON oi.product_id=p.id WHERE o.user_id=:user AND p.categories like :c AND $date $price LIMIT :offset, :count");
    $qtotal = $db->prepare("SELECT count(*) as total, sum(oi.unit_price*oi.quantity) as cost FROM OrderItems AS oi JOIN Products AS p ON oi.product_id=p.id WHERE oi.user_id=:user AND p.categories like :c AND $date");
    $stmt->bindValue(":user", $user, PDO::PARAM_STR);
    $qtotal->bindValue(":user", $user, PDO::PARAM_STR);
}
$offset = ($page-1) * $per_page;
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":c", "%$category%", PDO::PARAM_STR);
$qtotal->bindValue(":c", "%$category%", PDO::PARAM_STR);

$r = $qtotal->execute();
$res = $qtotal->fetch(PDO::FETCH_ASSOC);



$total = $res["total"];
$cost = $res["cost"];
$total_pages = ceil($total / $per_page);


$r = $stmt->execute();
$orders = $stmt->fetchALL(PDO::FETCH_ASSOC);

?>
<h3><?= $user != null ? get_username($user)."'s" : "All" ?> Purchases </h3>
<?php if($user != null && has_role("Admin")): ?><a href="./history.php?">view all</a><?php endif; ?>
<form method="POST" class="form-inline my-1">
<div class="input-group w-50">
       
        <select class="form-control mx-1" id="price" name="price_filter" value="">
                <option value="">By Price</option>
                <option value="ASC" <?php echo ($order == "ASC" ? 'selected="selected"' : ''); ?> >Ascending</option>
                <option value="DESC" <?php echo ($order == "DESC" ? 'selected="selected"' : ''); ?> >Descending</option>
        </select>
        <?php if(has_role("Admin")): ?>
        <select class="form-control mx-1" id="date" name="date_filter" value="">
                <option value="">By Date</option>
                <option value="hour" <?php echo ($range == "hour" ? 'selected="selected"' : ''); ?> >Last Hour</option>
                <option value="day" <?php echo ($range == "day" ? 'selected="selected"' : ''); ?> >Last Day</option>
                <option value="week" <?php echo ($range == "week" ? 'selected="selected"' : ''); ?> >Last Week</option>
                <option value="month" <?php echo ($range == "month" ? 'selected="selected"' : ''); ?> >Last Month</option>
                <option value="year" <?php echo ($range == "year" ? 'selected="selected"' : ''); ?> >Last Year</option>
                
        </select>
        <select class="form-control mx-1" id="category" name="cat_filter" value="">
                <option value="">By Category</option>
                <option value="sneakers" <?php echo ($category == "sneakers" ? 'selected="selected"' : ''); ?> >Sneakers</option>
                <option value="shoes" <?php echo ($category == "shoes" ? 'selected="selected"' : ''); ?> >Shoes</option>
                <option value="velcro" <?php echo ($category == "velcro" ? 'selected="selected"' : ''); ?> >Velcro</option>
                <option value="boots" <?php echo ($category == "boots" ? 'selected="selected"' : ''); ?> >Boots</option>
                <option value="flip-flops" <?php echo ($category == "flip-flops" ? 'selected="selected"' : ''); ?> >Flip-Flops</option>
        </select>
        <?php endif; ?>
        </div>
    <span class="input-group-btn">
            <input class="btn btn-primary text-white" type="submit" value="Filter" name="filter"/>
    </span>
    
    <div class="form-group ml-auto">
       <label for="cart-total"><b>Total Purchase Cost:  </b></label>
       <input class="form-control mx-2" style="max-width: 7rem;" name="cart-total" id="cart-total" type="text" value="$<?= $cost ?>" readonly>
    </div>
</form>

<div class="results mt-3">
    <?php if (count($orders) > 0): ?>
    <table class="table">
        <thead>
            <tr>
            <?php if(has_role("Admin")): ?>
            <th scope="col">User</th>
            <?php endif; ?>
            <th scope="col">Product</th>
            <th scope="col">Quantity</th>
            <th scope="col">Order Price</th>
            <th scope="col">Payment</th>
            <th scope="col">Date</th>
            <th scope="col">Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <?php if(has_role("Admin")): ?>
                <td scope="row"><a href="./history.php?id=<?= safer_echo($order["user_id"]);?>"><?=get_username($order["user_id"])?></a> </td>
                <?php endif; ?>
                <td scope="row"><b><?= get_product_name($order["product_id"]); ?></b> </td>
                <td scope="row"><?= safer_echo($order["quantity"]); ?></td>
                <td scope="row">$<?php safer_echo($order["unit_price"]*$order["quantity"]); ?></td>
                <td scope="row"><?php safer_echo($order["payment_method"]); ?></td>
                <td scope="row"><?php safer_echo($order["created"]); ?></td>
                <td scope="row"><?php safer_echo($order["address"]); ?></td>
            </tr>
            <?php endforeach; ?>
            <tbody>
       
    </table>
       
    <nav aria-label="Purchases">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                <a class="page-link" href=<?= has_role("Admin") && $user != null ? "?id=$user&page=".($page-1) : "?page=".($page-1); ?> tabindex="-1">Previous</a>
            </li>
            <?php for($i = 0; $i < $total_pages; $i++):?>
            <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href=<?= has_role("Admin") && $user != null ? "?id=$user&page=".($i+1) : "?page=".($i+1); ?>><?php echo ($i+1);?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page+1) > $total_pages?"disabled":"";?>">
                <a class="page-link" href=<?= has_role("Admin") && $user != null ? "?id=$user&page=".($page+1) : "?page=".($page+1); ?> >Next</a>
            </li>
        </ul>
    </nav>
    <?php else: ?>
        <p>No purchases yet, <a href="../shop/catalog.php">let's change that</a></p>
    <?php endif; ?>
    
</div> 
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>