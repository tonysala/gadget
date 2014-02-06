<?php
function set_offer($i,$r) {
	$ins = $upd = $nwp = 0;
	$errors = array();
	if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_list` WHERE `barcode` = '{$i[3]}';"),0) == 0) {
		if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_alts` WHERE `barcode` = '{$i[3]}';"),0) > 0) {
			$id = mysql_result(mysql_query("SELECT `pid` FROM `db_alts` WHERE `barcode` = '{$i[3]}';"),0);
			if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_offers` WHERE `prod_id` = '{$id}' AND `supplier_id` = '{$r}';"),0) > 0) {
				$i[2] = ($i[2] !== 'NULL'?"'{$i[2]}'":$i[2]);
				$i[7] = ($i[7] !== 'NULL'?"'{$i[7]}'":$i[7]);
				mysql_query("UPDATE `db_offers` SET `price` = {$i[2]},`url` = {$i[7]} WHERE `prod_id` = '{$id}' AND `supplier_id` = '{$r}';") or die(mysql_error());
				if (mysql_affected_rows() > 0) {
					$upd++;
				} else {
					$errors[] = mysql_error();
				}
			} else {
				$offers_values = array($id,$r['id'],$i[2],$i[7],$i[5]);
				foreach ($offers_values_values as &$offer_value) {
					if ($offer_value !== 'NULL') {
						$offer_value = "'{$offer_value}'";
					}
				}
				$offers_values = implode(",",$offers_values);
				mysql_query("INSERT INTO `db_offers` (`prod_id`,`supplier_id`,`price`,`url`,`category`) VALUES ({$offers_values});") or die(mysql_error());
				if (mysql_affected_rows() > 0) {
					$ins++;
				} else {
					$errors[] = mysql_error();
				}
			}
		} else {
			$list_values = array($i[0],$i[1],$i[3],$i[4],$i[5],$i[6]);
			foreach ($list_values as &$list_value) {
				if ($list_value !== 'NULL') {
					$list_value = "'{$list_value}'";
				}
			}
			$list = implode(",",$list_values);
			mysql_query("INSERT INTO `db_list` (`name`,`year`,`barcode`,`platform`,`category`,`author`) VALUES ({$list});") or die(mysql_error());
			if (mysql_affected_rows() > 0) {
				$nwp++;
			} else {
				$errors[] = mysql_error();
			}
			$id = mysql_insert_id();
			$offers_values = array($id,$r,$i[2],$i[7],$i[5]);
			foreach ($offers_values as &$offer_value) {
				if ($offer_value !== 'NULL') {
					$offer_value = "'{$offer_value}'";
				}
			}
			$offers_values = implode(",",$offers_values);
			mysql_query("INSERT INTO `db_offers` (`prod_id`,`supplier_id`,`price`,`url`,`category`) VALUES ({$offers_values});") or die(mysql_error());
			if (mysql_affected_rows() > 0) {
				$upd++;
			} else {
				$errors[] = mysql_error();
			}
		}
	} else {
		$q = mysql_query("SELECT `id` FROM `db_list` WHERE `barcode` = '{$i[3]}';") or die (mysql_error());
		$id = mysql_result($q,0);
		if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_offers` WHERE `prod_id` = '{$id}' AND `supplier_id` = '{$r}';"),0) > 0) {
			$i[2] = ($i[2] !== 'NULL'?"'{$i[2]}'":$i[2]);
			$i[7] = ($i[7] !== 'NULL'?"'{$i[7]}'":$i[7]);
			mysql_query("UPDATE `db_offers` SET `price` = {$i[2]},`url` = {$i[7]} WHERE `prod_id` = '{$id}' AND `supplier_id` = '{$r}';") or die(mysql_error());
			if (mysql_affected_rows() > 0) {
				$upd++;
			} else {
				$errors[] = mysql_error();
			}
		} else {
			$offers_values = array($id,$r,$i[2],$i[7],$i[5]);
			foreach ($offers_values as &$offer_value) {
				if ($offer_value !== 'NULL') {
					$offer_value = "'{$offer_value}'";
				}
			}
			$offers_values = implode(",",$offers_values);
			mysql_query("INSERT INTO `db_offers` (`prod_id`,`supplier_id`,`price`,`url`,`category`) VALUES ({$offers_values});") or die(mysql_error());
			if (mysql_affected_rows() > 0) {
				$upd++;
			} else {
				$errors[] = mysql_error();
			}
		}
	}
	return array($ins,$upd,$nwp,$errors);
}
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
function get_barcode($r,$i) {
	if($page = file_get_contents("http://www.google.com/search?q=".urlencode($i[0])."+".urlencode($i[5])."+UPC")) {
		$upc = array();
		preg_match_all("/\d{12}/",$page,$upc);
		$upc = $upc[0];
		foreach($upc as $upc_code) {
			if (validate_UPC($upc_code)) {
				$upc_arr[] = $upc_code;
			}
		}
		$arr_fin = sorted($upc_arr);
		return array_push($arr_fin);
	}
}
?>
