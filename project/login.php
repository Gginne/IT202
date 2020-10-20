<?php require_once(__DIR__ . "/partials/header.php"); ?>


<h2>Login</h2>
<br/>
<form method="POST">
    <label for="user">Username or Email:</label><br>
    <input type="text" id="user" name="user"/>
    <br><br>
    <label for="p1">Password:</label><br>
    <input type="password" id="p1" name="password" required/>
    <br><br>
    <input type="submit" name="login" value="Login"/>
</form>
<br>
<div>
<?php
if (isset($_POST["login"])) {
    $user = isset($_POST["user"]) ? $_POST["user"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;
   
    $isValid = true;
    if (!isset($user) && !isset($password)) {
        $isValid = false;
        flash("Missing fields");
    }
   
    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
            $stmt = $db->prepare("SELECT id, email, username, password from Users WHERE email = :user OR username = :user LIMIT 1");

            $params = array(":user" => $user);
            $r = $stmt->execute($params);
            echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong, please try again");
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];
                if (password_verify($password, $password_hash_from_db)) {
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password so we don't leak it beyond this page
                    //let's create a session for our user based on the other data we pulled from the table
                    $_SESSION["user"] = $result;//we can save the entire result array since we removed password
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles;
                    }
                    else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //on successful login let's serve-side redirect the user to the home page.
                    flash("Log in successful");
                    die(header("Location: home.php"));
                }
                else {
                    flash("<br>Invalid password, get out!<br>");
                }
            }
            else {
                flash("<br>Invalid username or email<br>");
            }
        }
    }
    else {
        flash("There was a validation issue");
    }
}
?>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__ . "/partials/footer.php"); ?>