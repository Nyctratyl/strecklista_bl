<?php
$name = $_REQUEST["n"];
$section = $_REQUEST["s"];
$path = "users2";
$line = $name . ", " . $section . "\n";
$file = fopen($path, "a");
fwrite($file, $line);
fclose($file);
$path = "usrs/" . $name;
$file = fopen($path, "w");
fwrite($file, "");
fclose($file);
chmod($file, 0777);
?>
