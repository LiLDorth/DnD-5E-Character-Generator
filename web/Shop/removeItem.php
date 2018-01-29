<?php
session_start();
foreach ($_POST as $item) {
unset($_SESSION[$item]);
}
header('Location: View.php');
exit;
?>