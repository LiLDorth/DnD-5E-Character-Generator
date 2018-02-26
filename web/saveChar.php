<?php
session_start();
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
if(isset($_SESSION['user'])) {
try {
    $query = "SELECT id FROM public.user WHERE username = :username";
	$statement = $db->prepare($query);
	$statement->bindValue(":username", $_SESSION['user'], PDO::PARAM_STR);
    $statement->execute();
	$user_id = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps1: " . $excep->getMessage();
	die();
} } else {
        echo "<script>
        alert('Session timed out, return to sign in');
        window.location.href='DnDMenu.html';
        </script>";
        die();
}

$character = htmlspecialchars($_REQUEST['char_name']);
$align = $_REQUEST['alignment'];
$str = htmlspecialchars($_REQUEST['Strength']);
$dex = htmlspecialchars($_REQUEST['Dexterity']);
$con = htmlspecialchars($_REQUEST['Constitution']);
$wis = htmlspecialchars($_REQUEST['Wisdom']);
$int = htmlspecialchars($_REQUEST['Intelligence']);
$char = htmlspecialchars($_REQUEST['Charisma']);
$lang = htmlspecialchars($_REQUEST['language']);
$ac = htmlspecialchars($_REQUEST['ac']);
$init = htmlspecialchars($_REQUEST['initiative']);
$speed = htmlspecialchars($_REQUEST['speed']);
$health = htmlspecialchars($_REQUEST['maxhp']);
$race = htmlspecialchars($_REQUEST['race']);
$class = htmlspecialchars($_REQUEST['class']);
$skills = $_REQUEST['skills'];
if ($race != 1)
    $health = ($health + floor((($con - 10) / 2)));
else
    $health = ($health + floor((($con - 10) / 2)) + floor((($dex - 10) / 2)));

$spells = array_merge($_REQUEST['cantripList'], $_REQUEST['spellList']);
$spellList = (json_encode($spells));

try { 
    $query = "INSERT INTO public.character (user_id, name, level, char_race, char_class, spells, align) VALUES (:id, :name, :lvl, :race, :class, :spellJson, :align);";
    $statement = $db->prepare($query);
	$statement->bindValue(":id", $user_id['id'], PDO::PARAM_STR);
    $statement->bindValue(":name", $character, PDO::PARAM_STR);
    $statement->bindValue(":lvl", '1', PDO::PARAM_INT);
    $statement->bindValue(":race", $race, PDO::PARAM_INT);
    $statement->bindValue(":class", $class, PDO::PARAM_INT);
    $statement->bindValue(":spellJson", $spellList, PDO::PARAM_STR);
    $statement->bindValue(":align", $align, PDO::PARAM_STR);
    $statement->execute();
} catch (PDOException $excep) {
	echo "Opps2: " . $excep->getMessage();
	die();
}

$char_id = $db->lastInsertId('character_id_seq');
try { 
    $query = "INSERT INTO public.stats (character_id, strength, dexterity, constitution, intelligence, wisdom, charisma, health, initiative, armor_class, skills) VALUES                                       (:id, :str, :dex, :cont, :int, :wis, :char, :health, :init, :armor, :skill);";
    $statement = $db->prepare($query);
	$statement->bindValue(":id", $char_id, PDO::PARAM_INT);
    $statement->bindValue(":str", $str, PDO::PARAM_INT);
    $statement->bindValue(":dex", $dex, PDO::PARAM_INT);
    $statement->bindValue(":cont", $con, PDO::PARAM_INT);
    $statement->bindValue(":int", $int, PDO::PARAM_INT);
    $statement->bindValue(":wis", $wis, PDO::PARAM_INT);
    $statement->bindValue(":char", $char, PDO::PARAM_INT);
    $statement->bindValue(":health", $health, PDO::PARAM_INT);
    $statement->bindValue(":init", $init, PDO::PARAM_INT);
    $statement->bindValue(":armor", $ac, PDO::PARAM_INT);
    $statement->bindValue(":skill", $skillList, PDO::PARAM_STR);
    $statement->execute();
} catch (PDOException $excep) {
	echo "Opps3: " . $excep->getMessage();
	die();
}

try { 
    $query = "UPDATE public.character SET char_stats = $char_id WHERE public.character.id = $char_id;";
    $statement = $db->prepare($query);
    $statement->execute();
} catch (PDOException $excep) {
	echo "Opps4: " . $excep->getMessage();
	die();
}

$_SESSION['character'] = $char_id;

header("Location: dnd_PDF.php");
die();
?>