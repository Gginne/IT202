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
            $stmt = $db->prepare("INSERT INTO Users(email, username, password) VALUES(:email, :username, :password)");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":username" => $username, ":password" => $hash);
            $r = $stmt->execute($params);
            //let's just see what's returned
            echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                $login = "<a href='login.php'>login</a>";
                echo "<br>Welcome! You successfully registered, please $login.";
            }
            else {
                if($e[0] == "23000"){
                    echo "<br>Either username or email is already registered, please try again";
                } else {
                    echo "uh oh something went wrong: " . var_export($e, true);
                }
            }
        }
    }
    else {
        echo "There was a validation issue";
    }
}
?>
</div>

<h2>Register</h2>
<br/>
<form method="POST">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required/>
    <br><br>
    <label for="user">Username:</label><br>
    <input type="text" id="user" name="username" maxlength="60" value="<?php safer_echo($username); ?>" required />
    <br><br>
    <label for="p1">Password:</label><br>
    <input type="password" id="p1" name="password" required/>
    <br><br>
    <label for="p2">Confirm Password:</label><br>
    <input type="password" id="p2" name="confirm" required/>
    <br><br>
    <input type="submit" name="register" value="Register"/>
</form>
<br>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>