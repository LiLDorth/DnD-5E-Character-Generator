<?php
session_start();
$name = htmlspecialchars($_POST["name"]);
$address = htmlspecialchars($_POST["address"]);
$city = htmlspecialchars($_POST["city"]);
$state = htmlspecialchars($_POST["state"]);
$zip = htmlspecialchars($_POST["zip"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Confirmation</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
 <div class="page-header">
  <h1>Shipment Confirmed</h1>
</div>
    
    <div class="container">
        <div class="well">
            <?php 
            echo 'Items: ';
            foreach($_SESSION as $item)
                echo $item . ', ';
            echo '</br> Shipped to ' . $name . ' at ' . $address . ' ' . $city . ', ' . $state . ', ' . $zip;
            ?>
        </div>
    </div>

</body>
</html>