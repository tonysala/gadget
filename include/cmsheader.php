<?php
include_once "cmssetup.php";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
	if (!isset($_ntitle))
	{
		$_ntitle = "";
	}
	echo "<title>{$_ntitle} CMS</title>"?>
	<meta charset="UTF-8">
	<meta name="description" content="">
	
	<link type="text/css" rel="stylesheet" href="css/style.css"/>
	<script src="js/main.js" type="text/javascript"></script>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<link href="css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
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
				if(isset($_SESSION['cmsuser']))
				{
					echo '<li><img src="img/user.png" style="position:relative;top:3px;left:10px;"></li>';
					echo "<li><a href='cms.php'>CMS </a><span style='font-size: 22px; color: #F8795A;'>{$_n}</span></li>";
					echo '<li><a href="cmslogout.php">LOGOUT</a></li>';
				}
				else
				{
					echo '<li><a href="cmslogin.php">LOGIN</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
</div>
