<?php 
session_start();
$sesArray = $_SESSION["array"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Browse Items</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
    /* Remove the navbar's default rounded borders and increase the bottom margin */ 
    .navbar {
      margin-bottom: 50px;
      border-radius: 0;
    }
    
    /* Remove the jumbotron's default bottom margin */ 
     .jumbotron {
      margin-bottom: 0;
    }
   
  </style>
</head>
<body>

<div class="jumbotron">
  <div class="container text-center">
    <h1>Dunder Mifflin</h1>      
    <p>Paper Products for a Paperless World</p>
  </div>
</div>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="Browse.php">Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="View.php" class="btn btn-default btn-lg">
          <span class="glyphicon glyphicon-shopping-cart"></span> <b>(<?php echo sizeof($_SESSION); ?>) Shopping Cart
            </b></a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">    
  <div class="row">
    <div class="col-sm-4">
      <div class="panel panel-primary">
        <div class="panel-heading">Trident Spectra copier paper</div>
        <div class="panel-body"><img src="Spectra.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">No one can copy the quality of this paper<button style="margin-left: 6%" name="Spectra" onclick="sessionUpdate('Spectra')">Add to cart</button></div>
      </div>
    </div>
    <div class="col-sm-4"> 
      <div class="panel panel-danger">
        <div class="panel-heading">Graphing paper</div>
        <div class="panel-body"><img src="Graph.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">Not for gardening purposes<button style="margin-left: 6%" name="Graph" onclick="sessionUpdate('Graph')">Add to cart</button></div>
      </div>
    </div>
    <div class="col-sm-4"> 
      <div class="panel panel-success">
        <div class="panel-heading">Sketch paper</div>
        <div class="panel-body"><img src="Sketch.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">It's not as sketchy as you'd think<button style="margin-left: 6%" name="Sketch" onclick="sessionUpdate('Sketch')">Add to cart</button></div>
      </div>
    </div>
  </div>
</div><br>

<div class="container">    
  <div class="row">
    <div class="col-sm-4">
      <div class="panel panel-primary">
        <div class="panel-heading">Crayola Construction paper</div>
        <div class="panel-body"><img src="Crayola.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">Let imagination run wild <button style="margin-left: 6%" name="Crayola" onclick="sessionUpdate('Crayola')">Add to cart</button></div>
      </div>
    </div>
    <div class="col-sm-4"> 
      <div class="panel panel-primary">
        <div class="panel-heading">Tru-Ray Construction paper</div>
        <div class="panel-body"><img src="Construction.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">Bob the Builder Approved <button style="margin-left: 6%" name="Construction" onclick="sessionUpdate('Construction')">Add to cart</button></div>
      </div>
    </div>
    <div class="col-sm-4"> 
      <div class="panel panel-primary">
        <div class="panel-heading">Wax paper</div>
        <div class="panel-body"><img src="Wax.jpg" class="img-responsive" style="width:100%" alt="Image"></div>
        <div class="panel-footer">Wax paper for all of your needs! <button style="margin-left: 6%" name="Wax" onclick="sessionUpdate('Wax')">Add to cart</button></div>
      </div>
    </div>
  </div>
</div><br>

    <script>
        function sessionUpdate(item) {
            document.getElementById("formName").name = item;
            document.getElementById("formName").value = item;
            document.getElementById("updateSes").submit();
}
    </script>
    
    
    <form id="updateSes" action="SessionUpdate.php" method="post">
		<input id="formName" type="hidden" value="" name=""><br>
	</form>

</body>
</html>
