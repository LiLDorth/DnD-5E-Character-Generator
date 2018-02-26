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


try {
    $query = "SELECT id FROM public.user WHERE username = :username";
	$statement = $db->prepare($query);
	$statement->bindValue(":username", $_SESSION['user'], PDO::PARAM_STR);
    $statement->execute();
	$user_id = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}

$_SESSION['user_id'] = $user_id['user_id'];
$id_user = $user_id['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage!</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <p class="navbar-brand"><b>Homepage</b></p>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="Home.php">Home</a>
            </li>
            <li>
                <a href="DnDMenu.html">Menu</a>
            </li>
        </ul>
    </div>
    </div>
</nav>
<br><br><br><br>
<div clss="container-fluid text-center">
    <div class="row content">
        <div class="col-lg-12">
    <form action="dnd_PDF.php" method="post">
        <h1>SELECT A CHARACTER</h1>
        <select class="form-control" id="chara" name="character" required>
<option value="" disabled selected hidden>Characters:</option>
            <?php
    foreach ($db->query("SELECT name, id FROM public.character WHERE user_id = $id_user;") as $row)
    {
	   $character = $row['name'];
	   $id = $row['id'];
	   if (isset($row))
	   {
            echo "<option  value='$id' name='$id'>$character</option>";
	   }
    }   
?>
        </select>
        <input type="submit" class="btn-lg btn-success" value="Load Character">
        <a href="DnD_Builder.php" class="btn-info btn-lg">Create A New Character</a>
            </form></div></div>
        </div>
</body>
</html>