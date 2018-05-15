<?php
try {
    global $db;
	$dbUrl = getenv('DATABASE_URL');
	if (empty($dbUrl)){
		$dbUrl = "postgres://postgres:server@localhost:5432/dnd";
	}
	$dbopts = parse_url($dbUrl);
	$currentHost = $dbopts["host"];
	$currentPort = $dbopts["port"];
	$currentUser = $dbopts["user"];
	$password = $dbopts["pass"];
	$name = ltrim($dbopts["path"], '/');
    
	$db = new PDO("pgsql:host=$currentHost;port=$currentPort;dbname=$name", $currentUser, $password);
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (PDOException $ex) {
	echo 'Bad Connection: ' . $ex->getMessage();
	die(); 
}

$table = htmlspecialchars($_REQUEST['table']);
$idNum = htmlspecialchars($_REQUEST['id']);

//SQL Injection preventive measures, PHP prohibts binding tablenames so this is a workaround
if ($table === 'race' || $table === 'class') {
try {
    $query = "SELECT * FROM $table WHERE id = :number";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $idNum, PDO::PARAM_INT);
    $statement->execute();
	$info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}
} else if ($table === 'spells') {
    $spell_id = json_decode($idNum, true);
    foreach($spell_id as $key => $id) {
    try {
    $query = "SELECT * FROM spells WHERE id = :number";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $id, PDO::PARAM_INT);
    $statement->execute();
	$temp = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}
    $info[$id] = $temp;
    }
}else {
    echo 'Nice Try SQL injection';
}

$info['type'] = $table;
if ($table == 'race') {
    $feats = (json_decode($info['features'], true));
    $info['features'] = $feats;
}
else if ($table == 'class') {
    $feats = (json_decode($info['features'], true));
    $info['features'] = $feats;
}

else if ($table == 'spells') {
    $cantrip = $info['cantrip'];
    $ritual = $info['ritual'];
    $school = $info['spell_school'];
    $lvl = $info['level'];
    $time = $info['time'];
    $duration = $info['duration'];
    $range = $info['range'];
    $description = $info['description'];
}

echo (json_encode($info));
die();
?>