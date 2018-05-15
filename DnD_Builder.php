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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>DnD Character Builder</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="DnD_Builder.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
      
      label {
          color: white;
      }
      
        nav {
            font-family: 'Press Start 2P', cursive !important;
      }
      .score label{
          display: inline-block;
          width: 100%;
          color: black;
          padding-top: 10%;
          font-size: 80%;
      }
      
      .skillsLabel {
          font-size: 50%;
          color: black;
      }
      
      button {
          background-color: darkgrey;
          border-color: black;
          border-style: solid;
          font-style: italic;
          font-weight: bold;
          text-align: center;
          opacity: 0.8;
          filter: alpha(opacity=80); /* For IE8 and earlier */
    
      }

      button:hover {
          color: rgba(247, 224, 106, .9);
          background-color: rgba(207,142,68,0.4);
      }
    </style>
</head>
<body>
    <nav class="navbar navbar-default  navbar-inverse" role="navigation">
<div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        </button>
        <p class="navbar-brand"><b>Character Builder</b></p>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="Home.php">Menu</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="DnDMenu.html"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
        </ul>
    </div>
</div>
</nav>
<div class="container">
<form class="form-horizontal" action="saveChar.php" method="get" id="myForm">
    <div class="container text-center">
        <div class="form-inline">
            <div class="form-group-lg col-lg-6">
                <label for="name" style="font-size: 200%">Character Name</label>
                <input type="text" class="form-control" id="name" name="char_name" required>
            </div>
            <div class="form-group-lg col-lg-6">
                <label for="align" style="font-size: 200%">Alignment</label>
                <select id="align" class="form-control" name="alignment" required>
        <option value="" disabled selected hidden>Please Choose</option>
        <option name="alignment" value="LG">Lawful Good</option>
        <option name="alignment" value="LN">Lawful Neutral</option>
        <option name="alignment" value="LE">Lawful Evil</option>
        <option name="alignment" value="NG">Neutral Good</option>
        <option name="alignment" value="NN">Neutral Neutral</option>
        <option name="alignment" value="NE">Neutral Evil</option>
        <option name="alignment" value="CG">Chaotic Good</option>
        <option name="alignment" value="CN">Chaotic Neutral</option>
        <option name="alignment" value="CE">Chaotic Evil</option>
      </select>
            </div>
        </div>
    </div>
    <br/>
    
<div class="container scores">
    <ul>
        <li>
            <div class="score">
                <label for="Strengthscore">Strength</label><input class="statName" name="Strength" placeholder="0" id="strength" type="number" required/>
            </div>
              <div class="modifier">
                <input placeholder="+0" id="strengthmod" disabled/>
              </div>
                <button type="button" class="btn" onclick="statGenerate('strength')" style="border-radius: 20%; margin: 5px">Roll</button>
        </li>
        <li>
            <div class="score">
                <label for="Dexterityscore">Dexterity</label><input class="statName" name="Dexterity" placeholder="0" id="dexterity" onchange="mod(this.name)" required />
            </div>
            <div class="modifier">
                <input id="dexteritymod" placeholder="+0" disabled/>
            </div>
                <button type="button" class="btn" onclick="statGenerate('dexterity')" style="border-radius: 20%; margin: 5px">Roll </button>
        </li>
        <li>
            <div class="score">
                <label for="Constitutionscore">Constitution</label><input class="statName" name="Constitution" placeholder="0" id="constitution" type="number" onchange="mod(this.name)" required />
            </div>
            <div class="modifier">
                <input id="constitutionmod" placeholder="+0" disabled/>
            </div>
                <button type="button" class="btn" onclick="statGenerate('constitution')" style="border-radius: 20%; margin: 5px">Roll </button>
        </li>
        <li>
            <div class="score">
                <label for="Wisdomscore">Wisdom</label><input class="statName" name="Wisdom" placeholder="0" id="wisdom" type="number" onchange="mod(this.name)" required />
            </div>
            <div class="modifier">
                <input id="wisdommod" placeholder="+0" disabled/>
            </div>
                <button type="button" class="btn" value="increase stats" onclick="statGenerate('wisdom')" style="border-radius: 20%; margin: 5px">Roll</button>
        </li>
        <li>
            <div class="score">
                <label for="Intelligencescore">Intelligence</label><input class="statName" name="Intelligence" placeholder="0" id="intelligence" type="number" onchange="mod(this.name)" required />
            </div>
            <div class="modifier">
                <input id="intelligencemod" placeholder="+0" disabled/>
            </div>
                <button type="button" class="btn" value="increase stats" onclick="statGenerate('intelligence')" style="border-radius: 20%; margin: 5px">Roll</button>
        </li>
        <li>
            <div class="score">
                <label for="Charismascore">Charisma</label><input class="statName" name="Charisma" placeholder="0" id="charisma" type="number" onchange="mod(this.name)" required />
            </div>
            <div class="modifier">
                <input id="charismamod" placeholder="+0" disabled/>
            </div>
                <button type="button" class="btn" value="increase stats" onclick="statGenerate('charisma')" style="border-radius: 20%; margin: 5px">Roll</button>
        </li>
    </ul>
    <button type="button" value="randAll" name="allRand" class="btn btn-primary btn-lg center-block" onclick="statGenerate('strength'); statGenerate('dexterity'); statGenerate('constitution'); statGenerate('wisdom'); statGenerate('intelligence'); statGenerate('charisma') ">Roll All Stats</button>
