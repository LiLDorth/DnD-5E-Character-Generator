<?php
   session_start();
		foreach ($_POST as $item) {
   $_SESSION[("Item" . sizeof($_SESSION))] = $item;
        }
header('Location: Browse.php');
exit;
?>