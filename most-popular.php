<?php
include_once "include/header.php";
$p = mysql_query("SELECT * FROM `db_list` WHERE `rating`=(SELECT MAX(`rating`) FROM `db_list`) ORDER BY `name` LIMIT 0,1;");
$p = mysql_fetch_array($p);
if($json = @file_get_contents("http://imdbapi.org/?id=".urlencode($p['imdb_id'])."&plot=full"))
{
	$data = json_decode($json);
	$desc = $data->{'plot'};
}
else
{
	$desc = "&#10007 No Description Available.";
}
?>
<div class='bg-section'>
	<div class='width-reg'>
		<div id='product'>
			<?php
				$maxpop = mysql_result(mysql_query("SELECT MAX(`popularity`) FROM `db_list`;"),0);
				echo "<div id='main-product' class='dvd-inlay' style='margin:50px; background:url(img/product-imgs/{$p['id']}.jpg);background-size:121px 174px;'></div>";
				echo "<div id='product-info'>
				<h1 class='headline' style='font-size:38px; margin: 55px 0 25px 0; text-align:left;'>{$p['name']} ({$p['year']})</h1>
				<div id='product-desc'>
					<p class='text' style='font-size:14px; margin: 20px 0 30px 0; text-align:left;'>{$desc}</p>
				</div>
				<div class='actions-list'>
					<a href='http://imdb.com/title/{$p['imdb_id']}'><div>View on IMDB &#8599;</div></a>
					<a href='account.php?watch={$p['id']}'><div>Watch Price &#8599;</div></a>
					<a href='product.php?id={$p['id']}'><div>".noOffers($p['id'])."</div></a>
					<div>Rating: {$p['rating']} &#9733;</div>
				</div><br style='clear:both'>
				<div class='emb' style='margin: 16px 0 16px; width: 406px;padding: 16px;height: 95px;'>
					<h1 class='headline' style='line-height:24px; font-size:22px; margin:15px 0; text-align:left;'>
					Current Rating : {$p['rating']} / 5.00 &#9733;&#9733;&#9733;&#9733;&#9733; </h1>
					<h1 class='headline' style='line-height:24px; font-size:22px; margin:15px 0; text-align:left;'>
					Popularity : ".round((($p['popularity'] / $maxpop)*100),0)."%</h1>
				</div>
				<a href='product.php?id={$p['id']}'>
					<div style='width: initial;' class='sell-btn'>Sell {$p['name']} &#8599;</div>
				</a>
				
				</div>";
			?>
			<div style='clear:both;'></div>
			<?php
				echo "<h1 class='headline' style='font-size:38px; margin-top:56px; text-align:left;'>Next 30 Most Popular</h1>";
				$ps = mysql_query("SELECT * FROM `db_list` ORDER BY `rating` DESC, `name` LIMIT 1,30;") or die(mysql_error());
				while ($r = mysql_fetch_array($ps))
				{
					echo "<div class='dvd'><div class='dvd-inlay' style='background: url(\"img/product-imgs/{$r['id']}.jpg\"); background-size:121px 174px;'></div>";
					echo "<div class='sell-btn' style='width:92%; margin-top:18px;' onclick='goToProduct({$r['id']});'>Sell Now {$r['rating']} &#9733;</div></div>";
				}
			?>
			<div style='clear:both;'></div>
		</div>
	</div>
</div>
<?php
	include_once "include/footer.php";
?>

