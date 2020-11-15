<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
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
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (isset($result) && !empty($result)): ?>
    <div class="jumbotron bg-white border border-secondary">
        <h1 class="display-4"><?= $result["name"] ?></h1>
        <p class="lead">$<?= $result["price"] ?></p>
            <div class="input-group">
                <input class="mx-1" id="quantity" min="1" max="<?= $result["quantity"] ?>" value="1" type="number">
                <span class="input-group-btn">
                    <button class="btn btn-primary" onClick="addToCart()">Add to Cart</button>
                </span>
            </div>     
        <hr class="my-4">
        <p><?= $result["description"] ?></p>
        
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<script>
        
    
    function addToCart() {
        let id = <?php echo $id;?>;
        let qt = Number(document.getElementById("quantity").value)
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successfully added " + json.product.name + " to cart");
                        location.reload();
                    } else {
                        alert(json.error);
                    }
                }
            }
        };
        xhttp.open("POST", `<?php echo getURL("api/addCart.php");?>?id=${id}&qt=${qt}`, true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //map any key/value data similar to query params
        xhttp.send();

    }
</script>
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>