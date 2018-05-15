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

if(isset($_SESSION['character']))
    $char_id = $_SESSION['character'];
else if (isset($_POST['character']))
    $char_id = $_POST['character'];
else {
    echo "<script>
        alert('Session timed out, return to sign in');
        window.location.href='DnDMenu.html';
        </script>";
        die();
}

try {
    $query = "SELECT * FROM public.character WHERE id = :number;";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $char_id, PDO::PARAM_INT);
    $statement->execute();
	$char_info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}

$name = $char_info['name'];
$level = $char_info['level'];
$descript = $char_info['description'];
$spellList = $char_info['spells'];
$race_id = $char_info['char_race'];
$class_id = $char_info['char_class'];
$align = $char_info['align'];

try {
    $query = "SELECT * FROM public.stats WHERE character_id = :number;";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $char_id, PDO::PARAM_INT);
    $statement->execute();
	$stat_info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}
$str = $stat_info['strength'];
$dex = $stat_info['dexterity'];
$const = $stat_info['constitution'];
$wis = $stat_info['wisdom'];
$intel = $stat_info['intelligence'];
$char = $stat_info['charisma'];

$ac = $stat_info['armor_class'];
$init = $stat_info['initiative'];
$health = $stat_info['health'];

try {
    $query = "SELECT * FROM public.race WHERE id = :number;";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $race_id, PDO::PARAM_INT);
    $statement->execute();
	$race_info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}

try {
    $query = "SELECT * FROM public.class WHERE id = :number;";
	$statement = $db->prepare($query);
	$statement->bindValue(":number", $class_id, PDO::PARAM_INT);
    $statement->execute();
	$class_info = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $excep) {
	echo "Opps: " . $excep->getMessage();
	die();
}

    $spell_id = json_decode($spellList, true);
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Character Sheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="DnD_Sheet_CSS.CSS">
    
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
            
        }
        nav {
    font-family: 'Press Start 2P', cursive !important;
}
        form {
            background-color: whitesmoke;
            border-radius: 2%;
            font-family: serif;
            color: black !important;;
        }
        .atr {
            margin: 5%;
        }
        
    </style>
</head>
<body onload="start()">
    <nav class="navbar navbar-default  navbar-inverse" role="navigation">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        </button>
        <p class="navbar-brand"><b>Character Sheet</b></p>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li>
                <a href="Home.php">Menu</a>
            </li>
              <li>
                <a href="DnD_Builder.php">Create New Character</a>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
      <li><a href="DnDMenu.html"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
    </ul>
    </div>
    </div>
