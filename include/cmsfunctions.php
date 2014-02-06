<?php
function showError($e) {
	return '<div class="bg-section"><div class="width-reg"><ul class="errorbox"><li>&#10007 '.$e.'<br><a href="javascript: window.history.back();">Go Back.</a></li></ul></div></div>	</div>
	</div><footer style="height:58px; width:100%; position:relative; bottom:0px;"><div id="footer-wrap" style="height:40px;padding-top: 10px;">
	<div class="footer-desc" style="margin:0 auto; float:none; height:20px; text-align:center;">
	<p style="margin:0;">Website Design By <a href="http://www.peopleperhour.com/people/tony/web-developer-designer/317579">Sala Web Design</a></p>
	</div></div></footer></div></body></html>';
}
function execute_sql($sql,&$st,&$e,&$page) {
	$sql = "mysql_query(\"".$sql."\");";
	eval($sql);
	if (mysql_affected_rows() !== -1) {
		$st[] = "Affected rows = ".mysql_affected_rows();
	} else {
		$e[] = "Error Occured : ".mysql_error();
	}
	$page = "settings";
	return true;
}
function get_search_query($q,&$page) {
	$page = "product";
	return " WHERE `name` REGEXP '{$q}'";
}
function set_new_supplier($s,$u,&$st,&$e,&$page) {
	mysql_query("INSERT INTO `db_supplier` (`name`) VALUES ('{$s}');") or die(showError(mysql_error()));
	$sid = mysql_insert_id();
	if ($sid < 0 || !file_put_contents("img/suppliers/{$sid}.jpg", file_get_contents($u))) {
		mysql_query("DELETE FROM `db_supplier` WHERE `id` = '{$sid}';") or die(showError(mysql_error()));
		$e[] = "&#10007; Failed to Add Recycler.";
	} else {
		$st[] = "&#10003; Added Recycler.";
		mkdir("cron/{$sid}/", 0777);
	}
	$page = "recyclers";
}
function set_new_admin($p,$u,&$page) {
	mysql_query("INSERT INTO `db_admins` (`username`,`password`) VALUES ('{$u}','{$p}');") or die(showError(mysql_error()));
	$page = 'admins';
}
function unset_admin($a,&$st,&$e,&$page) {
	$no_admins = mysql_query("SELECT COUNT(`uid`) FROM `db_admins`;") or die(showError(mysql_error()));
	if (mysql_result($no_admins,0) == 1) {
		$e[] = "&#10007; You cannot delete the last admin user.";
	} else {
		mysql_query("DELETE FROM `db_admins` WHERE `uid` = '{$a}';") or die(showError(mysql_error()));
		$st[] = "&#10003; Successfully deleted admin user.";
	}
	$page = 'admins';
}
function unset_product($id,&$st,&$page) {
	mysql_query("DELETE FROM `db_list` WHERE `id` = '{$id}';") or die(showError(mysql_error()));
	$st[] = "&#10003; Successfully deleted DVD.";
	unlink("img/product-imgs/{$id}.jpg");
	$page = "product";
}
function unset_supplier($r,&$st,&$page) {
	mysql_query("DELETE FROM `db_supplier` WHERE `id` = '{$r}';") or die(showError(mysql_error()));
	$st[] = "&#10003; Successfully deleted Recycler.";
	$page = "recyclers";
}
function set_new_prod($ti,$ye,$ty,$ca,$ba,$po,$id,$au,&$st,&$e,&$page) {
	$values = array($ti,$ye,$ty,$ca,$ba,$id,$au);
	foreach ($values as &$val) {
		$val = (empty($val) ? "NULL" : "'{$val}'");
	}
	if (empty($ty)||empty($ye)||empty($ty)||empty($ca)) {
		$e[] = "&#10007; You Left Some Required Fields Blank.";
	} else {
		switch($ca) {
			case "BOOK":
				if (!empty($au)) {
					mysql_query("INSERT INTO `db_list` (`name`,`year`,`platform`,`category`,`barcode`,`imdb_id`,`author`,) VALUES (".implode(",",$values).");") or die(showError(mysql_error()));
					if (mysql_affected_rows() > 0) {
						$st[] = "&#10003; Product Added : {$ti} [{$ty}]";
					} else {
						$e[] = "&#10007; Error adding Product [ ".mysql_error()." ]";
					}
				} else {
					$e[] = "&#10007; You Left the Required Author Field Blank.";
				}
				break;
			default:
				mysql_query("INSERT INTO `db_list` (`name`,`year`,`platform`,`category`,`barcode`,`imdb_id`,`author`) VALUES (".implode(",",$values).");") or die(showError(mysql_error()));
				if (mysql_affected_rows() > 0) {
					$st[] = "&#10003; Product Added : {$ti} [{$ty}]";
				} else {
					$e[] = "&#10007; Error adding Product [ ".mysql_error()." ]";
				}
				break;
		}
	}
	$pid = mysql_insert_id();
	if (!empty($po)) {
		file_put_contents("img/product-imgs/{$pid}.jpg", file_get_contents($po));
		if ($pid === false) {
			$e[] = "&#10007; Product cound not be added to the database.";
		} else {
			$st[] = "&#10003; Product has been added to the database.";
		}
	}
	$page = "product";
	return $pid;
}
function set_barcode($bc,$id,&$page,&$st) {
	$bc = mysql_real_escape_string($bc);
	$id = mysql_real_escape_string($id);
	if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_list` WHERE `barcode` = '{$bc}';"),0) > 0) {
		mysql_query("INSERT INTO `db_alts` (`pid`,`barcode`) VALUES ('{$id}','{$bc}');") or showError(mysql_error());
		$st[] = "&#10003; Alternative barcode has been added to the database.";
	} else {
		mysql_query("UPDATE `db_list` SET `id` = '{$id}' , `barcode` = '{$bc}' WHERE `id` = {$id};") or showError(mysql_error());
		$st[] = "&#10003; New barcode has been added to the database.";
	}
	$page = "product";
}
function get_poster_images($pid) {
	$pid = mysql_real_escape_string($pid);
	$o = "";
	$p = mysql_query("SELECT * FROM `db_list` WHERE `id` = '{$pid}';")or die(mysql_error());
	$p = mysql_fetch_assoc($p);
	$aspect_ratio = ($p['category'] == "2"? "" : "iar:t,");
	if ($pg = file_get_contents("https://www.google.co.uk/search?as_q=".urlencode($p['name'])."+".urlencode($p['platform'])."+cover&tbs={$aspect_ratio}ift:jpg&tbm=isch"))
	{
		$o .= "<style>
		.dvd {
			width: 103px;
			height: 147px;
			border-radius: 2px;
			box-shadow: 0 0 0 1px #666;
			border: 1px solid white;
			float: left;
			margin:0;
		}
		</style>
		<div class='successbox' style='top:24%;position:absolute;left:0;right:0;margin-left:auto;margin-right:auto;width:750px;height:300px;'>";
		$links = explode('imgurl=',$pg);
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
		$o .= "<div>";
		$o .= "Choose Image For {$p['name']} [{$p['platform']}]<br><br>";
		foreach($jpgs as $jpg)
		{
			$o .= "
			<div style='width:105px; height:177px; margin:10px; float:left;'>
				<img class='dvd' src='{$jpg}'/>
				<div style='margin: 12px 1px;' class='blue-button' onclick=\"save_img('{$jpg}','{$p['id']}',this.parentNode.parentNode);\">Save Poster</div>
			</div>";
		}
		$o .= "<br style='clear:both;'><br><p style='cursor:pointer;' onclick=\"this.parentNode.parentNode.style.display = 'none';\" class='text'>[Cancel]</p></div>";
	}
	else
	{
		$e[] = "&#10003; Cound not Connect to Google Servers. Cannot Choose Product Image.";
	}
	$o .= "<br><div style='clear:both;'></div><br></div>";
	return $o;
}
function validate_UPC($upc) { 
	if ($upc!="") { 
		$checkdigit = substr($upc, -1); 
		$code = substr($upc, 0, -1); 		 
		$checksum = createCheckDigit($code); 
		if ($checkdigit == $checksum) { 
			return true; 
		} else { 
			return false; 
		} 
	} else { 
		return false; 
	} 
}
function createCheckDigit($code) { 
	if ($code) { 
		for ($counter=0;$counter<=strlen($code)-1;$counter++) { 
			$codearr[]=substr($code,$counter,1); 
		} 
		 
		for ($counter=0;$counter<=count($codearr)-1;$counter++) { 
			if ( $counter&1 ) { 
				$evensum = $evensum + $codearr[$counter]; 
			} else { 
				$oddsum = $oddsum + $codearr[$counter]; 
			} 
		} 
		 
		$oddsum = $oddsum *3; 
		$oddeven = $oddsum + $evensum; 
		 
		for ($number=0;$number<=9;$number++) { 
			if (($oddeven+$number)%10==0) { 
				$checksum = $number; 
			} 
		} 
		 
		return $checksum; 
	} else { 
		return false; 
	} 
} 
?>
