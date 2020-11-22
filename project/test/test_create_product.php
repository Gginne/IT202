<?php require_once(__DIR__ . "/../partials/header.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>
<h3>Create Product</h3>
<br>
<form method="POST">
	<div class="form-group">
		<label for="name">Name:</label>
		<input class="form-control" id="name" name="name"/>
	</div>
	<div class="form-group">
		<label for="quantity">Quantity:</label>
		<input class="form-control" type="number" id="quantity" name="quantity"/>
	</div>
	<div class="form-group">
		<label for="price">Price:</label>
		<input class="form-control" type="number" id="price" name="price" step="0.01"/>
	</div>
	<div class="form-group">
		<label for="description">Description:</label>
		<textarea class="form-control" id="description" name="description" rows="4" cols="50"></textarea>
	</div>
	<div class="form-group">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="visibility" id="visibility" value="1" checked>
			<label class="form-check-label" for="visibility">
				Visible
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0">
			<label class="form-check-label" for="visibility">
				Not Visible
			</label>
		</div>
	</div>
    <input class="btn btn-primary" type="submit" name="save" value="save" />
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
	$vis = $_POST["visibility"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Products (name, quantity, price, description, user_id, visibility) VALUES(:name, :quantity, :price, :desc,:user,:visibility)");
	$r = $stmt->execute([
		":name"=>$name,
		":quantity"=>$quantity,
		":price"=>$price,
		":desc"=>$desc,
		":user"=>$user,
		":visibility"=>$vis
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