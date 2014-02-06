<?php
	include_once "include/header.php";
?>
<div class='bg-section'>
	<h1 class='headline'>Reset Password</h1>
	<div class='width-reg'>
		<ul id="js-error" style='display:none;' class="errorbox"></ul>
	<?php
	if (isset($_GET['token'],$_GET['username']) || isset($_POST['rp'],$_POST['np'],$_POST['username']) || isset($_GET['forgot']) || isset($_POST['user']))
	{
		if (isset($_GET['token']))
		{
			$user = $_GET['username'];
			$match = mysql_query("SELECT COUNT(`uid`) FROM `db_users` WHERE `username` = '{$user}';") or die(mysql_error());
			$match = mysql_result($match,0);
			if ($match === 0)
			{
				$e[] = "&#10007; Username does not exist!";
				if (empty($st) === false)
				{
					echo '<ul class="successbox"><li>';
					echo implode("</li><li>", $st);
					echo '</li></ul>';
				}
				if (empty($e) === false)
				{
					echo '<ul  class="errorbox"><li>';
					echo implode("</li><li>", $e);
					echo '</li></ul>';
				}
				echo "	</div><div style='clear:both;'></div></div></div>";
				include_once "include/footer.php";
				exit;
			}
			$f = "resets/{$user}.txt";
			if (file_exists($f) && filesize($f) > 0)
			{
				$fh = fopen($f, 'r');
			}
			else
			{
				$e[] = "&#10007; Reset token cannot be matched, Please try again!";
				if (empty($st) === false)
				{
					echo '<ul class="successbox"><li>';
					echo implode("</li><li>", $st);
					echo '</li></ul>';
				}
				if (empty($e) === false)
				{
					echo '<ul  class="errorbox"><li>';
					echo implode("</li><li>", $e);
					echo '</li></ul>';
				}
				echo "	</div><div style='clear:both;'></div></div></div>";
				include_once "include/footer.php";
				exit;
			}
			$fc = fread($fh,filesize($f));
			fclose($fh);
			$vars = explode("#",$fc);
			if ($vars[1] > time() && $_GET['token'] !== $vars[0])
			{
				echo "<label style='font-size:19px;margin-left:406px;position:relative;top:25px;'>Reset Your Password.</label>
				<div id='change-pass' class='emb' style='padding:25px; width:330px; height:200px; margin:30px auto;'>
					<form id='change-pass-form' method='post' action='reset.php'>
						<label style='margin-right:50px;'>New Password</label>
						<input type='password' id='np-pass' name='np' class='login-input'><br><br>
						<label style='margin-right:50px;'>Repeat New Password</label>
						<input type='password' id='rp-pass' name='rp' class='login-input'><br><br>
						<input type='hidden' name='username' value='{$user}'>
						<div class='actions-list'>
							<div onclick='validateNewPass();'>&#10003; Save Changes</div>
						</div>
					</form>
				</div>";
			}
			else if ($vars[1] < time())
			{
				$e[] = "&#10007; Token has expired.";
			}
			else
			{
				$e[] = "&#10007; Invalid Token!";
			}
		}
		else if (isset($_POST['np']))
		{
			$u = mysql_real_escape_string($_POST['username']);
			$newpass = mysql_real_escape_string(sha1(md5($_POST['np'])));
			$rptpass = mysql_real_escape_string(sha1(md5($_POST['rp'])));
			if ($newpass === $rptpass)
			{
				mysql_query("UPDATE `db_users` SET `password` = '{$newpass}' WHERE `uid` = '{$u}';") or die(mysql_error());
				$st[] = "&#10003; Your Password has been successfully changed.";
				echo "<p class='text'>You are being redirected, Please wait...</p>
				<img id='ajax-loader' style='margin-left:49%; display:block; position:relative; top:5px;' src='img/ajax-load.gif'><br>
				<script>setTimeout(function(){ window.location = 'login.php';}, 3000);</script>";
			}
			else
			{
				$e[] = "&#10007; Passwords do not match!";
			}
		}
		else if (isset($_POST['user']))
		{
			$user = mysql_real_escape_string($_POST['user']);
			$match = mysql_result(mysql_query("SELECT COUNT(`uid`) FROM `db_users` WHERE `username` = '{$user}';"),0);
			if ($match != 0)
			{
				$user_details = mysql_query("SELECT `fname`,`email` FROM `db_users` WHERE `username` = '{$user}';");
				$user_details = mysql_fetch_assoc($user_details);
				send_mail($user_details['email'],$user,$user_details['fname'],"resetpass");
				$st[] = "&#10003; A password reset email has been sent";
				echo "<p class='text'>You are being redirected, Please wait...</p>
				<img id='ajax-loader' style='margin-left:49%; display:block; position:relative; top:5px;' src='img/ajax-load.gif'><br>
				<script>setTimeout(function(){ window.location = 'login.php';}, 3000);</script>";
			}
			else
			{
				$e[] = "&#10007; This username is not recognized.";
				echo "<label style='font-size:19px;margin-left:410px;position:relative;top:25px;'>Recover your Account.</label>
				<div class='emb' style='padding:25px; width:330px; height:120px; margin:30px auto;'>
				<form method='post' action='reset.php'>
					<label style='margin-right:50px;'>Username</label>
					<input type='text' name='user' class='login-input'><br><br>
					<div class='actions-list'>
						<input type='submit' value='&#10003; Recover Password'>
					</div>
				</form></div>";
			}
		}
		else
		{
			echo "<label style='font-size:19px;margin-left:410px;position:relative;top:25px;'>Recover your Account.</label>
			<div class='emb' style='padding:25px; width:330px; height:120px; margin:30px auto;'>
			<form method='post' action='reset.php'>
				<label style='margin-right:50px;'>Username</label>
				<input type='text' name='user' class='login-input'><br><br>
				<div class='actions-list'>
					<input type='submit' value='&#10003; Recover Password'>
				</div>
			</form></div>";
		}
	}
	else
	{
		$e[] = "&#10007; Invalid Link!";
	}
	if (empty($st) === false)
	{
		echo '<ul class="successbox"><li>';
		echo implode("</li><li>", $st);
		echo '</li></ul>';
	}
	if (empty($e) === false)
	{
		echo '<ul  class="errorbox"><li>';
		echo implode("</li><li>", $e);
		echo '</li></ul>';
	}
?>
	</div>
	<div style='clear:both;'></div>
	</div>
</div>
<?php
	include_once "include/footer.php";
?>
