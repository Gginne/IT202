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
    if(empty($cart)){
      flash("Fill cart before checkout");
      die(header("Location: ../shop/catalog.php"));
    }
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
$confirm = false;
$payment = "cash";
$final_address = "";
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
      flash("Order succesfully processed, cart cleared");
      $confirm = true;
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
        <span class="badge badge-secondary badge-pill"><?= count($cart); ?></span>
      </h4>
      <ul class="list-group mb-3 checkout">
      <?php foreach ($cart as $c): ?>
        <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0"><?= $c["quantity"]." ".get_product_name($c["product_id"]); ?></h6>
          </div>
          <span class="text-muted">$<?= $c["price"]*$c["quantity"]; ?></span>
        </li>
      <?php endforeach; ?>
      <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Final Cost</h6>
          </div>
          <span class="text-muted">$<?= $total; ?></span>
        </li>
      </ul>
    <?php if($confirm): ?>
      <div class="card p-2">
        <div class="btn-group">
          <button class="btn btn-success" id="confirm">Confirm</button>
          <button class="btn btn-danger"  id="cancel">Cancel</button>
        </div>
        
      </div>
    <?php endif; ?>
    </div>
    <div class="col-md-8 order-md-1">
      <h4 class="mb-3">Shipping address</h4>
      <form class="needs-validation" id="shipping" method="POST">
       
        <div class="mb-3">
          <label for="address">Address</label>
          <input type="text" class="form-control" name="address" id="address" placeholder="1234 Main St" <?= $confirm ? "value=".$_POST["address"]." disabled" : ""; ?> required>
          <div class="invalid-feedback">
            Please enter your shipping address.
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 mb-3">
            <label for="country">Country</label>
            <input type="text" class="form-control" name="country" id="country" placeholder="Enter country" <?= $confirm ? "value=".$_POST["country"]." disabled" : ""; ?> required>
            <div class="invalid-feedback">
              Please enter your country.
            </div>
          </div>
          <div class="col-md-4 mb-3">
          <label for="city">City</label>
            <input type="text" class="form-control" name="city" id="city" placeholder="Enter city" <?= $confirm ? "value=".$_POST["city"]." disabled" : ""; ?> required>
            <div class="invalid-feedback">
              Please enter your city.
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="zip">Zip</label>
            <input type="text" class="form-control" pattern="[0-9]{5}" name="zip" id="zip" placeholder="Enter ZIP code" <?= $confirm ? "value=".$_POST["zip"]." disabled" : ""; ?> required>
            <div class="invalid-feedback">
              Zip code required.
            </div>
          </div>
        </div>
        <hr class="mb-4">

        <h4 class="mb-3">Payment</h4>

        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="cash" value="cash" name="payment" type="radio" class="custom-control-input" <?= $payment == "cash" ? "checked" : "" ?> <?= $confirm  ? "disabled" : ""; ?> required>
            <label class="custom-control-label" for="cash">Cash</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="paypal" value="paypal" name="payment" type="radio" class="custom-control-input" <?= $payment == "paypal" ? "checked" : "" ?> <?= $confirm ? "disabled" : ""; ?> required>
            <label class="custom-control-label" for="paypal">Paypal</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="amex" value="amex" name="payment" type="radio" class="custom-control-input" <?= $payment == "amex" ? "checked" : "" ?> <?= $confirm ? "disabled" : ""; ?> required>
            <label class="custom-control-label" for="amex">American Express</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="visa" value="visa" name="payment" type="radio" class="custom-control-input" <?= $payment == "visa" ? "checked" : "" ?> <?= $confirm ? "disabled" : ""; ?> required>
            <label class="custom-control-label" for="visa">Visa</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="mastercard" value="mastercard" name="payment" type="radio" class="custom-control-input" <?= $payment == "mastercard" ? "checked" : "" ?> <?= $confirm ? "disabled" : ""; ?> required>
            <label class="custom-control-label" for="mastercard">Mastercard</label>
          </div>
        </div>
        <?php if(!$confirm): ?>
          <input class="btn btn-warning" type="submit" name="checkout" value="Checkout" />
        <?php endif; ?>
      </form>

      
    </div>
  </div>

</div>

<?php if($confirm): ?>
<script>
    
    document.getElementById("confirm").addEventListener('click', function(e){
      
      let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert("Successful purchase for the amout of $<?= $total; ?>");
                        window.location.href = "./my_cart.php"
                    } else {
                        alert(json.error);
                        window.location.href = "./my_cart.php"
                    }
                }
            }
        };
        
        xhttp.open("POST", "<?php echo getURL("api/purchase.php");?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //send request
        xhttp.send()
    })
    
   document.getElementById("cancel").addEventListener('click', function(e){
      
      let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert(json.msg);
                        window.location.href = "./my_cart.php"
                    } else {
                        alert(json.error);
                        location.reload();
                    }
                }
            }
        };
        
        xhttp.open("POST", "<?php echo getURL("api/purchase.php");?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //send request
        xhttp.send(`cancel=true`)
    })
    
</script>
<?php endif; ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>