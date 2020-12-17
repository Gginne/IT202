<?php require_once(__DIR__ . "/partials/header.php"); ?>

<?php

if (!is_logged_in()){
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}

?>

<?php 
$db = getDB();
$liked = [];
$recent = [];
$qLiked= "SELECT p.id, p.name, p.quantity, p.price,  p.user_id, r.rating FROM Products p
          LEFT JOIN Ratings r ON p.id=r.product_id 
          WHERE r.user_id=:id ORDER BY r.rating DESC LIMIT 4";

$qRecent = "SELECT p.id, p.name, oi.quantity, oi.created, p.price, p.user_id FROM Products p
            LEFT JOIN OrderItems oi ON p.id=oi.product_id 
            WHERE oi.user_id=:id ORDER BY oi.created  DESC LIMIT 8";

$stmt = $db->prepare($qLiked);
$stmt->execute([":id" => get_user_id()]);
$liked = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare($qRecent);
$stmt->execute([":id" => get_user_id()]);
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<h1>Welcome, <?php echo get_username(); ?></h1>

<div class="container mt-4">
    <!-- # MOST LIKED -->
    <p class="lead">BEST RATED PURCHASES</p>
    <div class="row my-4">
        <?php if (empty($liked)): ?>
            <small>Not Purchases Rated</small>
        <?php else: ?>
            <?php foreach ($liked as $l): ?>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title my-1"><?php safer_echo($l["name"]); ?> </h5>
                        <span>
                            <?php for($star=1; $star<=5; $star++): ?>
                                <?php if($l["rating"] - $star >= 0): ?>
                                    <i class="fas fa-star text-warning mb-2"></i>
                                <?php else: ?>
                                    <i class="fas fa-star text-muted mb-2"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <small class="float-right text-muted">My Rating</small>
                            <p class="card-text lead"><b>$<?php safer_echo($l["price"]); ?></b> </p>
                            <a class="btn btn-block btn-white border border-dark" href="./shop/product.php?id=<?php safer_echo($l['id']); ?>">View</a>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- # RECENTLY PURCHASES -->
    <p class="lead">RECENT PURCHASES</p>
    <div class="row my-4">
        <?php if (empty($recent)): ?>
            <small>Not Purchases Yet</small>
        <?php else: ?>
            <?php foreach ($recent as $r): ?>
            <div class="col-sm-3 mb-4">
                <div class="card">
                    <small class="ml-auto px-2"><?php safer_echo($r["created"]); ?></small>
                    <div class="card-body">
                        <h5 class="card-title mb-1"><?php safer_echo($r["name"]); ?> </h5>
                        
                            
                        <p class="card-text lead">
                            <b>$<?php safer_echo($r["price"]*$r["quantity"]); ?> Total</b>     
                            <small class="float-right text-muted"><?php safer_echo($r["quantity"]); ?> bought</small>
                        </p>
                     
                        <a class="btn btn-block btn-white border border-dark" href="./shop/product.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>