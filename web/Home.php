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
    
    <style>
        
         @import url('https://fonts.googleapis.com/css?family=Press+Start+2P');
        body {
            background:
        linear-gradient(90deg, #1b1b1b 10px, transparent 10px),
        linear-gradient(27deg, #222 5px, transparent 5px) 0px 10px,
        linear-gradient(207deg, #151515 5px, transparent 5px) 10px 0px,
        linear-gradient(27deg, #151515 5px, transparent 5px) 0 5px,
        linear-gradient(207deg, #222 5px, transparent 5px) 10px 5px,
        linear-gradient(#1d1d1d 25%, #1a1a1a 25%, #1a1a1a 50%, transparent 50%, transparent 75%, #242424 75%, #242424);
    
        background-color: #131313;
        background-size: 20px 20px;
            font-family: serif;
            font-family: 'Press Start 2P', cursive !important;
            color: black;
        }
    .buttons {
          background-color: darkgrey;
    border-color: black;
    border-style: solid;
    font-style: italic;
    font-weight: bold;
    text-align: center;
    opacity: 0.8;
    filter: alpha(opacity=80); /* For IE8 and earlier */
        height: 4vh;
    
}

.buttons:hover {
    color: rgba(247, 224, 106, .9);
    background-color: rgba(207,142,68,0.4);
}
    </style>
</head>
<body style="height: 100%">
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
                <a href="Home.php">Menu</a>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
      <li><a href="DnDMenu.html"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
    </ul>
    </div>
    </div>
</nav>
<br><br><br><br>
<div clss="container-fluid text-center" style="text-align: center">
    <div class="row content">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
    <form action="dnd_PDF.php" method="post">
        <h1 style="color: white">SELECT A CHARACTER</h1>
        <select class="form-control" id="chara" name="character" required class="col-xs-4">
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
        <br>
        <input type="submit" class="btn-lg buttons" value="Load Character">
        <a href="DnD_Builder.php" class="buttons btn-lg" style="color:black; display: inline-block;">Create A New Character</a>
            </form></div><div class="col-lg-3"></div></div>
        </div>
</body>
</html>