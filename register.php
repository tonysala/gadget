<?php
include_once 'include/header.php';
if(isset($_POST['user'], $_POST['pass'], $_POST['rpt-pass'])) {
	if(empty($_POST['user'])) {
		$e[] = "&#10007; Please enter a username.";
	}
	elseif(strlen($_POST['user']) > 20) {
		$e[] = "&#10007; Your username cannot exceed 20 characters.";
	}
	elseif(username_available($_POST['user']) === false) {
		$e[] = "&#10007; This username is already in use.";
	}
	elseif(strlen($_POST['user']) < 6) {
		$e[] = "&#10007; Your username must be more than 6 characters.";
	}
	if(empty($_POST['pass'])) {
		$e[] = "&#10007; Please enter a password.";
	}
	elseif($_POST['pass'] !== $_POST['rpt-pass']) {
		$e[] = "&#10007; Passwords do not match.";
	}
	if(empty($_POST['fname'])) {
		$e[] = "&#10007; Please enter an first name.";
	}
	if(empty($_POST['sname'])) {
		$e[] = "&#10007; Please enter an second name.";
	}
	if(empty($_POST['email'])) {
		$e[] = "&#10007; Please enter an email address.";
	}
	
	if(empty($e)) {
		add_user($_POST['user'],$_POST['pass'],$_POST['title'],$_POST['fname'],$_POST['sname'],$_POST['email']);
		if (isset($_GET['next'])) {
			header("Location: login.php?first_reg&next=".urlencode($_GET['next']));
		exit;
		}
		header("Location: login.php?first_reg");
		exit;
	}
}
?>
<div class="bg-section">
	<div style="width:650px; margin:auto;">
		<h1 class="headline">Register</h1>
		<h1 class="headline" style="font-size:20px;">
		Registering an account with <span style='font-style:italic;'>[website name]</span> allows you to keep track of the DVDs you've sold as well as keeping an eye on the current price of DVDs that you've added to your "watch" list.
		</h1><br>
		<?php
		if (empty($e) === false)
		{
			echo '<ul class="errorbox"><li>';
			echo implode("</li><li>", $e);
			echo '</li></ul>';
		}
		?>
	</div>
	<form method="post" class="emb_form" action="" style="width:350px; margin:auto;">
		<br>
		<div>
			<label style="margin-right:50px;">Title</label><label>First Name</label><br>
			<select name="title" class="login-select" style="width:80px;">
				<option>Mr</option>
				<option <?php if($_POST['title'] == "Mrs"){echo "selected='selected'";}?>>Mrs</option>
				<option <?php if($_POST['title'] == "Miss"){echo "selected='selected'";}?>>Miss</option>
				<option <?php if($_POST['title'] == "Mrs"){echo "selected='selected'";}?>>Ms</option>
			</select>
			<input type="text" name="fname" value="<?php if(isset($_POST['fname'])){echo $_POST['fname'];}?>" class="login-input" style="width:213px;"></input><br><br>
			<label>Surname</label><br>
			<input type="text" name="sname" value="<?php if(isset($_POST['sname'])){echo $_POST['sname'];}?>" class="login-input" ></input><br><br>
			<label>Username</label><br>
			<input type="text" name="user" value="<?php if(isset($_POST['user'])){echo $_POST['user'];}?>" class="login-input"></input><br><br>
			<label>Password</label><br>
			<input type="password" name="pass" class="login-input"></input><br><br>
			<label>Repeat Password</label><br>
			<input type="password" name="rpt-pass" class="login-input"></input><br><br>
			<label>Email</label><br>
			<input type="email" name="email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];}?>" class="login-input"></input><br><br>
			<br>
		</div>
		<input type="submit" value="Register" class="login-button"></input>
		<br><label>Or, if you've already registered <a href="login.php<?php if (isset($_GET['next'])){echo "?next=".urlencode($_GET['next']);}?>">Login.</a></label>
	</form>
	<div style="clear:both"></div>
</div>
<?php
include_once 'include/footer.php';
?>
