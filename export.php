<?php
$file = fopen("users2", "r");
$userlist = fread($file, filesize("users2"));
fclose($file);
$userlist = explode("\n", $userlist);
$retstring = "";
for ($i = 0; $i < count($userlist); $i++) {
	$name = explode(",", $userlist[$i])[0];
	$path = "usrs/" . $name;
	$file = fopen($path, "r");
	$log = fread($file, filesize($path));
	fclose($file);
	$log = explode("\n", $log);
	$saldo = 0;
	for ($j = 0; $j < count($log); $j++) {
		$amount = explode(",", $log[$j])[1];
		$saldo = $saldo + (int)$amount;
	}
	if ($saldo < 0) {
		$retstring = $retstring . $name . ", " . $saldo . "\n";
	}
}
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="saldon.csv"');
echo $retstring;
exit();
?>
