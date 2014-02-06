<?php
ini_set('max_execution_time', 10800);
error_reporting(E_ALL);
ini_set('display_errors', 1);
$col = array("pos_title","pos_year","pos_price","pos_upc","pos_type","pos_cat","pos_auth","pos_link");
$input = array();
$ret = array(0,0,0,0);
$row = 0;
include_once '../include/setup.php';
include_once '../include/csvfunctions.php';
$recyclers = mysql_query("SELECT * FROM `db_supplier`;");
$e = "";
$time = round((time() / 3600),0);
$start_time = explode(" ",microtime());
$start_time = $start_time[0] + $start_time[1];
while ($r = mysql_fetch_assoc($recyclers)) {
	$e .= "********************************************************\n";
	$e .= "* LOG FOR {$r['name']}\n";
	$e .= "********************************************************\n*\n";
	$settings = mysql_query("SELECT * FROM `db_schema` WHERE `rec_id` = '{$r['id']}'");
	$s = mysql_fetch_assoc($settings);
	if ($handle = @fopen($r['url'],"r")) {
		while ($data = @fgetcsv($handle,0,",",$s['enclosing'])) {
			$row++;
			for ($c = 0 ; $c < 8 ; $c++) { // Clean Input
				if (empty($s[$col[$c]])) {
					$input[$c] = 'NULL';
				} else {
					$input[$c] = $data[$s[$col[$c]]-1];
					$input[$c] = preg_replace('/[^(\x20-\x7F)]*/','',$input[$c]);
					$input[$c] = mysql_real_escape_string(trim($input[$c]));
				}
			}
			if ($s['pos_cat'] == 0) { // Determine Category
				if (($s['con_dvds']=="1"&&$s['con_books']=="0"&&$s['con_games']=="0")||
				($s['con_dvds']=="0"&&$s['con_books']=="1"&&$s['con_games']=="0")||
				($s['con_dvds']=="0"&&$s['con_books']=="0"&&$s['con_games']=="1")) {
					echo "only one cat chosen<br>";
					if ($s['con_dvds']=="1") {
						$input[5] = "1";
					} else if ($s['con_books']=="1") {
						$input[5] = "2";
					} else if ($s['con_games']=="1") {
						$input[5] = "3";
					}
				}
			} else if (!empty($s["pos_cat"])) {
				if (($s["def_dvd"]) == $input[5]) {
					$input[5] = "1";
				} else if (($s["def_book"]) == $input[5]) {
					$input[5] = "2";
				} else if (($s["def_game"]) == $input[5]) {
					$input[5] = "3";
				} else {
					$e .= "Error Defining Category.\n";
				}
			}
			if (!empty($s['ovr_year'])) { // Overrides
				$input[1] = $s['ovr_year'];
			}
			if (!empty($s['ovr_year'])) {
				$input[4] = $s['ovr_type'];
			}
			if ($input[3] !== 'NULL') { // Import to DB
				$result = set_offer($input,$r['id']);
			} else {
				$input[3] = get_barcode($r,$input);
				$result = set_offer($input,$r['id']);
			}
			$ret[0] += $result[0];
			$ret[1] += $result[1];
			$ret[2] += $result[2];
			$ret[3] = $result[3];
		}
		if ($row > 0) {
			$e .= "* OFFERS INSERTED: {$ret[0]} | OFFERS UPDATED: {$ret[1]} | NEW PRODUCTS: {$ret[2]}\n";
			foreach ($ret[3] as $err) {
				$e .= "* MySQl Error: \" {$err} \"\n";
			}
			$e.= "* \n";
		} else {
			$e .= "* ERROR: This CSV Contains No Data.\n*\n";
		}
	} else {
		$e .= "* ERROR: CSV File Not Found.\n*\n";
	}
}
$end_time = explode(" ",microtime());
$end_time = $end_time[0] + $end_time[1];
$end_time = $end_time - $start_time;
$e .= "********************************************************\n* Script Executed In {$end_time} microseconds\n********************************************************\n";
file_put_contents("logs/{$time}.log",$e);
?>
