<?php
include_once 'include/cmsheader.php';
echo "<style>
body {
	background: #666;
}
footer {
	position:absolute!important;
}
.login-input {
	padding:2px 4px!important;
}
p {
	overflow: initial!important;
	text-overflow: initial!important;
}
</style>";
if ((isset($_GET['inc_dvds'])||isset($_GET['inc_games'])||isset($_GET['inc_books']))&&isset($_GET['clm_price'])) {
	$c_ti = (empty($_GET['clm_title']) ? "0" : $_GET['clm_title']);
	$c_ye = (empty($_GET['clm_year']) ? "0" : $_GET['clm_year']);
	$c_pr = (empty($_GET['clm_price']) ? "0" : $_GET['clm_price']);
	$c_upc = (empty($_GET['clm_upc']) ? "0" : $_GET['clm_upc']);
	$c_typ = (empty($_GET['clm_type']) ? "0" : $_GET['clm_type']);
	$c_cat = (empty($_GET['clm_cat']) ? "0" : $_GET['clm_cat']);
	$c_aut = (empty($_GET['clm_author']) ? "0" : $_GET['clm_author']);
	$c_link = (empty($_GET['clm_link']) ? "0" : $_GET['clm_link']);
	$dvd_def = (empty($_GET['dvd_def']) ? "" : $_GET['dvd_def']);
	$gm_def = (empty($_GET['gm_def']) ? "" : $_GET['gm_def']);
	$bk_def = (empty($_GET['bk_def']) ? "" : $_GET['bk_def']);
	$o_t = (empty($_GET['ovr_type']) ? "" : $_GET['ovr_type']);
	$o_y = (empty($_GET['ovr_year']) ? "" : $_GET['ovr_year']);
	$dvd = (empty($_GET['inc_dvds']) ? "0" : "1");
	$gms = (empty($_GET['inc_games']) ? "0" : "1");
	$bks = (empty($_GET['inc_books']) ? "0" : "1");
	$enc = (empty($_GET['enc_char']) ? '' : $_GET['enc_char']);
	$hdr = (empty($_GET['hdr']) ? "0" : "1");
	$opts = array($bks,$gms,$dvd,$c_ti,$c_ye,$c_pr,$c_upc,$c_typ,$c_cat,$c_aut,$c_link,$enc,$hdr,$dvd_def,$bk_def,$gm_def,$o_y,$o_t);
	
	foreach ($opts as &$o) {
		$o = mysql_real_escape_string($o);
	}
	$fo = "'".implode("','",$opts)."'";
	if (mysql_result(mysql_query("SELECT COUNT(`rec_id`) FROM `db_schema` WHERE `rec_id` = '".mysql_real_escape_string($_GET['recycler'])."';"),0) > 0) {
		mysql_query("DELETE FROM `db_schema` WHERE `rec_id` = '".mysql_real_escape_string($_GET['recycler'])."';") or die(mysql_error());
	}
	mysql_query("INSERT INTO `db_schema` (`con_books`,`con_games`,`con_dvds`,`pos_title`,`pos_year`,`pos_price`,`pos_upc`,
	`pos_type`,`pos_cat`,`pos_auth`,`pos_link`,`enclosing`,`header`,`def_dvd`,`def_book`,`def_game`,`ovr_year`,`ovr_type`,`rec_id`) VALUES ({$fo},'".mysql_real_escape_string($_GET['recycler'])."') ;") or die(mysql_error());
}
if (!empty($e)) {
		echo '<ul class="t errorbox"><li>'.implode("</li><li>", $e).'</li></ul><br>';
	}
