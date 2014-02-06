<?php
require_once "include/setup.php";

if (isset($_GET['q'])) {
	$q = mysql_real_escape_string(trim($_GET['q']));
	$count = $count2 = 1;
	$terms = 0;
	$queries = explode(" ", $q);
	$results = array();
	$cat = "";
	$barcode = str_replace(" ","",$q);
	if (is_numeric($barcode) && strlen($barcode) > 5) {
		echo "<ul id='auto_search'>";
		echo "<a style='cursor:default;text-align:center; height:35px; line-height:25px;'><li style='height:25px;'>Barcode Search</li></a>";
		$matches = mysql_query("SELECT `id`,`name`,`year`,`barcode`,`platform` FROM `db_list` WHERE `barcode` REGEXP '{$barcode}' ORDER BY `popularity` LIMIT 0,5;");
		if (is_resource($matches)) {
			while($row = mysql_fetch_assoc($matches)) {
				$result = "<a href='product.php?id={$row['id']}'><li><div class='search-prev' style='background:url(img/product-imgs/{$row['id']}.jpg); background-size:32px 51px;'></div>{$row['name']} [{$row['platform']}] [{$row['year']}]<span style='color:#888'>[".str_replace($barcode,"<span style='color:#D87777'>{$barcode}</span>",$row['barcode'])."]</span></li></a>";
				if (in_array($result, $results) == true) {
					array_unshift($results, $result);
				} else {
					$results[] = $result;
					++$count;
				}
			}
		}
	} else {
		if (isset($_GET['type'])) {
			switch($_GET['type']) {
				case 0:
					$cat = "";
					break;
				case 1:
					$cat = "AND `category` = '1'";
					break;
				case 2:
					$cat = "AND `category` = '2'";
					break;
				case 3:
					$cat = "AND `category` = '3'";
					break;
			}
		}
		echo "<ul id='auto_search'>";	
		foreach ($queries as $query) {
			if(!trim($query) == "") {
				$matches = mysql_query("SELECT `id`,`name`,`year`,`platform` FROM `db_list` WHERE `name` REGEXP '{$query}' {$cat} ORDER BY `popularity` DESC LIMIT 0,5;");
				if (is_resource($matches)) {
					while($row = mysql_fetch_assoc($matches)) {
						$result = "<a href='product.php?id={$row['id']}'><li><div class='search-prev' style='background:url(img/product-imgs/{$row['id']}.jpg); background-size:32px 51px;'></div>{$row['name']}  [{$row['platform']}] [{$row['year']}]</li></a>";
						if (in_array($result, $results) == true) {
							array_unshift($results, $result);
						} else {
							$results[] = $result;
							++$count;
						}
					}
				}
			}
		}
	}
	if ($count == 1) {
		echo "<a href='search.php?q=".urlencode($q)."' style='text-align:center; height:35px; line-height:25px;'><li style='height:25px;'>No Results</li></a>";
	}
	else {
		$results = array_unique($results);
		foreach($results as $result) {
			if ($count2 > 5) {
				break;
			}
			$count2++;
			echo $result;
		}
		echo "<a style='text-align:center; height:35px; line-height:25px;' href='search.php?q=".urlencode($q)."&type=".urlencode($_GET['type'])."'><li style='height:25px;'>Show More...</li></a>";
	}
	
	echo "</ul>";
	exit;
}
else if(isset($_GET['p'],$_GET['u'],$_GET['l'])) {
	$p = mysql_real_escape_string($_GET['p']);
	$u = mysql_real_escape_string($_GET['u']);
	$l = mysql_real_escape_string($_GET['l']);
	$extraparam = "";
	if(isset($_GET['s'])) {
		$s = mysql_real_escape_string($_GET['s']);
		$extraparam = " AND `sid` = {$s}";
	}
	mysql_query("DELETE FROM `db_{$l}` WHERE `uid` = '{$u}' AND `pid` = '{$p}'{$extraparam}");	
	exit;
}
else if(isset($_GET['bc'])) {
	$match = mysql_query("SELECT COUNT(`id`) FROM `db_list` WHERE `barcode` = '".mysql_real_escape_string($_GET['bc'])."' LIMIT 0,1;");
	if (mysql_result($match,0) == 0) {
		echo 0;
		exit;
	}
	else {
		$match = mysql_query("SELECT `id` FROM `db_list` WHERE `barcode` = '".mysql_real_escape_string($_GET['bc'])."';");
		$match = mysql_fetch_assoc($match);
		$maxprice = mysql_query("SELECT MAX(`price`) FROM `db_offers` WHERE `prod_id` = {$match['id']} LIMIT 0,1;");
		if (mysql_result($maxprice,0) == '') {
			$maxprice = "&#10007; 0 Offers";
		}
		else {
			$maxprice = "&#10007; View Offers";
		}
		echo "<div id='main-product' class='dvd-inlay' style='margin:12px 62px; background:url(img/product-imgs/{$match['id']}.jpg);background-size:121px 174px;'></div>";
		echo "<div class='actions-list' style='margin:0 3px;'>
			<a href='product.php?id={$match['id']}&bc' style='width:130px;'>{$maxprice}</a>
			<div style='width:110px;' onclick='checkForBarcode();'>Scan Another</div>
		</div>";
	}
}
else if (isset($_GET['dvd_id'],$_GET['poster_url']))
{
	echo (file_put_contents("img/product-imgs/{$_GET['dvd_id']}.jpg", file_get_contents($_GET['poster_url'])) ? "true" : "false");
}
?>







