<?php 
    require_once(__DIR__. "/../lib/db.php");
?>

<h1>Login</h1>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="text" id="email" name="email" placeholder="Enter email">
    <input type="password" id="password" name="password" placeholder="Enter password">
    <input type="submit" id="login" name="login" value="login">
</form>

<?php 

    if(isset($_POST["login"])){
        
        $email = isset($_POST["email"]) ? $_POST["email"] : null;
        $password = isset($_POST["password"] ) ? $_POST["password"] : null;
        $isValid = true;
       
        if(!isset($email) || !isset($password)){
            $isValid = false;
        }

        if($email == "" || $password == ""){
            echo "Missing fields <br>";
            $isValid = false;
        }

        if(!strpos($email, "@")){
            $isValid = false;
             echo "<br>Invalid email<br>";
           }

        if($isValid){
            $db = getDB();
            if(isset($db)){
                //here we'll use placeholders to let PDO map and sanitize our data
                $stmt = $db->prepare("SELECT email, password from Users WHERE email = :email LIMIT 1");
                //here's the data map for the parameter to data
                $params = array(":email"=>$email);
                $r = $stmt->execute($params);
                //let's just see what's returned
                $e = $stmt->errorInfo();
                if($e[0] != "00000"){
                    echo "uh oh something went wrong: " . var_export($e, true);
                }

                //we'll tell pdo to give it to us as an associative array
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if($result && isset($result["password"])){
                    $password_hash_from_db = $result["password"];
                    if(password_verify($password, $password_hash_from_db)){

                    //Save current user login to session
                    session_start();
                    unset($result["password"]);
                    $_SESSION["user"] = $result;
                    
                    //Login message
                    echo "<br>Welcome! You're logged!<br>"; 
                    }
                   
                } else{ 
                    echo "<br>Invalid user<br>";
                }
            }
        }
        else {
            echo "There was a validation issue";
        }
    }
?>
