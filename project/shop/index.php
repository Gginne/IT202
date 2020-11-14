<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
//$balance = getBalance();
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id,name, quantity, price, description, user_id from Products LIMIT 10");
    $r = $stmt->execute();
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
} else if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id,name, quantity, price, description, user_id from Products WHERE name like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<form method="POST">
    <div class="input-group">
        <input class="form-control w-25" name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
        <span class="input-group-btn">
            <input class="btn btn-primary" type="submit" value="Search" name="search"/>
        </span>
    </div>     
</form>

<div class="results mt-3">
    <?php if (count($results) > 0): ?>
        <div class="row">
            <?php foreach ($results as $r): ?>
                <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5>
                        <p class="card-text"><b><?php safer_echo($r["price"]); ?></b></p>
                        <div>
                            <a class="btn btn-success text-white" href="purchase.php?id=<?php safer_echo($r['id']); ?>&qt=1">Buy One</a>
                            <a class="btn btn-warning text-white" href="product.php?id=<?php safer_echo($r['id']); ?>">More</a>
                        </div>
                    </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>