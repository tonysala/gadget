<?php
include_once "setup.php";
$current_file = explode("/",$_SERVER["SCRIPT_NAME"]);
$current_file = end($current_file);
$current_file = ucwords(str_replace(".php","",str_replace("-"," ",$current_file)));
if($current_file === "Index") {
	$current_file = "Home";
} else if ($current_file === "Product") {
	$p_id = mysql_real_escape_string($_GET['id']);
	$p = getProdInfo($p_id);
	$current_file = "Sell ".$p['name'];
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
	if (!isset($_ntitle)) {
		$_ntitle = "";
	}
	echo "<title>{$_ntitle} {$current_file} | Website Name</title>"?>
	<meta charset="UTF-8">
	<meta name="description" content="">
	<link type="text/css" rel="stylesheet" href="css/style.css"/>	
	<link href="css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/main.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>	
	<script src="js/scriptcam.js" type="text/javascript"></script>
	<script src="js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
</head>
<body>
	<div id="header">
		<div id="header-content">
			<div id='header-logo'></div>
			<div id="nav">
				<ul class="nav-list">
					<li><a href="index.php">HOME</a></li>
					<li><a href="most-popular.php">MOST POPULAR</a></li>
					<li><a href="most-valuable.php">MOST VALUABLE</a></li>
					<li><a href="recent-sales.php">RECENT SALES</a></li>
					<li style="cursor:default;font-size: 24px;line-height: 32px;">&middot;</li>
					<?php
					if(isset($_SESSION['user'])) {
						echo '<li><img src="img/user.png" style="position:relative;top:3px;left:10px;"></li>';
						echo "<li><a href='account.php'>MY ACCOUNT </a><span style='font-size: 22px; color: #F8795A;'>{$_n}</span></li>";
						echo '<li><a href="logout.php">LOGOUT</a></li>';
						
					} else {
						echo '<li><a href="login.php">LOG IN</a></li>';
						echo '<li><a href="register.php">REGISTER</a></li>';
						echo '<li><img src="img/padlock.gif" style="position:relative;top:3px;right:10px;"></li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div>
<div id='screen-mask' style='display:none; background:url(img/mask.png) repeat; position:absolute; width:100%; height:100%; z-index:1000;'></div>
