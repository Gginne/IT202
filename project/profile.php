<?php require_once(__DIR__."/partials/header.php") ?>

<?php 
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

$db = getDB();
//save data if we submitted the form

$user = null;
$edit = true;
if(isset($_GET["id"]) && has_role("Admin")){
    $user = $_GET["id"];
    $edit = false;
} else if(!has_role("Admin")){
    $user = get_user_id();
}

$visibility = is_public($user);

if (isset($_POST["saved"])) {
    $isValid = true;
    //check if our email changed
    $newEmail = get_email();
    $visibility = $_POST["visibility"];
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Email already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Username is already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }
    if ($isValid) {
        $newVisibility = $_POST["visibility"];
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username, visibility= :visibility where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":visibility" => $newVisibility, ":id" => get_user_id()]);
        if ($r) {
            flash("Updated profile");
        }
        else {
            flash("Error updating profile");
        }
        //password is optional, so check if it's even set
        //if so, then check if it's a valid reset request
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);
                //this one we'll do separate
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
                if ($r) {
                    flash("Succesfully reset password");
                }
                else {
                    flash("Error resetting password");
                }
            } else {
                flash("Passwords must match");
            }
        }
//fetch/select fresh data in case anything changed
        $stmt = $db->prepare("SELECT email, username, visibility from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            $visibility = $result["username"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
    }
    else {
        //else for $isValid, though don't need to put anything here since the specific failure will output the message
    }
}


?>
<h2><?= get_username()."'s" ?> profile</h2>
<br>
<form method="POST">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="100" value="<?php safer_echo(get_email()); ?>" required>
  </div>
  <div class="form-group">
    <label for="user">Username:</label>
    <input type="text" class="form-control" id="user" name="username" maxlength="60" value="<?php safer_echo(get_username()); ?>" required>
  </div>
  <div class="form-group">
    <label for="p1">Password:</label>
    <input type="password" class="form-control" id="p1" name="password" minlength="6" maxlength="60" required>
  </div>
  <div class="form-group">
    <label for="p2">Confirm Password:</label>
    <input type="password" class="form-control" id="p2" name="confirm" minlength="6" maxlength="60" required>
  </div>
  <div class="form-group">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="visibility" id="visibility" value="1" <?php echo $visibility == 1 ? "checked": "";?>>
			<label class="form-check-label" for="visibility">
				Public
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="visibility" id="visibility" value="0" <?php echo $visibility == 0 ? "checked": "";?>>
			<label class="form-check-label" for="visibility">
				Private
			</label>
		</div>
	</div>
  <input type="submit" class="btn btn-primary" name="saved" value="Save Profile"/>
</form>


<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__."/partials/footer.php") ?>