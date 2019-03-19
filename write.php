<?php
$user = $_REQUEST["u"];
$amount = $_REQUEST["a"];
$path = "usrs/" . $user;
$streckline = date('Y-m-d') . ", -" . $amount . ",,\n";
$file = fopen($path, "a");
fwrite($file, $streckline);
fclose($file);
?>
