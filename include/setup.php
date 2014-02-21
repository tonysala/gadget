<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
setlocale(LC_MONETARY, 'en_GB');

function inc_pop($id,$by) {
	mysql_query("UPDATE `db_list` SET `popularity` = `popularity` + {$by} WHERE `id` = '{$id}';");
}
function add_user($user, $pass, $title, $fname, $sname, $email) {
	$user = mysql_real_escape_string(htmlentities($user));
	$pass = sha1(md5($pass));
	$title = mysql_real_escape_string(htmlentities($title));
	$fname = mysql_real_escape_string(htmlentities($fname));
	$sname = mysql_real_escape_string(htmlentities($sname));
	$email = mysql_real_escape_string(htmlentities($email));
	mysql_query("INSERT INTO `db_users` (`username` , `password` , `title` , `fname` , `sname` , `email`) VALUES ('{$user}','{$pass}','{$title}','{$fname}','{$sname}','{$email}')") or die(mysql_error());
	if (mysql_affected_rows > 0) {
		send_mail($email,$user,$fname,"register");
	}
}
function send_mail($to,$user,$fname,$type,$dvd="") {
	$sub = "Automovie.co.uk";
	$m = "<style>
			* , p
			{
				font-family:Andale Mono;
				font-size:12px;
				color:white!important;
				margin:2px;
				text-shadow: 0 1px rgba(0, 0, 0, .2);
			}
			body 
			{
				margin:10px;
				box-shadow:0 1px 3px rgba(0,0,0,.3);
				border-radius:5px;
				padding:10px;
				background:#EE7544;
			}
			</style>";
	switch($type) {	
		case "register":
			$m .= "<h2><u>Account Confirmation</u></h2>";
			$m .= "<p>Hi {$fname},<br> Your account ({$user}) has been created succesfully. To go to your account 
			click <a href='localhost/login.php'>here</a> 
			and enter your login details, from here you can keep an eye on the price of movies in your watch list.</p>";
			break;
		case "resetpass":
			$token = hash("sha256",$user.mt_rand().uniqid());
			$fi = $token."#".(time()+6*3600);
			$f = "resets/{$user}.txt";
			$fh = fopen($f, 'w');
			fwrite($fh, $fi);
			fclose($fh);
			$m .= "<h2><u>Password Reset</u></h2>";
			$m .= "<p>Hi {$fname},<br><br>
			<u>Account Details</u><br>
			Username: {$user}<br>
			Email: {$to}<br><br>
			Follow this <a href='http://localhost/c/reset.php?token={$token}&user=tonysala'>link</a> to reset your password.</p>";
			break;
		case "notify":
			$m = "<h2>Someone wants to buy {$dvd}</h2>";
			$m .= "<p>You asked us to keep you informed on the status of this dvd.<br>
			You can now sell it. Click here to go to <a href='localhost'>Localhost.</a></p>";
			break;
	}
	$headers  = 'From :tony@tronfo.com'."\r\n";
	$headers .= 'Reply-To: tony@tronfo.com'."\r\n";
	$headers .= 'X-Mailer: PHP/'.phpversion()."\r\n";
	$headers .= 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1';
	mail($to,$sub,$m,$headers);
}
function username_available($user) {
	$user = mysql_real_escape_string($user);
	$select = mysql_query("SELECT COUNT(`uid`) FROM `db_users` WHERE `username`='{$user}'");
	return(mysql_result($select, 0) == '0'? true : false);
}
function validate_login($user, $pass) {
	$user = mysql_real_escape_string($user);
	$pass = mysql_real_escape_string($pass);
	$select = mysql_query("SELECT COUNT(`uid`) FROM `db_users` WHERE `username`='{$user}' AND `password` ='{$pass}'");
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
function roundRating($n) {
	return (round($n * 2) / 2) * 10;
}
function getProdInfo($pid) {
	$pid = mysql_real_escape_string($pid);
	$d = mysql_query("SELECT * FROM `db_list` WHERE `id` = '{$pid}';")or die(mysql_error());
	return mysql_fetch_assoc($d);
}
function getSupInfo($sid) {

	$sid = mysql_real_escape_string($sid);
	$s = mysql_query("SELECT * FROM `db_supplier` WHERE `id` = '{$sid}';")or die(mysql_error());
	return mysql_fetch_assoc($s);
}
mysql_connect ("localhost", "broker_master", "") or die ('MySQL connection failed.');
mysql_select_db("broker_master") or die('Cannot select database.');
mysql_query("UPDATE `db_analytics` SET `page_views` = `page_views` + 1;") or die(mysql_error());

if (isset($_SESSION['user'])) {
	$u = mysql_query("SELECT * FROM `db_users` WHERE `username` = '".mysql_real_escape_string($_SESSION['user'])."';") or die(mysql_error());
	$u = mysql_fetch_assoc($u);
	$_u = $u['uid'];
	$_nval = 0;
	$_n = $_ntitle = "";
	if(isset($_GET['clear_notif'])) {
		$_nquery = mysql_query("SELECT * FROM `db_watch` WHERE `uid` = '{$_u}';") or die(mysql_error());
		while($r = mysql_fetch_assoc($_nquery)) {
			$m = mysql_query("SELECT * FROM `db_offers` WHERE `prod_id` = '{$r['pid']}' ORDER BY `price` DESC LIMIT 0,1;") or die(mysql_error());
			$m = mysql_fetch_assoc($m);
			$maxprice = $m['price'];
			if ($r['price'] != $maxprice && $maxprice != "" && $r['price'] =! 0) {
				$time = time();
				mysql_query("UPDATE `db_watch` SET `price` = '{$maxprice}' , `timestamp` = '{$time}' WHERE `uid` = '{$_u}' AND `pid` = '{$r['pid']}'");	
			}
		}
		$_n = $_SESSION['notifications'] = $_ntitle = "";
		$_nval = $_SESSION['notif_value'] = 0;
				
	}
	else if (isset($_SESSION['notifications'],$_SESSION['notif_value'])) {
		$_n = $_SESSION['notifications'];
		$_nval = $_SESSION['notif_value'];
		$_ntitle = "{$_n}";
		if($_nval > 0) {
			$st[] = "You have {$_nval} notifications. <br><a href='account.php#notifications'>View Notifications</a><span style='cursor:default;font-size: 24px;line-height: 32px;'> &middot; </span><a href='account.php?clear_notif'>Clear Notifications</a>";
		}
	} else {
		$_nquery = mysql_query("SELECT * FROM `db_watch` WHERE `uid` = '{$_u}';") or die(mysql_error());
		while($r = mysql_fetch_assoc($_nquery)) {
			$m = mysql_query("SELECT * FROM `db_offers` WHERE `prod_id` = '{$r['pid']}' ORDER BY `price` DESC LIMIT 0,1;") or die(mysql_error());
			$m = mysql_fetch_assoc($m);
			$maxprice = $m['price'];
			if ($r['price'] != $maxprice && $maxprice != "" && $r['price'] =! 0) {
				$_nval++;
			}
		}
		if ($_nval != 0) {
			$_npo = $_nval + 1;
			$_ntitle = "{$_n}";
			if ($_n > 9) {
				$_n = "&#10110;+";
			} else {
				$_n = "&#1010{$_npo};";
			}
			$st[] = "You have {$_nval} notifications. <br><a href='account.php#notifications'>View Notifications</a><span style='cursor:default;font-size: 24px;line-height: 32px;'> &middot; </span><a href='account.php?clear_notif'>Clear Notifications</a>";
		} else {
			$_n = "";
			$_nval = 0;
		}
		$_SESSION['notifications'] = $_n;
		$_SESSION['notif_value'] = $_nval;
	}
} else {
	$n = "";
	$prepend_title = "";
}
$page_name = explode("/", $_SERVER['SCRIPT_NAME']);
$page_name = end($page_name);
$page_name = substr($page_name, 0, -4);
$sess_required = array("account","logout");
$no_sess_required = array("reset","login");

if(isset($_SESSION['user']) === false) {
	if((isset($_COOKIE['username'], $_COOKIE['password'])) && (validate_login($_COOKIE['username'],$_COOKIE['password']))) {
		$_SESSION['user'] = $_COOKIE['username'];
		header("Location: account.php");
		exit();
	} else if(in_array($page_name, $sess_required)) {
		header("Location: login.php");
		exit();
	}
} else {
	if(in_array($page_name, $no_sess_required)) {
		header("Location: account.php");
		exit();
	}
}
?>
