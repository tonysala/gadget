<?php
//error_reporting(E_ALL);
//ini_set('display_errors', -1);
include_once "../include/cmsfunctions.php";
function sorted($arr) {
	$k_arr = $v_arr = array();
	foreach ($arr as $a) {
		if (in_array($a, $k_arr)) {
			$v_arr[array_search($a, $k_arr)]++;
		} else {
			$v_arr[] = 1;
			$k_arr[] = $a;
		}
	}
	$f_arr = array_combine($k_arr,$v_arr);
	arsort($f_arr);
	return($f_arr);
}
echo "<link type='text/css' rel='stylesheet' href='console.css'/>";
require_once '../include/setup.php';
$count = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_list` WHERE `barcode` IS NULL;"),0);
$maxlen = strlen($count);
$c = $ins = $nf = $ce = 0;
$q = mysql_query("SELECT `name`,`id`,`platform` FROM `db_list` WHERE `barcode` IS NULL;") or die(mysql_error());
echo "<p><u>Barcodes Needed: ({$count})</u></p><br>";
while($r = mysql_fetch_assoc($q)) {
	$padc = str_pad($c,$maxlen,"0",STR_PAD_LEFT);
	echo "<p><u>Searching for {$r['name']} [{$r['platform']}]</u></p><br>";
	echo "<p>Using URL http://www.google.com/search?q=".urlencode($r['name'])."+".urlencode($r['platform'])."+UPC</p>";
	$c++;
	if($page = file_get_contents("http://www.google.com/search?q=".urlencode($r['name'])."+".urlencode($r['platform'])."+UPC")) {
		//echo htmlentities($page);
		$upc = array();
		preg_match_all("/\d{13}/",$page,$upc);
		$upc = $upc[0];
		foreach($upc as $upc_code) {
			if (validate_UPC($upc_code)) {
				$upc_arr[] = $upc_code;
			}
		}
		$barcodes = sorted($upc_arr);
		if (count($barcodes) > 0) {
			mysql_query("UPDATE `db_list` SET `barcode` = '".array_keys($barcodes)[0]."' WHERE `id` = '{$r['id']}'") or die(mysql_error());
			echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>[{$padc}] &#10003; Found & Inserted UPC [".array_keys($barcodes)[0]."] for [{$r['name']}] [{$r['platform']}] [".array_values($barcodes)[0]." Occurances]</p><br>";
			$ins++;
		} else {
			echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>[{$padc}] &#10007; No Found UPC for [{$r['name']}] [{$r['platform']}]</p><br>";
			$nf++;
		}
	} else {
		echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Cound Not Connect to Google Servers.</p>";
		$ce++;
	}
	sleep(3);
	/* EAN SEARCH (BOOKS)
	if($page = file_get_contents("http://www.google.com/search?q=".urlencode($r['name'])."+".urlencode($r['platform'])."+UPC")) {
		echo "<p>Using URL http://www.google.com/search?q=".urlencode($r['name'])."+".urlencode($r['platform'])."+EAN</p>";
		//echo htmlentities($page);
		$ean = array();
		preg_match_all("/\d{13}/",$page,$ean);
		$ean = $ean[0];
		//var_dump($ean);
		foreach($ean as $ean_code)  {
			echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>[{$padc}] &#10003; Found UPC for [{$r['name']}] [{$r['platform']}] [{$ean_code}]</p>";
		}
	} else {
		echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Cound Not Connect to Google Servers.</p>";
	}
	*/
	ob_end_flush();
	@ob_flush();
	flush();
	ob_start();
	unset($upc_arr,$page,$upc);
}
echo "<p style='text-decoration:underline;margin:2px;'><u>Script Executed</u></p>";
echo "<p style='text-decoration:underline;margin:2px;'><u>Inserted: ({$ins}/{$c}) | Not Found: ({$nf}/{$c}) | Connection Error: ({$ce}/{$c})</u></p>";
?>
