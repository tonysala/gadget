<?php
include_once "include/header.php";

function alreadyRated($id) {
	if (!isset($_SESSION['rating'][0])) {
		return false;
	}
	for ($i = 1; $i <= $_SESSION['rating'][0]; $i++) {
		if ($_SESSION['rating'][$i] == $id) {
			return true;
		}
	}
	return false;
}
if (isset($_GET['rate'],$_GET['r'],$_GET['s'],$_SESSION['user'])) {
	$r = $fr = intval(mysql_real_escape_string($_GET['r']));
	$s = mysql_real_escape_string($_GET['s']);
	if (isset($_SESSION['rating']) && (alreadyRated($s) === true)) {
		$e[] = "&#10007; You have already rated this Recycler. To keep the rating system fair we only allow you to rate each Recycler once.";
	}
	else {
		if(!is_numeric($r) || $r > 5 || $r < 1 || $r == "undefined") {
			$e[] = "&#10007; An error occured whilst submitting your rating. Please try again.";
		}
		else {
			$rating_query = mysql_query("SELECT `votes`,`vote_sum` FROM `db_supplier` WHERE `id` = '{$s}';")or die(mysql_error());
			$rating_query = mysql_fetch_assoc($rating_query);
			$votes = $rating_query['votes'];
			$vote_sum = $rating_query['vote_sum'];
			echo $votes;
			if($votes !== 0) {
				$new_rating = round((($r + $vote_sum) / $votes),2);
			}
			else {
				$new_rating = $r;
			}
			$vote_sum = $vote_sum + $r;
			mysql_query("UPDATE `db_supplier` SET `rating` = '{$new_rating}', `votes` = `votes` + '1', `vote_sum` = '{$vote_sum}' WHERE `id` = '{$s}';")or die(mysql_error());
			$st[] = "&#10003; Thanks for submitting  your rating. {$fr} / 5 ";
			if(!isset($_SESSION['rating'][0])) {
				$_SESSION['rating'][0] = 0;
			}
			$_SESSION['rating'][0]++;
			$_SESSION['rating'][$_SESSION['rating'][0]] = $s;
		}
	}
}
else if(isset($_GET['watch']))
{
	$pid = mysql_real_escape_string($_GET['watch']);
	inc_pop($pid,10);
	$select = mysql_query("SELECT COUNT(`id`) FROM `db_watch` WHERE `pid`='{$pid}' AND `uid` ='{$_u}'");
	$present = (mysql_result($select, 0) > '0')? true : false;
	$find = mysql_query("SELECT COUNT(`id`) FROM `db_list` WHERE `id`='{$pid}';");
	$exists = (mysql_result($find, 0) == '1')? true : false;
	$t = time();
	if ($present === false) {
		if ($exists === true) {
			$p = mysql_query("SELECT `price` FROM `db_offers` WHERE `prod_id`= '{$pid}' ORDER BY `price` DESC LIMIT 0,1;")or die(mysql_error());
			$p = mysql_fetch_assoc($p);
			mysql_query("INSERT INTO `db_watch` (`uid`,`pid`,`price`,`timestamp`) VALUES ('{$_u}','{$pid}','{$p['price']}','{$t}');")or die(mysql_error());
			if( mysql_insert_id() == 0) {
				$e[] = "&#10007; There was a problem adding the DVD to your watch list. Please try again or contact our <a href='mailto:help@companyname.co.uk?Subject=Problem%20using%20the%20watchlist.'>support team.</a>";
			}
			else {
				$st[] = "&#10003; The DVD has been added to your watch list.";
			}
		}
		else {
			$e[] = "&#10007; We could not find this DVD in our system. Please try again or contact our <a href='mailto:help@companyname.co.uk?Subject=Problem%20using%20the%20watchlist.'>support team.</a>";
		}
	}
	else {
		$e[] = "&#10007; This DVD is already in your watch list.";
	}
}
else if (isset($_GET['notify'])) {
	$pid = mysql_real_escape_string($_GET['notify']);
	if (!mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_notify` WHERE `pid` = '{$pid}' AND `uid` = {$_u};"),0)) {
		mysql_query("INSERT INTO `db_notify` (`uid`,`pid`,`email`) VALUES ('{$_u}','{$pid}','{$u['email']}');");
		if (mysql_affected_rows() > 0) {
			$st[] = "&#10003; You will be notified as soon as this product becomes available to sell.";
		}
		else
		{
			$e[] = "&#10007; Error adding this product to your notification list.";
		}
	}
	else
	{
		$e[] = "&#10007; You are already being notified about this product.";
	}
}
else if (isset($_GET['f'], $_GET['s'], $_GET['t'])) {
	$f = mysql_real_escape_string($_GET['f']);
	$s = mysql_real_escape_string($_GET['s']);
	$t = mysql_real_escape_string($_GET['t']);
	if(strlen($s) > 0 && strlen($f) > 0 && $t == "Mr" || $t == "Mrs" || $t == "Miss" || $t == "Ms" ) {
		mysql_query("UPDATE `db_users` SET `title` = '{$t}', `fname` = '{$f}', `sname` = '{$s}' WHERE `uid` = '{$_u}';")or die(mysql_error());
		$u = mysql_query("SELECT * FROM `db_users` WHERE `username` = '".mysql_real_escape_string($_SESSION['user'])."';") or die(mysql_error());
		$u = mysql_fetch_assoc($u);
		$_u = $u['uid'];
		$st[] = "&#10003; Your Datails have been successfully changed.";
	}
	else {
		$e[] = "&#10007; You must fill in all the fields.";
	}
}
else if (isset($_POST['np'],$_POST['cp'],$_POST['rp'])) {
	$newpass = mysql_real_escape_string(sha1(md5($_POST['np'])));
	$curpass = mysql_real_escape_string(sha1(md5($_POST['cp'])));
	$rptpass = mysql_real_escape_string(sha1(md5($_POST['rp'])));
	if ($newpass === $rptpass) {
		$q = mysql_query("SELECT COUNT(`uid`) FROM `db_users` WHERE `uid` = {$_u} AND `password` = '{$curpass}';") or die(mysql_error());
		if (mysql_result($q,0) == 1) {
			mysql_query("UPDATE `db_users` SET `password` = '{$newpass}' WHERE `uid` = '{$_u}';") or die(mysql_error());
			$st[] = "&#10003; Your Password has been successfully changed.";
		}
		else {
			$e[] = "&#10007; Your current password is incorrect!" or die(mysql_error());
		}
	}
	else {
		$e[] = "&#10007; Passwords do not match!";
	}
}

$no_query = mysql_query("SELECT COUNT(`id`) FROM `db_clicks` WHERE `uid` = '{$_u}'");
?>

<script>
	(function($){
			$(window).load(function(){
				$('#scrl-2').mCustomScrollbar({
					horizontalScroll:true,
					scrollButtons:{
						enable:true,
						scrollType:'pixels',
						scrollAmount:116
					}
				});
			});
			
		<?php
			if (mysql_result($no_query, 0) != '0') 	{
			echo "
			$(window).load(function(){
				$('#scrl').mCustomScrollbar({
					horizontalScroll:true,
					scrollButtons:{
						enable:true,
						scrollType:'pixels',
						scrollAmount:116
					}
				});
			});";
			}
		?>
		})(jQuery);
		window.onload = function(){
			setTimeout(function(){
			document.getElementById('ajax-loader-2').style.display = 'none';
			document.getElementById('scrl-wrap-2').style.visibility = 'visible';
			<?php
			if (mysql_result($no_query, 0) != '0') 	{
			echo "
			document.getElementById('ajax-loader').style.display = 'none';
			document.getElementById('scrl-wrap').style.visibility = 'visible';
			";
			}
			?>
			},350);
		}
</script>
<div class='bg-section'>
	<div class='width-reg'>
		<ul class='errorbox t' id='js-error' style='display:none;'>
		
		</ul>
	<?php
		if (empty($e) === false) {
			echo '<ul class="errorbox"><li>';
			echo implode("</li><li>", $e);
			echo '</li></ul><br>';
		}
		if (empty($st) === false) {
			echo '<ul class="successbox"><li>';
			echo implode("</li><li>", $st);
			echo '</li></ul>';
		}
		echo "<h1 class='headline' style='font-size:38px; margin-top:56px; text-align:left;'>Welcome {$u['fname']},</h1>";
	?>
	<div id='edit-details' class='emb t' style='display:none; padding:25px; width:330px; height:190px;margin-bottom:30px;'>
		<form id='edit-details-form' method='get' action='account.php'>
			<label style="margin-right:50px;">Title</label><label>First Name</label><br>
			<select name="t" class="login-select" style="width:80px;">
				<option>Mr</option>
				<option <?php if($u['title'] == "Mrs"){echo "selected='selected'";}?>>Mrs</option>
				<option <?php if($u['title'] == "Miss"){echo "selected='selected'";}?>>Miss</option>
				<option <?php if($u['title'] == "Ms"){echo "selected='selected'";}?>>Ms</option>
			</select>
			<input type="text" name='f' placeholder="forename" value="<?php echo $u['fname']; ?>" class="login-input" style="width:213px;"><br><br>
			<label style="margin-right:50px;">Surname</label>
			<input type="text" name='s' placeholder="surname" value="<?php echo $u['sname']; ?>" class="login-input" ><br><br>
			<div class='actions-list'>
				<div onclick='submitForm("edit-details-form");'>&#10003; Save Changes</div>
				<div onclick='document.getElementById("edit-details").style.display = "none";'>&#10007 Cancel</div>
			</div>
		</form>
	</div>
	
	<div id='change-pass' class='emb' style='display:none; padding:25px; width:330px; height:270px;margin-bottom:30px;'>
		<form id='change-pass-form' method='post' action='account.php'>
			<label style="margin-right:50px;">Current Password</label>
			<input type="password" id='cp-pass' name='cp' class="login-input" ><br><br>
			<label style="margin-right:50px;">New Password</label>
			<input type="password" id='np-pass' name='np' class="login-input" ><br><br>
			<label style="margin-right:50px;">Repeat New Password</label>
			<input type="password" id='rp-pass' name='rp' class="login-input" ><br><br>
			<div class='actions-list'>
				<div onclick='validateNewPass();'>&#10003; Save Changes</div>
				<div onclick='document.getElementById("change-pass").style.display = "none";'>&#10007 Cancel</div>
			</div>
		</form>
	</div>
	<div class='actions-list'>
		<div onclick='document.getElementById("edit-details").style.display = "block";'>Edit User Details</div><div  onclick='document.getElementById("change-pass").style.display = "block";'>Change Password</div>
	</div>
	<div style='clear:both;'></div>
	<?php
		if (mysql_result($no_query, 0) != '0') {
	?>
	<h1 class='headline' style='font-size:38px; margin-top:56px; text-align:left; text-decoration:underline;'>Recently Viewed</h1>
	<div id='scrl-wrap' style="avisibility:hidden;">
		<div class="mCustomScrollBox mCSB_horizontal" id="mCSB_1" style="position:relative; height:355px; overflow:hidden; max-width:100%;">
			<div id='scrl'>		
		<?php
		$r = mysql_query("SELECT * FROM `db_clicks` WHERE `uid` = '{$_u}'");
		
		while($row = mysql_fetch_assoc($r)) {
			
			$p = getProdInfo($row['pid']);
			$sup = getSupInfo($row['sid']);
			echo "<div class='dvd dvdinfo'><div class='dvd-inlay' style='float:left; background: url(\"img/product-imgs/{$row['pid']}.jpg\"); background-size:121px 174px;'></div>
				<div class='infodiv'>
				<h1 class='headline' style='font-size:18px; line-height:24px; max-height:24px; overflow:hidden; text-align:left; margin: 2px 0 10px 0; text-decoration:underline;'>{$p['name']}  [{$p['platform']}]</h1>
				<p class='text' style='margin:4px 0; text-align:left;'>Supplier Rating : <span style='font-size:14px;'>".starRating($sup['rating'])."</span></p>
				<p class='text' style='margin:4px 0; text-align:left;'>Rate Your Experience with </p>
				<div style='display:inline-block; margin: 6px 0 6px 4px; box-shadow: 0 1px 8px rgba(0, 0, 0, .3); padding: 0 10px;' class='offer-logo'>
					<img src='img/suppliers/{$sup['id']}.jpg'>
				</div>
				<div style='margin-top:4px;'>
					<p class='text' id='s-1-{$row['pid']}_{$sup['id']}' onmouseover=\"changeRating('s-1-{$row['pid']}_{$sup['id']}');\" style='margin:4px; text-align:left; display:inline; font-size:22px; cursor:pointer;'>★</p>
					<p class='text' id='s-2-{$row['pid']}_{$sup['id']}' onmouseover=\"changeRating('s-2-{$row['pid']}_{$sup['id']}');\" style='margin:4px; text-align:left; display:inline; font-size:22px; cursor:pointer;'>&#9734;</p>
					<p class='text' id='s-3-{$row['pid']}_{$sup['id']}' onmouseover=\"changeRating('s-3-{$row['pid']}_{$sup['id']}');\" style='margin:4px; text-align:left; display:inline; font-size:22px; cursor:pointer;'>&#9734;</p>
					<p class='text' id='s-4-{$row['pid']}_{$sup['id']}' onmouseover=\"changeRating('s-4-{$row['pid']}_{$sup['id']}');\" style='margin:4px; text-align:left; display:inline; font-size:22px; cursor:pointer;'>&#9734;</p>
					<p class='text' id='s-5-{$row['pid']}_{$sup['id']}' onmouseover=\"changeRating('s-5-{$row['pid']}_{$sup['id']}');\" style='margin:4px; text-align:left; display:inline; font-size:22px; cursor:pointer;'>&#9734;</p>
				</div>
				</div>
				<div style='clear:both;'></div>
				<div class='actions-list' style='margin-top:10px;'>
					<div style='width:31%; float:left;' onclick='goToProduct({$row['pid']});'><span style='line-height:12px; font-size:12px;'>£ Sell Now</span></div>
					<div style='width:31%; float:left;' onclick='rmFromList({$row['pid']},{$_u},this.parentNode.parentNode,\"clicks\",{$sup['id']});'><span style='line-height:12px; font-size:12px;'>&#10007; Remove</span></div>
					<div style='width:31%; float:left;' value='1' id='rate-{$row['pid']}-{$sup['id']}'  onclick='window.location = \"account.php?rate&s={$sup['id']}&r=\"+this.value;'><span style='line-height:12px; font-size:12px;'>★ Post Rating</span></div>
				</div>
			</div>";
		}
		
		?>
			
			</div>
		</div>
	</div>
	<br id='notifications'>
	<?php 
	echo "<img id='ajax-loader' style='margin-left:49%; position:relative; top:-250px;' src='img/ajax-load.gif'>";
	}
	echo "<h1 class='headline' style='font-size:38px; margin-top:-20px; text-align:left;'><span style='text-decoration:underline;'>Watch List</span><span>&nbsp;{$_n}</span></h1>";?>
	<div id='scrl-wrap-2' style="visibility:hidden;">
			<div id='scrl-2'>		
		<?php
		$c = 0;
		$r2 = mysql_query("SELECT * FROM `db_watch` WHERE `uid` = '{$_u}'");
		while($row2 = mysql_fetch_assoc($r2)) {
			$c++;
			$p = getProdInfo($row2['pid']);
			$s = mysql_query("SELECT COUNT(`id`) FROM `db_offers` WHERE `prod_id`='{$row2['pid']}'");
			$n = (mysql_result($s, 0));
			$offers = (mysql_result($s, 0) == '0'? true : false);
			$m = mysql_query("SELECT * FROM `db_offers` WHERE `prod_id` = '{$row2['pid']}' ORDER BY `price` DESC LIMIT 0,1;") or die(mysql_error());
			$m = mysql_fetch_assoc($m);
			$maxpop = mysql_result(mysql_query("SELECT MAX(`popularity`) FROM `db_list`;"),0);
			if ($offers === false) {
				
				$maxprice = $m['price'];
				$diff = $row2['price'] - $maxprice;
				$maxprice = "{$n} Offers, £".number_format($m['price'],2);
				if($diff == 0) {
					$diff = "No Change in Price.";
				}
				else {
					if ($diff > 0) {
						$diff = "Price Decreased By : <span style='color:#FF6A5F; font-size:14px;'>£".(number_format(abs($diff),2))." &#8615;</span>";
					}
					else {
						$diff = "Price Increased By : <span style='color:#9DFF71; font-size:14px;'>£".(number_format(abs($diff),2))." &#8613;</span>";
					}
					mysql_query("");
				}
			}
			else {
				$maxprice = "No Offers";
				$diff = "Price Change: None";
			}
			echo "<div class='dvd dvdinfo'><div class='dvd-inlay' style='float:left; background: url(\"img/product-imgs/{$row2['pid']}.jpg\"); background-size:121px 174px;'></div>
				<div class='infodiv'>
				<h1 class='text' style='font-size:18px; line-height:24px; overflow:hidden; max-height:48px; text-align:left; margin: 2px 0 10px 0; text-decoration:underline;'>{$p['name']} [{$p['platform']}]</h1>
				<p class='text' style='margin:4px 0; text-align:left;'>Price on (".date("d/m/y",$row2['timestamp']).") : £".number_format($row2['price'],2)."</p>
				<p class='text' style='margin:4px 0; text-align:left;'>Current Price : {$maxprice}</p>
				<p class='text' style='margin:4px 0; text-align:left;'>{$diff}</p>
				<p class='text' style='margin:4px 0; text-align:left;'>Rating : <span style='font-size:14px;'>".starRating($p['rating'])."</span></p>
				<p class='text' style='margin:4px 0; text-align:left;'>Popularity : ".round((($p['popularity'] / $maxpop)*100),1)."%</p>
				</div>
				<div style='clear:both;'></div>
				<div class='actions-list' style='margin-top:10px;'>
					<div style='width:48%; float:left;' onclick='goToProduct({$row2['pid']});'><span style='line-height:12px; font-size:12px;'>£ Sell Now</span></div>
					<div style='width:48%; float:left;' onclick='rmFromList({$row2['pid']},{$_u},this.parentNode.parentNode,\"watch\");'><span style='line-height:12px; font-size:12px;'>&#10007; Remove From List</span></div>
				</div>
			</div>";
			
		}
		if ($c == 0) {
			echo "<div style='margin-left: 275px; text-align: center; width: 446px; height: 69px;'><p style='font-size:12px; margin-top: 95px; color:white; text-shadow:0 1px rgba(0,0,0,.5);'>&#10007 You haven't added any DVDs to your Watch List.</p>";
			echo "<form action='search.php' method='get'><input type='text' style='height:26px;' name='q' placeholder='why not search for some?' class='login-input' >
			<input type='submit' style='margin:0; height: 32px; font-weight: 800; cursor: pointer;border: 1px solid #0965B9;' value='search' class='blue-button'></form></div>";
		}
		?>
			</div>
		</div>
	</div>
	<img id='ajax-loader-2' style='margin-left:49%; position:relative; top:-250px;' src='img/ajax-load.gif'>
	
	</div>
</div>
<?php
include_once "include/footer.php";
?>
