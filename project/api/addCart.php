<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
$testing = false;
if (isset($_GET["test"])) {
    $testing = true;
}

//TODO check if user can afford
//get number of eggs in ownership
//first egg is free
//each egg extra is base_cost * #_of_eggs
//$eggs_owned = 0;
//$base_cost = 10;
//$cost = $eggs_owned * $base_cost;
$total = calc_cart_total();
if($cost < 0){
	$response = ["status"=>400, "error"=>"Error calculating cost"];
	echo json_encode($response);
	die();
}



$egg["next_stage_time"] = $nst;
$user = get_user_id();
if (!$testing) {
    $db = getDB();
	$stmt = $db->prepare("SELECT MAX(id) as max from F20_Eggs");
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$max = (int)$result["max"];
	$max++;
	$egg["name"] .= " #$max";//forgot name was unique so just appending the "expected" id
    $stmt = $db->prepare("INSERT INTO F20_Eggs (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
    $r = $stmt->execute([
        ":name" => $egg["name"],
        ":state" => $egg["state"],
        ":br" => $egg["base_rate"],
        ":min" => $egg["mod_min"],
        ":max" => $egg["mod_max"],
        ":nst" => $egg["next_stage_time"],
        ":user" => $egg["user_id"]
    ]);
    if ($r) {
        $response = ["status" => 200, "egg" => $egg];
        echo json_encode($response);
        die();
    }
    else {
        $e = $stmt->errorInfo();
        $response = ["status" => 400, "error" => $e];
        echo json_encode($response);
        die();
    }
}
else {
    echo "<pre>" . var_export($egg, true) . "</pre>";
}

?>