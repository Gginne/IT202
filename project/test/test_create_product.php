<?php require_once(__DIR__ . "../partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST">
	<label for="name">Name</label>
	<input name="name" placeholder="Name"/>
	<label for="quantity">Quantity</label>
	<input type="number" name="quantity"/>
	<label for="price">Price</label>
	<input type="number" name="price" step="0.01"/>
	<label for="description">Description</label>
    <textarea name="description" rows="4" cols="50"></textarea>
    <input type="submit" name="save" value="save" />
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
	$nst = date('Y-m-d H:i:s');//calc
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Products (name, quantity, price, description, next_stage_time, user_id) VALUES(:name, :quantity, :price, :desc,:nst,:user)");
	$r = $stmt->execute([
		":name"=>$name,
		":quantity"=>$quantity,
		":price"=>$price,
		":desc"=>$desc,
		":nst"=>$nst,
		":user"=>$user
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__ . "../partials/footer.php"); ?>