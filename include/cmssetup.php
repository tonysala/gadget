<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
setlocale(LC_MONETARY, 'en_GB');

function add_cms_user($cmsuser, $cmspass) {
	$cmsuser = mysql_real_escape_string(htmlentities($cmsuser));
	$cmspass = sha1(md5($cmspass));
	mysql_query("INSERT INTO `db_admins` (`username` , `password`) VALUES ('{$cmsuser}','{$cmspass}')") or die(mysql_error());
	}
function cms_username_available($cmsuser) {
	$cmsuser = mysql_real_escape_string($cmsuser);
	$select = mysql_query("SELECT COUNT(`uid`) FROM `db_admins` WHERE `username`='{$cmsuser}'");
	return(mysql_result($select, 0) == '0'? true : false);
}
function cms_validate_login($cmsuser, $cmspass) {
	$cmsuser = mysql_real_escape_string($cmsuser);
	$cmspass = mysql_real_escape_string($cmspass);
	$select = mysql_query("SELECT COUNT(`uid`) FROM `db_admins` WHERE `username`='{$cmsuser}' AND `password` ='{$cmspass}'");
	return (mysql_result($select, 0) == '1')? true : false;
}
function noOffers($id) {
	$s = mysql_query("SELECT COUNT(`id`) FROM `db_offers` WHERE `prod_id`='{$id}'");
	$n = (mysql_result($s, 0));
	return(mysql_result($s, 0) == '0'? "&#10007; ".$n." Offers" : "&#10003; ".$n." Offers");
}
function starRating($n) {
	$n = round($n);
	$str = "";
	for ($i = 1; $i <= $n; $i++) {
		$str .= "&#9733;";
	}
	return $str;
}
mysql_connect ("localhost", "broker_master", "Gadgets01") or die ('MySQL connection failed.');
mysql_select_db("broker_master") or die('Cannot select database.');
$n = "";
$prepend_title = "";
$page_name = explode("/", $_SERVER['SCRIPT_NAME']);
$page_name = end($page_name);
$page_name = substr($page_name, 0, -4);
$required = array("cms","cmslogout");

if(isset($_SESSION['cmsuser']) === false){
	if((isset($_COOKIE['cmsusername'], $_COOKIE['cmspassword'])) && (cms_validate_login($_COOKIE['cmsusername'],$_COOKIE['cmspassword']))){
		$_SESSION['cmsuser'] = $_COOKIE['cmsusername'];
		header("Location: cms.php");
		exit();
	} else if(in_array($page_name, $required)) {
		header("Location: cmslogin.php");
		exit();
	}
}
?>
