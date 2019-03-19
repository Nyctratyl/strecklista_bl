<?php
$name = $_REQUEST["n"];
$amount = $_REQUEST["p"];
$path = "ui";
$streckline = $name . "," . $amount . "\n";
$file = fopen($path, "a");
fwrite($file, $streckline);
fclose($file);
?>
