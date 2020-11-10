<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>

<form method="POST">
	<label for="name">Name</label><br>
	<input name="name" placeholder="Name"/>
	<br><br>
	<label for="quantity">Quantity</label><br>
	<input type="number" name="quantity" placeholder="Quantity"/>
	<br><br>
	<label for="price">Price</label><br>
	<input type="number" name="price" step="0.01" placeholder="Price"/>
	<br><br>
	<label for="description">Description</label><br>
	<textarea name="description" rows="4" cols="50"></textarea>
	<br><br>
    <input type="submit" name="save" value="save" />
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Products (name, quantity, price, description, user_id) VALUES(:name, :quantity, :price, :desc,:user)");
	$r = $stmt->execute([
		":name"=>$name,
		":quantity"=>$quantity,
		":price"=>$price,
		":desc"=>$desc,
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
<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>