</nav>
<form class="charsheet" id="sheet">
  <header>
    <section class="charname">
      <label for="charname">Character Name</label><input name="charname" value='<?php echo $name; ?>'/>
    </section>
    <section class="misc">
      <ul>
        <li>
          <label for="classlevel">Class & Level</label><input name="classlevel" value='<?php echo $class_info['name'] . " " . $level; ?>' />
        </li>
        <li>
          <label for="background">Background</label><input name="background" placeholder="Acolyte" />
        </li>
        <li>
          <label for="playername">Player Name</label><input name="playername" value='<?php echo $_SESSION['user']; ?>'>
        </li>
        <li>
          <label for="race">Race</label><input name="race" value='<?php echo $race_info['name']; ?>' />
        </li>
        <li>
          <label for="alignment">Alignment</label><input name="alignment" value='<?php echo $align; ?>' />
        </li>
        <li>
          <label for="experiencepoints">Experience Points</label><input name="experiencepoints" placeholder="3240" />
        </li>
      </ul>
    </section>
  </header>
  <main>
    <section>
      <section class="attributes">
        <div class="scores">
          <ul>
            <li class="atr">
              <div class="score">
                <label for="Strengthscore">Strength</label><input name="Strengthscore" value='<?php echo $str; ?>' />
              </div>
              <div class="modifier">
                <input name="Strengthmod" value='<?php echo (floor(($str - 10) /2)); ?>' placeholder="0" />
              </div>
            </li>
            <li class="atr">
              <div class="score">
                <label for="Dexterityscore">Dexterity</label><input name="Dexterityscore" value='<?php echo $dex; ?>' />
              </div>
              <div class="modifier">
                <input name="Dexteritymod" value='<?php echo (floor(($dex- 10) /2)); ?>' />
              </div>
            </li>
            <li class="atr">
              <div class="score">
                <label for="Constitutionscore">Constitution</label><input name="Constitutionscore" value='<?php echo $const; ?>' />
              </div>
              <div class="modifier">
                <input name="Constitutionmod" value='<?php echo (floor(($const - 10) /2)); ?>' />
              </div>
            </li>
            <li class="atr">
              <div class="score">
                <label for="Wisdomscore">Wisdom</label><input name="Wisdomscore" value='<?php echo $wis; ?>' />
              </div>
              <div class="modifier">
                <input name="Wisdommod" value='<?php echo (floor(($wis - 10) /2)); ?>'/>
              </div>
            </li>
            <li class="atr">
              <div class="score">
                <label for="Intelligencescore">Intelligence</label><input name="Intelligencescore" value='<?php echo $intel; ?>' />
              </div>
              <div class="modifier">
                <input name="Intelligencemod" value='<?php echo (floor(($intel - 10) /2)); ?>' />
              </div>
            </li>
            <li class="atr">
              <div class="score">
                <label for="Charismascore">Charisma</label><input name="Charismascore" value='<?php echo $char; ?>' />
              </div>
              <div class="modifier">
                <input name="Charismamod" value='<?php echo (floor(($char - 10) /2)); ?>' />
              </div>
            </li>
          </ul>
        </div>
        <div class="attr-applications">
          <div class="inspiration box">
            <div class="label-container">
              <label for="inspiration">Inspiration</label>
            </div>
            <input name="inspiration" type="checkbox" />
          </div>
          <div class="proficiencybonus box">
            <div class="label-container">
              <label for="proficiencybonus">Prof Bonus</label>
            </div>
            <input name="proficiencybonus" value='2' />
          </div>
          <div class="saves list-section box">
            <ul>
              <li>
                <label for="Strength-save">Strength</label><input name="Strength-save" placeholder="+0" type="text" /><input name="Strength-save-prof" type="checkbox" />
              </li>
              <li>
                <label for="Dexterity-save">Dexterity</label><input name="Dexterity-save" placeholder="+0" type="text" /><input name="Dexterity-save-prof" type="checkbox" />
              </li>
              <li>
                <label for="Constitution-save">Constitution</label><input name="Constitution-save" placeholder="+0" type="text" /><input name="Constitution-save-prof" type="checkbox" />
              </li>
              <li>
                <label for="Wisdom-save">Wisdom</label><input name="Wisdom-save" placeholder="+0" type="text" /><input name="Wisdom-save-prof" type="checkbox" />
              </li>
              <li>
                <label for="Intelligence-save">Intelligence</label><input name="Intelligence-save" placeholder="+0" type="text" /><input name="Intelligence-save-prof" type="checkbox" />
              </li>
              <li>
                <label for="Charisma-save">Charisma</label><input name="Charisma-save" placeholder="+0" type="text" /><input name="Charisma-save-prof" type="checkbox" />
              </li>
            </ul>
            <div class="label">
              Saving Throws
            </div>
          </div>
          <div class="skills list-section box">
            <ul>
                 <?php
               $skills = json_decode($stat_info['skills'], true);
                //echo "console.log('" . $stat_info['skills'] . "');";
  foreach ($skills as $key => $data) {
        switch ($key) {
            case 0 :
                $name = 'Acrobatics';
                $type = 'Dex';
                break;
                
            case 1 :
                $name = 'Animal Handling';
                $type = 'Wis';
                break;
            case 2 :
                $name = 'Arcana';
                $type = 'Int';
                break;
            case 3 :
                $name = 'Athletics';
                $type = 'Str';
                break;
            case 4 :
                $name = 'Deception';
                $type = 'Cha';
                break;
            case 5 :
                $name = 'History';
                $type = 'Int';
                break;
            case 6 :
                $name = 'Insight';
                $type = 'Wis';
                break;
            case 7 :
                $name = 'Intimidation';
                $type = 'Cha';
                break;
            case 8 :
                $name = 'Investigation';
                $type = 'Int';
                break;
            case 9 :
                $name = 'Medicine';
                $type = 'Wis';
                break;
            case 10 :
                $name = 'Nature';
                $type = 'Int';
                break;
            case 11 :
                $name = 'Perception';
                $type = 'Wis';
                break;
            case 12 :
                $name = 'Performance';
                $type = 'Cha';
                break;
            case 13 :
                $name = 'Persuasion';
                $type = '(Cha)';
                break;
            case 14 :
                $name = 'Religion';
                $type = 'Int';
                break;
            case 15 :
                $name = 'Sleight of Hand';
                $type = 'Dex';
                break;
            case 16 :
                $name = 'Stealth';
                $type = 'Dex';
                break;
            case 17 :
                $name = 'Survival';
                $type = 'Wis';
        }
        echo "<li> <label for='$name'>$name <span class-'skill'>\($type\)</span></label><input name='$name' placeholder='+0' type='text' value='$data'/><input name-'$name -prof' type='checkbox'/> </li>";
    }   
