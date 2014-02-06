<?php
	if (isset($_GET['download'],$_GET['fn']))
	{	
		if (isset($_GET['download'],$_GET['fn']))
		{
		    $file = $_GET['fn'];
			
			if(!file_exists($_GET['fn']))
			{
				die('File not found.');
			}
		    header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=template.csv");
			header("Content-Type: text/csv");
			header("Content-Transfer-Encoding: binary");
			readfile($file);
			exit;
		}
	}

	include_once "include/header.php";
		
?>
<div class='bg-section'>
	<h1 class='headline'>CSV Template</h1>
	<div class='width-reg'>
		<style>
		@font-face 
		{
			font-family: 'Monaco';
			font-style: normal;
			font-weight: 400;
			src: local('Monaco'), url(../fonts/Monaco.ttf) format('ttf');
		}
		pre , pre *
		{
			text-align: left;
			line-height: 18px;
			font-family: Monaco, Andale Mono, Courier New, monospace;
			font-size: 12px;
			color: white;
			text-shadow: 0 1px rgba(0,0,0,.6);
		}
		.code
		{
			background: #4E4E4E;
			float:left;
			border: 1px solid #333;
			padding: 10px 28px;
			border-radius: 6px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, .4),inset 0 1px rgba(255, 255, 255, .2);
		}
		input
		{
			text-shadow:0 0;
		}
		</style>
		<div class='code' style='height:400px;'>
			<pre><u>CSV EXAMPLE FORMAT</u><br>
db_id,price,url,imdb_id
00002,4.50,http://...,tt1074638
00003,4.50,http://...,tt0454876
00004,4.50,http://...,tt1637725
00008,4.50,http://...,tt0325980
00006,4.50,http://...,tt1411697
00009,4.50,http://...,tt0120338
00012,4.50,http://...,tt0116683
00013,4.50,http://...,tt0099685
00018,4.50,http://...,tt0080684
00017,4.50,http://...,tt0076759
00016,4.50,http://...,tt0137523
00019,4.50,http://...,tt0475276
00020,4.50,http://...,tt0468569
00021,4.50,http://...,tt1375666
00022,4.50,http://...,tt0234215
00023,4.50,http://...,tt0133093
00024,4.50,http://...,tt0242653
00025,4.50,http://...,tt0367882</pre>
		</div>
		<div class='code' style='height:300px; float:right; width:660px;'>
		
		<?php
		if (isset($_GET['prepare']))
		{
			if (!isset($_SESSION['csv_limit']) || $_SESSION['csv_limit'] > time())
			{
				$base_url = "http://";
				$base_price = 0.00;
				if (isset($_POST['baseprice']))
				{
					if (is_numeric($_POST['baseprice']))
					$base_price = $_POST['baseprice'];
				}
				if (isset($_POST['baseurl']))
				{
					if (strstr($_POST['baseurl'],"http://") !== false || strstr($_POST['baseurl'],"https://") !== false)
					{
						$base_url = $_POST['baseprice'];
					}
				}
				$_SESSION['csv_limit'] = time()+3600;
				$maxlen = strlen(mysql_result(mysql_query("SELECT MAX(`id`) FROM `db_list`"),0));
				$dvds = mysql_query("SELECT * FROM `db_list`");
				$fo = "";
				$c = 0;
				$fo .= "db_id,price,url,imdb_id\n";
				while($r = mysql_fetch_assoc($dvds)) {
					$c++;
					$id = str_pad($r['id'],$maxlen,"0",STR_PAD_LEFT);					
					if (isset($base_url,$base_price)) {
						$fo .= $id.",{$base_price},{$base_url},".$r['imdb_id']."\n";
					}
					else {
						$fo .= $id.",[~PRICE~],[~URL~],".$r['imdb_id']."\n";
					}
				}				
				$mt = explode(" ",microtime());
				$mt = str_replace(".","",($mt[0] + $mt[1]));
				$f = getcwd()."/csv/template/".$mt.".csv";
				file_put_contents($f,$fo);
				echo "<a class='blue-button' href='recyclers.php?download&fn={$f}' style='width: 175px;'>Download CSV Template</a>";
			}
			else
			{
				echo "<div class='errorbox'>Request can only be made every 60 minutes.</div>";
			}
		}
		else
		{
			echo "<pre style='margin:0;'>
			<form action='' method='get'>
			<label style='float: left;margin: 4px;'>Base Price (0.00 By Default)</label><br>
			<input type='text' name='baseprice' value='' style='float: left;' class='login-input'></input><br>
			<input type='hidden' name='prepare'>
			<label style='float: left;margin: 4px;'>Base URL (e.g http://www.example.com)</label><br>
			<input type='text' name='baseurl' value='' style='float: left;' class='login-input'></input><br>
			<input type='submit' class='blue-button' style='width: 210px;' value='Prepare Custom CSV file.'></form></pre>";	
		}
		?>
		</div>
		<div style='clear:both;'></div>
	</div>
</div>
<?php
	include_once "include/footer.php";
?>
