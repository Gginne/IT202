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
$orders = [];
$total = 0;
$offset = ($page-1) * $per_page;
$user = null;
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


$db = getDB();
$stmt = null;
$qtotal = null;
if(has_role("Admin") && $user == null){
    #SELECT ALL ORDERS
    $stmt = $db->prepare("SELECT o.created, o.user_id, o.address, o.payment_method, oi.product_id, oi.quantity, oi.unit_price FROM OrderItems AS oi INNER JOIN Orders AS o ON oi.orderRef=o.id LIMIT :offset, :count");
    $qtotal = $db->prepare("SELECT count(*) as total FROM OrderItems");
} else{
    #SELECT USER'S ORDERS
    $stmt = $db->prepare("SELECT o.created, o.user_id, o.address, o.payment_method, oi.product_id, oi.quantity, oi.unit_price FROM OrderItems AS oi INNER JOIN Orders AS o ON oi.orderRef=o.id WHERE o.user_id=:user LIMIT :offset, :count");
    $qtotal = $db->prepare("SELECT count(*) as total FROM OrderItems WHERE user_id=:user");
    $stmt->bindValue(":user", $user, PDO::PARAM_STR);
    $qtotal->bindValue(":user", $user, PDO::PARAM_STR);
}

$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);


$qtotal->execute();

$t = $qtotal->fetch(PDO::FETCH_ASSOC);

$total = $t["total"];
$total_pages = ceil($total / $per_page);

$stmt->execute();
$orders = $stmt->fetchALL(PDO::FETCH_ASSOC);



?>
<h3><?= $user != null ? get_username($user)."'s" : "All" ?> Purchases </h3>
<?php if($user != null && has_role("Admin")): ?><a href="./history.php?">view all</a><?php endif; ?>
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
       
    <nav aria-label="Orders">
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
        <p>No purchases yet, <a href="../shop/catalog.php">let's change that</a></p>
    <?php endif; ?>
    
</div> 
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>