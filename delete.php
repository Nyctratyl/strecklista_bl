<?php
$user = $_REQUEST["u"];
$path = "usrs/" . $user;
$file = fopen($path, "r");
$content = fread($file, filesize($path));
fclose($file);
$content = explode("\n", $content);
array_pop($content);
array_pop($content);
$content = join("\n", $content);
$content = $content . "\n";
$file = fopen($path, "w");
fwrite($file, $content);
fclose($file);
?>
