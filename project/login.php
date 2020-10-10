<?php require_once(__DIR__ . "/partials/header.php"); ?>

<p class="display-4 text-center text-info">Login</p>
<form method="POST" class="mx-auto w-75">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required>
  </div>
  <div class="form-group">
    <label for="p1">Password: </label>
    <input type="password" class="form-control" name="password" id="p1" placeholder="Password" required>
  </div>
  <div class="form-group">
  <input type="submit" class="btn btn-primary" name="login" value="Login" />
  </div>
</form>

<div class="text-center">
<?php
if (isset($_POST["login"])) {
    $email = isset($_POST["email"]) ? $_POST["email"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;
   
    $isValid = true;
    if (!isset($email) || !isset($password)) {
        $isValid = false;
    }
    if (!strpos($email, "@")) {
        $isValid = false;
        echo "<br>Invalid email<br>";
    }
    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
            $stmt = $db->prepare("SELECT id, email, password from Users WHERE email = :email LIMIT 1");

            $params = array(":email" => $email);
            $r = $stmt->execute($params);
            echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                echo "uh oh something went wrong: " . var_export($e, true);
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
                    header("Location: home.php");
                }
                else {
                    echo "<br>Invalid password, get out!<br>";
                }
            }
            else {
                echo "<br>Invalid user<br>";
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