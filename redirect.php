<?php
if (isset($_GET['url'],$_GET['sid'],$_GET['pid']))
{
	include_once "include/setup.php";
	$url = urlencode($_GET['url']);
	$pid = mysql_real_escape_string($_GET['pid']);
	$sid = mysql_real_escape_string($_GET['sid']);
	inc_pop($pid,10);
	if (isset($_SESSION['user']))
	{
		$u = mysql_real_escape_string($_SESSION['user']);
		$u = mysql_query("SELECT `uid` FROM `db_users` WHERE `username` = '{$u}';")or die(mysql_error());
		$u = mysql_fetch_assoc($u);
		$uid = $u['uid'];
	}
	else
	{
		$uid = "0";
	}
	$t = time();
	mysql_query("UPDATE `db_list` SET `sold` = '{$t}' WHERE `id` = '{$pid}';")or die(mysql_error());
	$select = mysql_query("SELECT COUNT(`id`) FROM `db_clicks` WHERE `sid` = '{$sid}' AND `pid`='{$pid}' AND `uid` ='{$uid}'")or die(mysql_error());
	if (mysql_result($select, 0) == 0)
	{
		mysql_query("INSERT INTO `db_clicks` (`uid`,`sid`,`pid`,`url`,`timestamp`) VALUES ('{$uid}','{$sid}','{$pid}','".mysql_real_escape_string($_GET['url'])."','{$t}');")or die(mysql_error());
	}
	header("Location: ".urldecode($url));
}


?>
