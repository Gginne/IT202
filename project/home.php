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
$qLiked= "SELECT p.id, p.name, p.quantity, p.price, p.description, p.user_id FROM Products p
          LEFT JOIN Ratings r ON p.id=r.product_id 
          WHERE r.user_id=:id ORDER BY r.rating DESC LIMIT 4";

$qRecent = "SELECT p.id, p.name, p.quantity, p.price, p.description, p.user_id FROM Products p
            LEFT JOIN OrderItems oi ON p.id=oi.product_id 
            WHERE oi.user_id=:id ORDER BY oi.created DESC LIMIT 4";

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
    <p class="lead">MOST LIKED</p>
    <div class="row my-4">
        <?php if (empty($liked)): ?>
            <small>Not Purchases Rated</small>
        <?php else: ?>
            <?php foreach ($liked as $l): ?>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title my-1"><?php safer_echo($l["name"]); ?> </h5>
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
                
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>

<?php require_once(__DIR__ . "/partials/footer.php"); ?>