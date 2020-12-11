<?php require_once(__DIR__ . "/../partials/header.php"); ?>

<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

if (!is_visible($_GET["id"])){
    flash("Item not available");
    die(header("Location: catalog.php"));
}
?>

<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT Products.id,name,quantity,price,description, user_id, Users.username FROM Products as Products JOIN Users on Products.user_id = Users.id where Products.id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        flash("Error fetching product");
    }
}


if(isset($_POST["review"])){
    $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
    $rating = isset($_POST["rating"]) ? $_POST["rating"] : "";
    $stmt = $db->prepare("INSERT INTO Ratings (product_id, rating, comment, user_id) VALUES(:product, :rating, :comment, :user) on duplicate key update comment=:comment, rating=:rating");
    $r = $stmt->execute([
        ":product" => $id,
        ":rating" => $rating,
        ":comment" => $comment,
        ":user" => get_user_id()
    ]);
    if($r){
        flash("New rating posted");
    } else{
        flash("Something went wrong, rating not posted");
    }
}
$total = 0;
$page = 1;
$per_page = 8;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){
    }
}

$myComment = 1;
$myRating = "";

$qString = "SELECT rating, comment, user_id FROM Ratings LIMIT 10";
$qTotal = "SELECT count(*) as total from Ratings";

$stmt = $db->prepare($qString);
$stmt->execute();
$reviews = $stmt->fetchALL(PDO::FETCH_ASSOC);

foreach($reviews as $rev){
    if($rev["user_id"] == get_user_id()){
        $myComment = $rev["comment"];
        $myRating = $rev["rating"];
    }
}

?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="jumbotron bg-white border border-secondary">
        <h1 class="display-4"><?= $result["name"] ?></h1>
        <p class="lead">$<?= $result["price"] ?></p>
            <?php if(is_logged_in()): ?>
            <div class="input-group">
                <input class="mx-1" id="quantity" min="0" max="<?= $result["quantity"] ?>" value="<?= in_cart($result["id"]) ?>" type="number">
                
                <span class="input-group-btn">
                    <button class="btn btn-primary" onClick="addToCart(<?= $id;?>)">Add to Cart</button>
                    <button class="btn btn-danger" onClick="makePurchase()">Buy</button>
                </span>  
            </div>
            <?php endif; ?>     
        <hr class="my-4">
        <p><?= $result["description"] ?></p>
        
       
    </div>
    <div class="card">
        <div class="card-header">
            Reviews & Ratings
        </div>
        <?php if(is_logged_in()): ?>
        <div class="p-2">
            <form method="POST">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rt-1" value="1" <?= $myRating == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="rt-1">1</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rt-2" value="2" <?= $myRating == 2 ? "checked" : "" ?>>
                    <label class="form-check-label" for="rt-2">2</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rt-3" value="3" <?= $myRating == 3 ? "checked" : "" ?>>
                    <label class="form-check-label" for="rt-3">3</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rt-4" value="4" <?= $myRating == 4 ? "checked" : "" ?>>
                    <label class="form-check-label" for="rt-4">4</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rt-5" value="5" <?= $myRating == 5 ? "checked" : "" ?>>
                    <label class="form-check-label" for="rt-5">5</label>
                </div>
                <div class="form-group my-2">
                    <textarea class="form-control" id="comment" name="comment" rows="2" cols="10" placeholder="write a comment..."><?= $myComment ?></textarea>
                </div>
                <input type="submit" class="btn btn-warning" name="review" value="Post Review" />
            </form>
        </div>
        <hr class="my-3">
        <?php endif; ?>
        <div class="card-body">
            <?php foreach($reviews as $rev):?>
                <p class="lead"><?= $rev["rating"]." stars";?> <?= $rev["comment"];?></p>
                <hr class="my-2">
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<script>

    function makePurchase(){
        alert("TBD")
    }  
    
    function addToCart(id) {
        let qt = Number(document.getElementById("quantity").value)
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText)
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successfully added " + json.cart.quantity + " " + json.cart.name + " to cart");
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
</script>
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>