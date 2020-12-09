<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<?php
$db = getDB();
$cart = [];
$stmt = $db->prepare("SELECT product_id, quantity, price FROM Carts WHERE user_id = :user");
$r = $stmt->execute([
    ":user" => get_user_id()
]);

if($r){
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else{
    flash("There was a problem fetching the cart");
}

$total = 0;
$stmt = $db->prepare("SELECT SUM(price*quantity) as total FROM Carts c WHERE c.user_id = :user");
$stmt->execute([
    ":user" => get_user_id()
]);
$res =  $stmt->fetch(PDO::FETCH_ASSOC);
$total = $res["total"];

if(isset($_POST["checkout"])){
    $address = isset($_POST["address"]) ? $_POST["address"] : "";
    $city = isset($_POST["city"]) ? $_POST["city"] : "";
    $country = isset($_POST["country"]) ? $_POST["country"] : "";
    $zip = isset($_POST["zip"]) ? $_POST["zip"] : "";
    $payment = $_POST["payment"];

    $final_address = "$address $city, $country $zip";

    $stmt = $db->prepare("INSERT INTO Orders (total_price, payment_method, address, user_id) VALUES(:total, :payment, :address, :user)");
    $r = $stmt->execute([
        ":total" => $total,
        ":payment" => $payment,
        ":address" => $final_address,
        ":user" => get_user_id()
    ]);
    if ($r) {
        flash("Order succesfully processed");
    } else {
        flash("There was an error in order processing");
    }
}

?>
<style>
.checkout{
    max-height: 350px;
    overflow: scroll;
    -webkit-overflow-scrolling: touch;
}
</style>
<div class="container">

  <div class="row">
    <div class="col-md-4 order-md-2 mb-4">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Your cart</span>
        <span class="badge badge-secondary badge-pill"><?= count($cart) ?></span>
      </h4>
      <ul class="list-group mb-3 checkout">
      <?php foreach ($cart as $c): ?>
        <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0"><?= $c["quantity"]." ".get_product_name($c["product_id"]) ?></h6>
          </div>
          <span class="text-muted">$<?= $c["price"]*$c["quantity"] ?></span>
        </li>
      <?php endforeach; ?>
        
      </ul>

      <div class="card p-2">
        <input class="btn btn-warning" type="submit" name="checkout" value="Checkout" form="billing" />
         
      </div>
    </div>
    <div class="col-md-8 order-md-1">
      <h4 class="mb-3">Billing address</h4>
      <form class="needs-validation" id="billing" method="POST" onSubmit="makePurchase()">
       
        <div class="mb-3">
          <label for="address">Address</label>
          <input type="text" class="form-control" name="address" id="address" placeholder="1234 Main St" required>
          <div class="invalid-feedback">
            Please enter your shipping address.
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 mb-3">
            <label for="country">Country</label>
            <input type="text" class="form-control" name="country" id="country" placeholder="Enter country" required>
            <div class="invalid-feedback">
              Please enter your country.
            </div>
          </div>
          <div class="col-md-4 mb-3">
          <label for="city">City</label>
            <input type="text" class="form-control" name="city" id="city" placeholder="Enter city" required>
            <div class="invalid-feedback">
              Please enter your city.
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="zip">Zip</label>
            <input type="text" class="form-control" pattern="[0-9]{5}" name="zip" id="zip" placeholder="Enter ZIP code" required>
            <div class="invalid-feedback">
              Zip code required.
            </div>
          </div>
        </div>
        <hr class="mb-4">

        <h4 class="mb-3">Payment</h4>

        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="cash" value="cash" name="payment" type="radio" class="custom-control-input" checked required>
            <label class="custom-control-label" for="credit">Cash</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="paypal" value="paypal" name="payment" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="credit">Paypal</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="amex" value="amex" name="payment" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="debit">American Express</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="visa" value="visa" name="payment" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="paypal">Visa</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="mastercard" value="mastercard" name="payment" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="paypal">Mastercard</label>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>

<script>

    function makePurchase(){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successful purchase for the amout of $<?= $total; ?>");
                        location.reload();
                    } else {
                        alert(json.error);
                    }
                }
            }
        };
        
        xhttp.open("POST", "<?php echo getURL("api/purchase.php");?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    }  

    
</script>

<?php require_once(__DIR__ . "/../partials/footer.php"); ?>