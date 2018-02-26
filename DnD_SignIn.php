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


$username = $_POST['user'];
$hashedPass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
$type = $_POST['formType'];

try {
    if($type == 'Sign In') { 
        $query = "SELECT password FROM public.user WHERE username = :name;";
        $statement = $db->prepare($query);
    }
    else if($type == 'Create User') {
    $query = "INSERT INTO public.user (username, password) VALUES (:name, :pass);";
        $statement = $db->prepare($query);
        $statement->bindValue(":pass", $hashedPass, PDO::PARAM_STR);
    }
    
    
	$statement->bindValue(":name", $username, PDO::PARAM_STR);
    $statement->execute();
	$info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}

if ($type == 'Sign In') {
    if(password_verify($_POST["pass"],$info['password'])){
        $_SESSION['user'] = $_POST['user'];
        header("Location: Home.php");
        echo "gets herehere";
        die();
    }
}
else
    header("Location: DnDMenu.html");
die();
?>