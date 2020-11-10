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
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>


<?php
//saving
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Products set name=:name, quantity=:quantity, price=:price, description=:desc where id=:id");
		//$stmt = $db->prepare("INSERT INTO F20_Eggs (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
		$r = $stmt->execute([
			":name"=>$name,
			":quantity"=>$quantity,
			":price"=>$price,
			":desc"=>$desc,
			":id"=>$id
		]);
		if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else{
		flash("ID isn't set, we need an ID in order to update");
	}
}
?>

<?php
//fetching
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Products where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Name</label><br>
	<input name="name" value="<?php echo $result["name"];?>"/>
	<br><br>
	<label for="quantity">Quantity</label><br>
	<input type="number" name="quantity" value="<?php echo $result["quantity"];?>"/>
	<br><br>
	<label for="price">Price</label><br>
	<input type="number" name="price" step="0.01" value="<?php echo $result["price"];?>"/>
	<br><br>
	<label for="description">Description</label><br>
	<textarea name="description" rows="4" cols="50"><?php echo $result["description"];?></textarea>
	<br><br>
	<input type="submit" name="save" value="Update"/>
</form>


<?php require(__DIR__ . "/../partials/flash.php"); ?>
<?php require_once(__DIR__ . "/../partials/footer.php"); ?>