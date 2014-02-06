<?php
require_once '../include/setup.php';
$mtime = explode(" ",microtime()); 
$starttime = $mtime[1] + $mtime[0]; 
function not_diff($id1,$id2) {
	$file = file_get_contents("not_diff.txt");
	$arr = explode("|",$file);
	$c = 0;
	array_pop($arr);
	foreach ($arr as &$a) {
		$c++;
		if ($c > (count($a) - 2))
		$a = explode(",",$a);
		if (($id1 == $a[0] && $id2 == $a[1]) || ($id1 == $a[1] && $id2 == $a[0])) {
			return false;
		}
	}
	return true;
}
echo "<link type='text/css' rel='stylesheet' href='console.css'/>
<script type='text/javascript' src='console.js'></script>";
$r = mysql_query("SELECT * FROM `db_list`;");
$c = $m = 0;
$c1_arr = $c2_arr = $found = array();
while($i = mysql_fetch_assoc($r)) {
	$c1_arr[] = $i;$c2_arr[] = $i;
}
foreach($c1_arr as $c1) {
	foreach($c2_arr as $c2) {
		$c++;
		if ($c1['id'] !== $c2['id']) {
			if (not_diff($c1['id'],$c2['id'])) {
				if (!in_array("{$c1['id']},{$c2['id']}",$found) && !in_array("{$c2['id']},{$c1['id']}",$found)) {
					$found[] = "{$c1['id']},{$c2['id']}";
					similar_text(strtolower($c1['name']),strtolower($c2['name']),$p);
					if ($p > 65) {
						echo "<p style='margin-bottom:30px;'>1) \"{$c1['name']} [{$c1['year']}][{$c1['platform']}]\"<br>2) \"{$c2['name']} [{$c2['year']}][{$c2['platform']}]\"<br>Similarity: ".round($p,0)."% Duplicate: <a onclick='duplicate({$c1['id']},{$c2['id']},this.parentNode);' style='cursor:pointer;'><u>Y</u></a> / <a style='cursor:pointer;' onclick='diff({$c1['id']},{$c2['id']},this.parentNode);'><u>N</u></a></p>";
						$m++;
					}
				}
			}
		}
	}
}
echo "<p>Comparisons: ({$c}). Possible Matches: ({$m}).</p>";
$mtime = explode(" ",microtime()); 
$endtime = $mtime[1] + $mtime[0];
$time = round(($endtime - $starttime),6); 
echo "<p>Script Executed in {$time} Seconds</p>";
?>
