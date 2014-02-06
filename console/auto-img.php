<?php
echo "<link type='text/css' rel='stylesheet' href='console.css'/>";
echo "<script type='text/javascript' src='console.js'></script>";
require_once '../include/setup.php';
$items = mysql_query("SELECT * FROM `db_list`;") or die("error: ".mysql_error());
$u = $total = $exists = 0;
while($item = mysql_fetch_assoc($items)) {
	$total++;
	if (!file_exists("../img/product-imgs/".$item['id'].".jpg")) {
		$plat = $type = "";
		switch($item['category']) {
			case 1:
				$type = "dvd";
				break;
			case 2:
				$type = "book";
				break;
			case 3:
				$type = urlencode("game ".$item['platform']);
				break;
		}
		if ($p = file_get_contents("https://www.google.co.uk/search?as_q=".urlencode($item['name'])."+{$type}+cover&tbs=iar:t,ift:jpg&tbm=isch")) {
			$links = explode('imgurl=',$p);
			$jpgs = array();
			$c = 0;
			foreach($links as $link) {
				$link = explode('&',$link);
				$link = $link[0];
				if (substr($link, -4) == ".jpg") {
					$c++;
					$jpgs[] = $link;
				}
				if ($c === 6) {
					break;
				}
			}
			echo "<div>";
			echo "<p>Using Query {$item['name']} ".urldecode($type)."...</p>";
			foreach($jpgs as $jpg) {
				echo "<div style='width:105px; height:177px; margin:10px; float:left;'>
					<img class='dvd' src='{$jpg}'/>
					<div class='blue-button' onclick=\"save_img('{$jpg}','{$item['id']}',this.parentNode.parentNode);\">Save Poster</div>
				</div>";
			}
			echo "</div>";
		} else {
			echo "<p>&#10003; Cound not Connect to Google Servers.</p>";
		}
		echo "<br><div style='clear:both;'></div><br>";
	} else {
		$exists++;
	}
	ob_end_flush(); 
	@ob_flush(); 
	flush(); 
	ob_start();
}
echo "<p>Already Exists : ({$exists} / {$total})</p>";
echo "<p>Script Executed.</p>";
?>
