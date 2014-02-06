<?php
include_once 'include/header.php';
if(isset($_POST['user'], $_POST['pass']))
{
	if(empty($_POST['user']))
	{
		$e[] = "&#10007; Please enter a username.";
	}
	if(empty($_POST['pass']))
	{
		$e[] = "&#10007; Please enter a password.";
	}
	if(empty($e))
	{
		if(validate_login($_POST['user'], sha1(md5($_POST['pass']))) === false) {
			$e[] = "&#10007; Invalid login details.";
		}
		else {
			if(isset($_POST['remember'])) {
				setcookie("username", htmlentities($_POST['user']), time()+60*60*24*365*20);
				setcookie("password", sha1(md5($_POST['pass'])), time()+60*60*24*365*20);
			}
			$_SESSION['user'] = htmlentities($_POST['user']);
			if (isset($_GET['next'])) {
				header("Location: {$_GET['next']}");
				exit;
			}
			header("Location: account.php");
			exit;
		}
	}
}
if (isset($_GET['logout']))
{
	$st[] = "&#10003; You have successfully logged out.";
}
if (isset($_GET['first_reg']))
{
	$st[] =  "&#10003; You have successfully registered. Login using the form below.";
}
?>
<div class="bg-section">
	<h1 class="headline">Log In</h1>
	<?php
	if (empty($e) === false)
	{
		echo '<ul class="errorbox"><li>';
		echo implode("</li><li>", $e);
		echo '</li></ul>';
	}
	if (empty($st) === false)
	{
		echo '<ul class="successbox"><li>';
		echo implode("</li><li>", $st);
		echo '</li></ul>';
	}
	?>
	<form method="post" style="width:500px; text-align:center; margin:0 auto;">
		<br>
		<label>Username</label><br>
		<input type="text" name="user" value="<?php if(isset($_POST['user'])){ echo htmlentities($_POST['user']);}?>" class="login-input"></input><br><br>
		<label>Password</label><br>
		<input type="password" name="pass" class="login-input"></input><br><br>
		<div id="remember_wrap">
			<label for="remember">Remember Me?</label>
			<input type="checkbox" name="remember"></input>
		</div>
		<br>
		<input type="submit" value="Login" class="login-button"></input>
		<br><br><div style='padding:10px; height:50px;' class='emb'><label>Or, if you've not registered, <a href="register.php<?php if (isset($_GET['next'])){echo "?next=".urlencode($_GET['next']);}?>">Register.</a></label>
		<br><label style='font-size: 12px;'>Forgotten your password? Click <a href="reset.php?forgot">Here.</a></label>
		</div><br>
	</form>
</div>
<?php
include_once 'include/footer.php';
?>
