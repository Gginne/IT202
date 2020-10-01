<?php 
    require_once(__DIR__. "/../lib/db.php");
?>

<h1>Register</h1>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="text" id="email" name="email" placeholder="Enter email">
    <input type="password" id="password" name="password" placeholder="Enter password">
    <input type="password" id="confirm" name="confirm" placeholder="Confirm password">
    <input type="submit" id="register" name="register" value="register">
</form>

<?php 

    if(isset($_POST["register"])){
        
        $email = isset($_POST["email"]) ? $_POST["email"] : null;
        $password = isset($_POST["password"]) ? $_POST["password"] : null;
        $confirm = isset($_POST["confirm"]) ? $_POST["confirm"] : null;
        $isValid = true;
        $errMsg = "";
       
        if(!isset($email) || !isset($password) || !isset($confirm)){
            $errMsg = "Undefined fields";
            $isValid = false;
        }

        if($email == "" || $password == "" || $confirm == "" ){
            $errMsg = "Missing fields <br>";
            $isValid = false;
        }

        if($password != $confirm){
            $errMsg = "Passwords must match <br>";
            $isValid = false;
        }

        
        if($isValid){
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db = getDB();
            if(isset($db)){
                $stmt = $db->prepare("INSERT INTO Users(email, password) VALUES(:email, :password)");

                $params = array(":email"=>$email, ":password"=>$hash);
                $r = $stmt->execute($params);

                $login = "<a href='/IT202/loginreg/login.php'>login</a>";
                $e = $stmt->errorInfo();
                if($e[0] == "00000"){
                    echo "<br>Welcome! You successfully registered, please {$login}.";
                }
                else{
                    echo "uh oh something went wrong: " . var_export($e, true);
                }
            }
        }
        else{
            echo "There was a validation issue: <br>"; 
            echo $errMsg;
        }
    }
?>


