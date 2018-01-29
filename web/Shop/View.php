<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Shopping carts</title>
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
   
    /* Add a gray background color and some padding to the footer */
    footer {
      background-color: #f2f2f2;
      padding: 25px;
    }
  </style>
</head>
<body>

<div class="page-header">
  <h1>Shoppng Cart</h1>
</div>


<div class="container">
    <div class="panel-group">
        <?php 
        foreach($_SESSION as $number => $item) {
      echo '<div class="panel panel-primary center-block" style="width:25%">';
        echo '<div class="panel-heading">' . $item . "Paper </div>";
        echo '<div class="panel-body"><img src="' . $item . '.jpg"class="img-thumbnail" alt="Image"></div>';
        echo '<div class="panel-footer"><button class="center-block" onclick="sessionUpdate(' . "'";
        echo $number . "'" . ')">Delete item from cart</button></div>';
      echo '</div>';
}
?>
    </div></div>
    
    <script>
        function sessionUpdate(item) {
            document.getElementById("formName").name = item;
            document.getElementById("formName").value = item;
            document.getElementById("updateSes").submit();
}
    </script>
        
    <form id="updateSes" action="removeItem.php" method="post">
		<input id="formName" type="hidden" value="" name=""><br>
	</form>

    <div class="btn-group btn-group-justified">
  <a href="Checkout.html" class="btn btn-success">Checkout</a>
  <a href="Browse.php" class="btn btn-danger">Return to browsing</a>
</div>
<br/>
</body>
</html>
