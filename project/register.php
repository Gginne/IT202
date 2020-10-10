<?php require_once(__DIR__ . "/partials/header.php"); ?>

<p class="display-4 text-center text-info">Register</p>
<form method="POST" class="mx-auto w-75">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" name="email"  id="email" placeholder="Enter email" required>
  </div>
  <div class="form-group">
    <label for="p1">Password: </label>
    <input type="password" class="form-control" name="password" id="p1" placeholder="Enter password" required>
  </div>
  <div class="form-group">
    <label for="p2">Confirm: </label>
    <input type="password" class="form-control" name="confirm" id="p2" placeholder="Confirm password" required>
  </div>
  <div class="form-group">
  <input type="submit" class="btn btn-primary" name="register" value="Register" />
  </div>
</form>

<div class="text-center">
<?php
if (isset($_POST["register"])) {
    $email = isset($_POST["email"]) ? $_POST["email"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;
    $confirm = isset($_POST["confirm"]) ? $_POST["confirm"] : null;
  
    $isValid = true;
    //check if passwords match on the server side
    if ($password !== $confirm) {
        echo "Passwords don't match<br>";
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
            $stmt = $db->prepare("INSERT INTO Users(email, password) VALUES(:email, :password)");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":password" => $hash);
            $r = $stmt->execute($params);
            //let's just see what's returned
            echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                $login = "<a href='login.php'>login</a>";
                echo "<br>Welcome! You successfully registered, please $login.";
            }
            else {
                echo "uh oh something went wrong: " . var_export($e, true);
            }
        }
    }
    else {
        echo "There was a validation issue";
    }
}
?>
</div>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>