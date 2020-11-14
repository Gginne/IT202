<?php require_once(__DIR__ . "/partials/header.php"); ?>

<div>
<?php
if (isset($_POST["register"])) {
    $email = isset($_POST["email"]) ? $_POST["email"] : null;
    $username = isset($_POST["username"]) ? $_POST["username"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;
    $confirm = isset($_POST["confirm"]) ? $_POST["confirm"] : null;
  
    $isValid = true;
    //check if passwords match on the server side
    if ($password !== $confirm) {
        flash("Passwords don't match");
        $isValid = false;
    }

    if (!isset($email) || !isset($password) || !isset($confirm)) {
        $isValid = false;
    }
    //TODO other validation as desired, remember this is the last line of defense
    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $db = getDB();
        if (isset($db)) {
            //here we'll use placeholders to let PDO map and sanitize our data
            $stmt = $db->prepare("INSERT INTO Users(email, username, password) VALUES(:email, :username, :password)");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":username" => $username, ":password" => $hash);
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                $login = "<a href='login.php'>login</a>";
                flash("Welcome! You successfully registered, please $login.");
            }
            else {
                if($e[0] == "23000"){
                    flash("Either username or email is already registered, please try again");
                } else {
                    flash("An error occurred, please try again");
                }
            }
        }
    }
    else {
        flash("There was a validation issue");
    }
}

if (!isset($email)) {
    $email = "";
}
if (!isset($username)) {
    $username = "";
}
?>
</div>

<h2>Register</h2>
<br>
<form method="POST">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="60" value="<?php safer_echo($email); ?>" required>
  </div>
  <div class="form-group">
    <label for="user">Username:</label>
    <input type="text" class="form-control" id="user" name="username" maxlength="60" value="<?php safer_echo($username); ?>" required>
  </div>
  <div class="form-group">
    <label for="p1">Password:</label>
    <input type="password" class="form-control" id="p1" name="password" minlength="6" maxlength="60" required>
  </div>
  <div class="form-group">
    <label for="p2">Confirm Password:</label>
    <input type="password" class="form-control" id="p2" name="confirm" minlength="6" maxlength="60" required>
  </div>
  <input type="submit" class="btn btn-primary" name="register" value="Register"/>
</form>

<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>