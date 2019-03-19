<?php
$name = $_REQUEST["n"];
$path = "ui";
$file = fopen($path, "r");
$content = fread($file, filesize($path));
fclose($file);
$content = explode("\n", $content);
for ($i = 0; $i < count($content)-1; $i++) {
	$content[$i] = explode(',', $content[$i]);
	if ($content[$i][0] == $name) {
		unset($content[$i]);
	}
	$content[$i] = join(',', $content[$i]);
}
$content = join("\n", $content);
$content = substr($content, 0, -1);
$file = fopen($path, "w");
fwrite($file, $content);
fclose($file);
?>
