<?php
require_once '../include/setup.php';

if (isset($_GET['dvd_id'],$_GET['poster_url']))
{
	echo (file_put_contents("../img/product-imgs/{$_GET['dvd_id']}.jpg", file_get_contents($_GET['poster_url'])) ? "true" : "error saving file");
}
else if (isset($_GET['dvd_id'],$_GET['platform']))
{
	if (mysql_query("UPDATE `db_list` SET `platform` = '{$_GET['platform']}' WHERE `id` = '{$_GET['dvd_id']}';"))
	{
		echo "true";
	}
	else
	{
		echo "error saving file";
	}
} else if (isset($_GET['dup_id1'],$_GET['dup_id2'])) {
	$bc = mysql_result(mysql_query("SELECT `barcode` FROM `db_list` WHERE `id` = '{$_GET['dup_id2']}';"),0);
	mysql_query("INSERT INTO `db_alts` (`barcode`,`pid`) VALUES ('{$bc}','{$_GET['dup_id1']}');");
	mysql_query("DELETE FROM `db_list` WHERE `id` = '{$_GET['dup_id2']}';");
} else if (isset($_GET['diff_id1'],$_GET['diff_id2'])) {
	file_put_contents("not_diff.txt","{$_GET['diff_id1']},{$_GET['diff_id2']}|",FILE_APPEND) or die("Error Writing to File.");
}
echo "Error!";
?>