?>
            </ul>
            <div class="label">
              Skills
            </div>
          </div>
        </div>
      </section>
      <div class="passive-perception box">
        <div class="label-container">
          <label for="passiveperception">Passive Wisdom (Perception)</label>
        </div>
        <input name="passiveperception" placeholder="10" />
      </div>
      <div class="otherprofs box textblock">
        <label for="otherprofs">Other Proficiencies and Languages</label><textarea name="otherprofs"><?php $feats = json_decode($race_info['features'], true);
              foreach ($feats['languages'] as $key => $value)
                  echo $value . "\n";?></textarea>
      </div>
    </section>
    <section>
      <section class="combat">
        <div class="armorclass">
          <div>
            <label for="ac">Armor Class</label><input name="ac" value='<?php echo $ac; ?>' type="text" />
          </div>
        </div>
        <div class="initiative">
          <div>
            <label for="initiative">Initiative</label><input name="initiative" value='<?php echo $init; ?>' type="text" />
          </div>
        </div>
        <div class="speed">
          <div>
            <label for="speed">Speed</label><input name="speed" value='<?php echo $race_info['speed']; ?>' type="text" />
          </div>
        </div>
        <div class="hp">
          <div class="regular">
            <div class="max">
              <label for="maxhp">Hit Point Maximum</label><input name="maxhp" value='<?php echo $health; ?>' type="text" />
            </div>
            <div class="current">
              <label for="currenthp">Current Hit Points</label><input value='<?php echo $health; ?>' type="text" />
            </div>
          </div>
          <div class="temporary">
            <label for="temphp">Temporary Hit Points</label><input name="temphp" type="text" />
          </div>
        </div>
        <div class="hitdice">
          <div>
            <div class="total">
              <label for="totalhd">Total</label><input name="totalhd" value='<?php echo $class_info['hit_die']; ?>' type="text" />
            </div>
            <div class="remaining">
              <label for="remaininghd">Hit Dice</label><input name="remaininghd" type="text" />
            </div>
          </div>
        </div>
        <div class="deathsaves">
          <div>
            <div class="label">
              <label>Death Saves</label>
            </div>
            <div class="marks">
              <div class="deathsuccesses">
                <label>Successes</label>
                <div class="bubbles">
                  <input name="deathsuccess1" type="checkbox" />
                  <input name="deathsuccess2" type="checkbox" />
                  <input name="deathsuccess3" type="checkbox" />
                </div>
              </div>
              <div class="deathfails">
                <label>Failures</label>
                <div class="bubbles">
                  <input name="deathfail1" type="checkbox" />
                  <input name="deathfail2" type="checkbox" />
                  <input name="deathfail3" type="checkbox" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="attacksandspellcasting">
        <div>
          <label>Attacks & Spellcasting</label>
          <table>
            <thead>
              <tr>
                <th>
                  Name
                </th>
                <th>
                  Range
                </th>
                <th>
                  Casting Time
                </th>
              </tr>
            </thead>
            <tbody><?php
                foreach($info as $key => $value) {
                    $title = ("Level " . $value['level'] . " " . $value['spell_school']);
                    if ($value['cantrip'] == true)
                        $title = ($title . " Cantrip ");
                    if ($value['ritual'] == true)
                        $title = ($title . " \(Ritual\)");
                    $title = ($title . "\nDuration: " . $value['duration'] . "\n" . $value['description']);
                    echo "<tr><td><input name='spell' title='$title' type='text' value='" . $value['name'] . "'/></td>";
                    echo "<td><input name='spell' title='$title' type='text' value='" . $value['range'] . "'/></td><";
                    echo "<td><input name='spell' title='$title' type='text' value='" . $value['time'] . "'/></td><";
                    echo "</tr>";
                }
                ?>
            </tbody>
          </table>
          <textarea></textarea>
        </div>
      </section>
      <section class="equipment">
        <div>
          <label>Equipment</label>
          <div class="money">
            <ul>
              <li>
                <label for="cp">cp</label><input name="cp" />
              </li>
              <li>
                <label for="sp">sp</label><input name="sp" />
              </li>
              <li>
                <label for="ep">ep</label><input name="ep" />
              </li>
              <li>
                <label for="gp">gp</label><input name="gp" />
              </li>
              <li>
                <label for="pp">pp</label><input name="pp" />
              </li>
            </ul>
          </div>
          <textarea placeholder="Equipment list here"></textarea>
        </div>
      </section>
    </section>
    <section>
      <section class="flavor">
        <div class="personality">
          <label for="personality">Personality</label><textarea name="personality"></textarea>
        </div>
        <div class="ideals">
          <label for="ideals">Ideals</label><textarea name="ideals"></textarea>
        </div>
        <div class="bonds">
          <label for="bonds">Bonds</label><textarea name="bonds"></textarea>
        </div>
        <div class="flaws">
          <label for="flaws">Flaws</label><textarea name="flaws"></textarea>
        </div>
      </section>
      <section class="features">
        <div>
          <label for="features">Features & Traits</label><textarea name="features" ><?php 
              $feats = json_decode($class_info['features'], true);
              foreach ($feats['features'] as $key => $value)
                  echo "\t" . $value . "\n";
            $feats = json_decode($race_info['features'], true);
              foreach ($feats['features'] as $key => $value)
                  echo "\t" . $value . "\n";
              ?>
            </textarea>
        </div>
      </section>
    </section>
  </main>
</form>
    <script>
        function start() {
            $("#sheet :input").prop("disabled", true);
        }
    </script>
    </body>
</html>

<!-- Base HTML Dungeons and Dragons form from Brandon Fulljames at https://codepen.io/evertras/full/YVVeMd --!>