if (!empty($st)) {
	echo '<ul class="t successbox"><li>'.implode("</li><li>", $st).'</li></ul>';
}
echo "
<link type='text/css' rel='stylesheet' href='css/style.css'/>
<div id='schema_window' class='console' style='top:92px; display:block; height:456px; width:755px;'>
	<form action='schema.php' method='get' style='float:left; width:335px; height:472px; padding:14px;'>
		<label><u>CSV SCHEMA</u></label><br><br>
		<label>CSV Contains:</label>
		<label>DVDs</label><input onchange='' type='checkbox' name='inc_dvds' style='position:relative;top:4px;left:6px;'>
		<label>Games</label><input onchange='' type='checkbox' name='inc_games' style='position:relative;top:4px;left:6px;'>
		<label>Books</label><input onchange='' type='checkbox' name='inc_books' style='position:relative;top:4px;left:6px;'>
		<br><br>
		<label><u>If CSV Contains Multiple Types Of Items</u></label><br><br>
		<label>In Category Column Each is Defined As:</label><br><br>
		<label>DVD: </label><input type'text' placeholder='' style='width:38px;' name='dvd_def' class='login-input'>
		<label>BOOK: </label><input type'text' placeholder='' style='width:38px;' name='bk_def' class='login-input'>
		<label>GAME: </label><input type'text' placeholder='' style='width:38px;' name='gm_def' class='login-input'>
		<br><br>
		<label><u>Column Numbers (Starting From 1)</u></label><br><br>
		<label>Title:</label><input type'text' placeholder='' style='width:14px;' name='clm_title' class='login-input'>
		<label>Year:</label><input type'text' placeholder='' style='width:14px;' name='clm_year' class='login-input'>
		<label>Price: </label><input type'text' placeholder='' style='width:14px;' name='clm_price' class='login-input'>
		<label>UPC: </label><input type'text' placeholder='' style='width:14px;' name='clm_upc' class='login-input'>
		<label>Type: </label><input type'text' placeholder='' style='width:14px;' name='clm_type' class='login-input'>
		<label>Cat: </label><input type'text' placeholder='' style='width:14px;' name='clm_cat' class='login-input'>
		<label>Author:</label><input type'text' placeholder='' style='width:14px;' name='clm_author' class='login-input'>
		<label>Link:</label><input type'text' placeholder='' style='width:14px;' name='clm_link' class='login-input'>
		<br><br>
		<label>Enclosing:</label><input type'text' placeholder='' style='width:10px;' name='enc_char' class='login-input'>
		<label>Header Row:</label><input onchange='' type='checkbox' name='hdr' style='position:relative;top:4px;left:6px;'>
		<br><br>
		<label><u>Overrides</u></label>
		<br><br>
		<label>Year:</label><input type'text' placeholder='' style='width:34px;' name='ovr_year' class='login-input'>
		<label>Type: </label><input type'text' placeholder='' style='width:34px;' name='ovr_type' class='login-input'>
		<br><br>
		<input type='hidden' value='{$_GET['recycler']}' name='recycler'>
		<input type='submit' value='Save Schema' style='float:left;margin-right:20px;padding:3px 2px!important;color:white!important;height:26px!important;border:1px solid #22499C;' class='blue-button'>
		<input type='button' onclick='document.getElementById(\"schema_window\").style.display =\"none\";' value='Cancel' style='float:left;padding:3px 2px!important;color:white!important;height:26px!important;border:1px solid #9C2C22;' class='red-button'>
		<div style='clear:both;'></div>
	</form>
	<div style='float:left; max-width:361px; text-overflow:none; height:424px; padding:14px; overflow-y:hidden;overflow-x:scroll;'>
	<p><u>Preview CSV File</u></p>";
$csvfile = mysql_result(mysql_query("SELECT `url` FROM `db_supplier` WHERE `id` = '".mysql_real_escape_string($_GET['recycler'])."';"),0);
if (!empty($csvfile)) {
	if ($f = @fopen($csvfile,"r")) {
		$fs = strlen(file_get_contents($csvfile));
		$c = 0;
		while (($data = fgetcsv($f,$fs,',','"')) !== FALSE) {
			$c++;
			if ($c !== 15) {
				echo "<p style='white-space:nowrap;'>".implode(",",$data)."</p>";
			} else {
				break;
			}
		}
	}
}
echo "</div><div style='clear:both;'></div>";
?>