</div>
<br>

<div class="form-group">
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                        <h4 class="modal-title">Choose an language to know</h4>
                </div>
        <div class="modal-body" id="languageChoice">
                <input type='radio' name='language' value='' selected disabled hidden="hidden">
                <input type='radio' name='language' value='Dwarvish'>Dwarvish<br>
                <input type="radio" name="language" value="Elvish">Elvish<br>
                <input type="radio" name="language" value="Giant">Giant<br>
                <input type='radio' name='language' value='Gnomish'>Gnomish<br>
                <input type="radio" name="language" value="Goblin">Goblin<br>
                <input type="radio" name="language" value="Halfling">Halfling<br>
                <input type='radio' name='language' value='Orc'>Orc<br>
                             
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" onclick="addLanguage()">Add Language</button>
        </div>
      </div>
    </div>
  </div>
</div>
        
        
<div class="container">
    <ul class="nav nav-tabs nav-justified">
        <li class="active"><a data-toggle="tab" href="#race">1.Race</a></li>
        <li><a data-toggle="tab" href="#class">2.Class</a></li>
        <li><a data-toggle="tab" href="#home">3.Character Info</a></li>
        <li><a data-toggle="tab" href="#stat">4.Stats</a></li>
    </ul>
        
    <div class="tab-content">
        <div id="home" class="tab-pane fade ">
                    <br>
                    <div class="form-group">
                        <div class="armorclass"><div>
                            <label for="ac" class="control-label col-sm-2">Armor Class</label><input name="ac" id="armor" placeholder="10" type="number" class="col-sm-10" disabled/>
                    </div></div></div>
          
                    <div class="form-group">
                        <div class="initiative"><div>
                            <label for="initiative" class="control-label col-sm-2">Initiative</label><input name="initiative" id="init" placeholder="+0" type="number" class="col-sm-10" disabled/>
                    </div></div></div>
              
            <div class="form-group">
                <div class="speed"><div>
                    <label for="speed" class="control-label col-sm-2">Speed</label><input name="speed" placeholder="0" type="number" class="col-sm-10" id="speedStat" disabled/>
            </div></div></div>
            <div class="form-group">
                <div class="hp">
                    <label for="maxhp" class="control-label col-sm-2">Hit Point Die</label><input name="maxhp" placeholder="10" type="number" class="col-sm-10" id="hpStat" disabled/>
            </div></div>
            <div class="form-group">
                <label for="extra">Languages and Proficiencies</label>
                <textarea class="form-control" rows="9" id="extra" name="lang" disabled></textarea>
            </div></div>
                 
<div id="race" class="tab-pane fade in active">
    <br>
    <div class="form-group">
        <label class="control-label col-sm-2" for="races">Select Race</label>
        <div class="col-sm-10">
            <select class="form-control" id="races" name="race" onchange="retrieve(this.name, this.value)" required>
            <option value="" disabled selected hidden>Please Choose</option>
            <?php
                foreach ($db->query("SELECT * FROM race") as $row)
                {
	               $race = $row['name'];
	               $id = $row['id'];
	               if (isset($row))
	               {
		              echo "<option  value='$id' name='$id'>$race</option>";
	               }
                }
            ?>
            </select>
        </div></div>
        <div class="form-group">
            <label for="race_feats">Race Feautures</label>
            <textarea class="form-control" rows="8" id="race_feats" disabled></textarea>
