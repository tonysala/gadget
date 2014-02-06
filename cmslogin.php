<?php
include_once 'include/cmsheader.php';

if(isset($_POST['cmsuser'], $_POST['cmspass']))
{
	if(empty($_POST['cmsuser']))
	{
		$e[] = "&#10007; Please enter a username.";
	}
	if(empty($_POST['cmspass']))
	{
		$e[] = "&#10007; Please enter a password.";
	}
	if(empty($e))
	{
		if(cms_validate_login($_POST['cmsuser'], sha1(md5($_POST['cmspass']))) === false)
		{
			$e[] = "&#10007; Invalid login details.";
		}
		else
		{
			if(isset($_POST['cmsremember']))
			{
				setcookie("cmsusername", htmlentities($_POST['cmsuser']), time()+60*60*24*365*20);
				setcookie("cmspassword", sha1(md5($_POST['cmspass'])), time()+60*60*24*365*20);
			}
			$_SESSION['cmsuser'] = htmlentities($_POST['cmsuser']);
			header("Location: cms.php");
			exit();
		}
	}
}
?>
<div class="bg-section">
	<div class='width-reg'>
	<h1 class="headline">CMS Log In</h1>
	<?php
	if (empty($e) === false)
	{
		echo '<ul class="errorbox"><li>';
		echo implode("</li><li>", $e);
		echo '</li></ul>';
	}
	?><form method="post" style="width:500px; text-align:center; margin:0 auto;">
	<br>
	<label>Username</label><br>
	<input type="text" placeholder="username" class="login-input" name="cmsuser" value="<?php if(isset($_POST['cms_user'])){ echo htmlentities($_POST['cms_user']);}?>"></input><br><br>
	<label>Password</label><br>
	<input type="password" placeholder="password" name="cmspass" class="login-input"></input><br><br>
	<div id="remember_wrap">
		<input type="checkbox" name="cmsremember"></input>
		<label for="remember">Remember Me?</label>
	</div>
	<br>
	<input type="submit" value="Login" class="login-button"></input>
	<br>
</div>
<?php
include_once 'include/cmsfooter.php';
?>
