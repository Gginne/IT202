<?php 
    require_once(__DIR__. "/../lib/db.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Store202 || Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="text" id="email" name="email" placeholder="Enter email">
        <input type="password" id="password" name="password" placeholder="Enter password">
        <input type="password" id="confirm" name="confirm" placeholder="Confirm password">
        <input type="submit" id="register" name="register" value="register">
    </form>

    <?php 
        if(isset($_POST["register"])){
            $db = getDB();
            echo var_dump($db);
        }
    ?>
</body>
</html>