</div></div>
            
<div id="class" class="tab-pane fade">
    <br>
    <div class="form-group">
        <label class="control-label col-sm-2" for="classes">Select Class</label>
        <div class="col-sm-10">
            <select class="form-control" id="classes" name="class" onchange="retrieve(this.name, this.value)" required>
            <option value="" disabled selected hidden>Please Choose</option>
            <?php
                foreach ($db->query("SELECT * FROM class") as $row)
                {
	               $class = $row['name'];
	               $id = $row['id'];
	               if (isset($row))
	               {
		              echo "<option  value='$id' name='$id'>$class</option>";
	               }
                }
            ?>
            </select>                  
    </div></div>
        
    <div class="form-group">
        <label class="control-label col-sm-1" for="spellList" id="spellLabel">Select Spells</label>
        <div class="col-sm-5">
            <select class="form-control" id="spells" name="spellList[]" onchange="verifyAmount('spells')" multiple>
            </select>
        </div>
        <label class="control-label col-sm-1" for="cantripList" id="cantripLabel">Select Cantrips</label>
        <div class="col-sm-5">
            <select class="form-control" id="cantrips" name="cantripList[]" onchange="verifyAmount('cantrips')" multiple>
            </select>
    </div></div>
        
    <div class="form-group">
        <label for="class_feats">Class Features</label>
        <textarea class="form-control" rows="8" id="class_feats" disabled></textarea>
    </div>
</div>
            
<div id="stat" class="tab-pane fade">
    <br>
    <div class="form-inline">
        <div class="form-group">
            <ul>
              <li class="list-group-item col-xs-6">
                <label for="Acrobatics" class="control-label col-sm-4 skillsLabel">Acrobatics <span class="skill">(Dex)</span></label><input name="skills[]" id="Acrobatics" placeholder="0" type="number" class="form-control dex" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Animal Handling" class="control-label col-sm-4 skillsLabel">Animal Handling <span class="skill">(Wis)</span></label><input name="skills[]" id="Animal Handling" placeholder="0" type="number" class="form-control wis" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Arcana" class="control-label col-sm-4 skillsLabel">Arcana <span class="skill">(Int)</span></label><input name="skills[]" id="Arcana" placeholder="0"type="number" class="form-control int" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Athletics" class="control-label col-sm-4 skillsLabel">Athletics <span class="skill">(Str)</span></label><input name="skills[]" id="Athletics" placeholder="0" type="number" class="form-control str" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Deception" class="control-label col-sm-4 skillsLabel">Deception <span class="skill">(Cha)</span></label><input name="skills[]" id="Deception" placeholder="0" type="number" class="form-control cha" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="History" class="control-label col-sm-4 skillsLabel">History <span class="skill">(Int)</span></label><input name="skills[]" id="History" placeholder="0" type="number" class="form-control int" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Insight" class="control-label col-sm-4 skillsLabel">Insight <span class="skill">(Wis)</span></label><input name="skills[]" id="Insight" placeholder="0" type="number" class="form-control wis" disabled/>
                </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Intimidation" class="control-label col-sm-4 skillsLabel">Intimidation <span class="skill">(Cha)</span></label><input name="skills[]" id="Intimidation" placeholder="0" type="number" class="form-control cha" disabled/>
                </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Investigation" class="control-label col-sm-4 skillsLabel">Investigation <span class="skill">(Int)</span></label><input name="skills[]" id="Investigation" placeholder="0" type="number" class="form-control int" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Medicine" class="control-label col-sm-4 skillsLabel">Medicine <span class="skill">(Wis)</span></label><input name="skills[]" id="Medicine" placeholder="0" type="number"  class="form-control wis" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Nature" class="control-label col-sm-4 skillsLabel">Nature <span class="skill">(Int)</span></label><input name="skills[]" id="Nature" placeholder="0" type="number" class="form-control int" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Perception" class="control-label col-sm-4 skillsLabel">Perception <span class="skill">(Wis)</span></label><input name="skills[]" id="Perception" placeholder="0" type="number" class="form-control wis" disabled/>
                </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Performance" class="control-label col-sm-4 skillsLabel">Performance <span class="skill">(Cha)</span></label><input name="skills[]" id="Performance" placeholder="0" type="number" class="form-control cha" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Persuasion" class="control-label col-sm-4 skillsLabel">Persuasion <span class="skill">(Cha)</span></label><input name="skills[]" id="Persuasion" placeholder="0" type="number" class="form-control cha" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Religion" class="control-label col-sm-4 skillsLabel">Religion <span class="skill">(Int)</span></label><input name="skills[]" id="Religion" placeholder="0" type="number" class="form-control int" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Sleight of Hand" class="control-label col-sm-4 skillsLabel">Sleight of Hand <span class="skill">(Dex)</span></label><input name="skills[]" id="Sleight of Hand" placeholder="0" type="number" class="form-control dex" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Stealth" class="control-label col-sm-4 skillsLabel">Stealth <span class="skill">(Dex)</span></label><input name="skills[]" id="Stealth" placeholder="0" type="number" class="form-control dex" disabled/>
              </li>
                
              <li class="list-group-item col-xs-6">
                <label for="Survival" class="control-label col-sm-4 skillsLabel">Survival <span class="skill">(Wis)</span></label><input name="skills[]" id="Survival" placeholder="0" type="number" class="form-control wis" disabled/>
              </li>
            </ul>
        </div>
    </div>
</div>
    
</div></div>
        <input  type="button" class="btn-success btn-lg center-block" value="Make Character" onclick="formEnable()">
        <input type="submit" hidden="hidden" id="submitButton">
</form>
</div>
        
<script>
    var stats = ['0','0','0','0','0','0'];
    var base_stats = ['0','0','0','0','0','0'];
    var class_health = 0;
    var race_health = 0;
    var cantripAmount = 0;
    var spellAmount = 0;
    var cantrips = ['0'];
    var spells = ['0'];
    
     /************************
      * Function that disables all user input boxes on page load. 
      * Makes sure the user adheres to D&D rules and guidelines for character creating
      ***********************/
    $(document).ready(function() {
  $('#switcher').click(function() {
    $('input, select, textarea').each(function() {
        if ($(this).attr('disabled')) {
            $(this).removeAttr('disabled');
        }
    });
  });
});
    
    /************************
      * Function that computes the selected stat for a chacter.
      * Ensures values that follow the guidelines for rolling three 6 sided dice
      ***********************/
        function statGenerate(name) {
            var base_random = Math.floor((Math.random() * 16) + 3);
            switch (name) {
                case 'strength':
                    base_stats[0] = base_random;
                    break;
                case 'dexterity':
                    base_stats[1] = base_random;
                    break;
                case 'constitution':
                    base_stats[2] = base_random;
                    break;
                case 'wisdom':
                    base_stats[3] = base_random;
                    break;
                case 'intelligence':
                    base_stats[4] = base_random;
                    break;
                case 'charisma':
                    base_stats[5] = base_random;
            } 
            addStat(name);
            
        }
    
    /************************
      * Function that adds any extra stat bonuses from racial or class perks to the base stats.
      ***********************/
    function addStat(name) {
        //More efficenet but causes lots of bugs and freezes page
        /*var statSpot = document.getElementsByClassName('statName');
        for(i =0; i < statSpot.length; i++) {
            statSpot[i].value = Number(stats[i] + base_stats[i]);         
            mod(statSpot[i].id);
        } */
        document.getElementById('strength').value = Number(stats[0] + base_stats[0]);
        mod('strength');
        document.getElementById('dexterity').value = Number(stats[1] + base_stats[1]);
        mod('dexterity');
        document.getElementById('constitution').value = Number(stats[2] + base_stats[2]);
        mod('constitution');
        document.getElementById('wisdom').value = Number(stats[3] + base_stats[3]);
        mod('wisdom');
        document.getElementById('intelligence').value = Number(stats[4] + base_stats[4]);
        mod('intelligence');
        document.getElementById('charisma').value = Number(stats[5] + base_stats[5]);  
        mod('charisma');
    }
    
    
    function mod(type) {
        var score = document.getElementById(type).value;
        var modName = (type + 'mod');
        var modScore = Math.floor(((score - 10) /2));
        document.getElementById(modName).value = modScore;
        if (type == 'dexterity') {
            document.getElementById('init').value = modScore;
            document.getElementById('armor').value = modScore + 10;
        }
        skills(type, modScore);
    }
    
    /************************
      * Function that makes an AJAX call to an external database to lookup
      * information concerning classes, races, and spells. 
      * On a successful retrieveal, the page will update with new information
      ***********************/
    function retrieve(type, id) {
        var parse = ("table=" + type + "&id=" + id);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var info = JSON.parse(this.responseText);
                
                switch (info.type) {
                    case 'race':
                        document.getElementById('extra').value = "Languages: \n \t";
                        var langs = document.getElementsByName('language');
                        
                        for (i = 0; i < info.features['languages'].length; i++) {
                            if (null != info.features['languages'][i] && info.features['languages'][i] != 'choice') {
                                document.getElementById('extra').value += info.features['languages'][i] + " \n \t";
                                for(c =0; c < langs.length; c++) {
                                    langs[0].checked = true;
                                    if (langs[c].value == info.features['languages'][i])
                                        langs[c].disabled = true;
                                    else
                                       langs[c].disabled = false; 
                                } 
                            }
                         else if (info.features['languages'][i] == 'choice') {
                             $("#myModal").modal();
                        }}
                        
                        
                        document.getElementById((type + '_feats')).value = "Size: " + info.race_size + "\n";
                        document.getElementById((type + '_feats')).value = "Features: \n \t";
                        document.getElementById('speedStat').value = info.speed;
                        stats[0] = 0;
                        stats[1] = 0;
                        stats[2] = 0;
                        stats[3] = 0;
                        stats[4] = 0;
                        stats[5] = 0;
                        race_health = 0;
                        break;
                    case 'class':
                        document.getElementById((type + '_feats')).value = "Features: \n \t";
                        class_health = info.hit_die;
                        document.getElementById('hpStat').value = class_health + race_health;
                        spellAmount = 0;
                        cantripAmount = 0;
                        cantrips.length = 0;
                        spells.length = 0;
                        document.getElementById("cantrips").innerHTML = "";
                        document.getElementById("spells").innerHTML = "";
                        document.getElementById('cantripLabel').innerHTML = "Select " + cantripAmount + " cantrips:";
                        document.getElementById('spellLabel').innerHTML = "Select " + spellAmount + " spells:";
                        break;
                    case 'spells':
                        for (var key in info){
                            if (info.hasOwnProperty(key))
                            var spellValue = info[key];
                            var option = document.createElement("option");
                            option.value = spellValue['id'];
                            option.setAttribute("name", spellValue['name']);
                            //option.name = spellValue['id'];
                            option.text = spellValue['name'];
                            
                            
                            var descriptionFormat = "Level " + spellValue['level'] + " " + spellValue['spell_school'];
                            if(spellValue['ritual'] == true)
                                descriptionFormat += " \(Ritual\)";
                            else if (spellValue['cantrip'] == true)
                                descriptionFormat += " Cantrip";
                            descriptionFormat += "\nCasting Time: " + spellValue['time'];
                            descriptionFormat += "\nRange: " + spellValue['range'];
                            descriptionFormat += "\nDuration: " + spellValue['duration'];
                            descriptionFormat += "\n\t" + spellValue['description'];
                            option.setAttribute("title", descriptionFormat);
                            
                            if(spellValue['cantrip'] == true) {       //ADD ARRAY
                                document.getElementById('cantrips').appendChild(option);
                                document.getElementById('cantripLabel').innerHTML = "Select " + cantripAmount + " cantrips:";
                            }
                            else if (spellValue['cantrip'] == false) {
                                document.getElementById('spells').appendChild(option);
                                document.getElementById('spellLabel').innerHTML = "Select " + spellAmount + " spells:";
                            }
                        }
                }
                
                if (info.type != 'spells') {
                for (var key in info.features){
                    if (info.features.hasOwnProperty(key)) {
                        if (key != 'languages') {
                        var infoValue = info.features[key];
                            if(null != info.features[key])
                                switch (key) {
                                    case 'str':
                                        stats[0] = Number(infoValue);
                                        addStat('strength');
                                        break;
                                    case 'dex':
                                        stats[1] = Number(infoValue);
                                        addStat('dexterity');
                                        break;
                                    case 'cont':
                                        stats[2] = Number(infoValue);
                                        addStat('constitution');
                                        break;
                                    case 'wis':
                                        stats[3] = Number(infoValue);
                                        addStat('wisdom');
                                        break;
                                    case 'intel':
                                        stats[4] = Number(infoValue);
                                        addStat('intelligence');
                                        break;
                                    case 'charis':
                                        stats[5] = Number(infoValue);
                                        addStat('charisma');
                                        break;
                                    case 'skills':
                                        /*for (i = 0; i < infoValue.length; i++) {
      Extra feature to be implemented        document.getElementById((type + '_feats')).value += infoValue[i] + "\t";
                                        }*/
                                        break;
                                    case 'proficiency':
                                        //Extra feature to be implemented
                                        break;
                                    case 'saving':
                                        //Extra feature to be implemented
                                        break;
                                    case 'cantrip':
                                        cantripAmount = Number(infoValue[0]);
                                        for (i = 1; i < infoValue.length; i++) {
                                            cantrips[i] = infoValue[i];
                                        }
                                        retrieve('spells', JSON.stringify(cantrips));
                                        break;
                                    case 'spells':
                                        spellAmount = Number(infoValue[0]);
                                        for (i = 1; i < infoValue.length; i++) {
                                            spells[i] = infoValue[i];
                                        }
                                        retrieve('spells', JSON.stringify(spells));
                                        break;
                                    case 'armor':
                                        //Extra feature to be implemented
                                        break;
                                    case 'weapons':
                                        //Extra feature to be implemented
                                        break;
                                    case 'primary':
                                        //Extra feature to be implemented
                                        break;
                                    case 'ability':
                                        //Extra feature to be implemented
                                        break;
                                    case 'features':
                                        for (i = 0; i < info.features['features'].length; i++) {
                                            if (null != info.features['features'][i])
                                                document.getElementById((type + '_feats')).value += info.features['features'][i] + "\n \t";
                                        }
                                        break;
                                    case 'health':
                                        race_health = Number(infoValue);
                                        document.getElementById('hpStat').value = class_health + race_health;
                                        break;
                                    default:
                                        document.getElementById((type + '_feats')).value += infoValue;
                                }}
                }}}
                
                }
            };
         xmlhttp.open("POST", "lookUp.php", true);
         xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
         xmlhttp.send(parse);
    }
    
    /************************
      * Function that updates the users skills based on the base stats
      ***********************/
    function skills(type, score) {
        var skillAbriv = (type[0] + type[1] + type[2]);
        
        var skills = document.getElementsByClassName(skillAbriv);
        for(i =0; i < skills.length; i++) {
            skills[i].value = score;
        } 
    }
  
    /************************
      * Function that verifies the amount of spells or cantrips chosen does not exceed the limit
      ***********************/
    function verifyAmount(type)
        {
            var selectChoose = document.getElementById(type);
            if (type == 'cantrips')
                maxOptions = cantripAmount;
            else if (type == 'spells')
                maxOptions = spellAmount;
            
            var optionCount = 0;
            for (var i = 0; i < selectChoose.length; i++) {
                if (selectChoose[i].selected) {
                    optionCount++;
                    if (optionCount > maxOptions) {
                        alert(("Only select " + maxOptions + " from " + type))
                        selectChoose.selectedIndex = null;
                        return false;
                    }
                }
            }
            return true;
        }
    
    /************************
      * Function that shows the optional avaiable languages to learn for the race
      ***********************/
    function addLanguage() {
        var langs = document.getElementsByName('language');
        for (i =0; i <langs.length; i++) {
            if(langs[i].checked == true)
                document.getElementById('extra').value += langs[i].value + " \n \t";
        }
    }
    
    /************************
      * Function that enables all user input boxes before page submit. 
      * This is required to process the form on the server side.
      ***********************/
    function formEnable() {
    var yourForm = document.getElementById('myForm');
    if(yourForm.checkValidity()) {
        $(document).ready(function() {
        $('input, select').each(function() {
        if ($(this).attr('disabled')) {
            $(this).removeAttr('disabled');
        }
    });
            document.getElementById('submitButton').click();
  });
    } else
        document.getElementById('submitButton').click()
}
    
</script>
    
    
</body>
</html>