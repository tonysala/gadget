<?php
include_once "include/header.php";
$product = mysql_query("SELECT * FROM `db_offers` WHERE `price`=(SELECT MAX(`price`) FROM `db_offers`) LIMIT 0,1;");
$product = mysql_fetch_array($product);
$prod_info = mysql_query("SELECT * FROM `db_list` WHERE `id` = '{$product['prod_id']}';");
$prod_info = mysql_fetch_array($prod_info);
$p = mysql_query("SELECT * FROM `db_list` WHERE `id` = '{$product['prod_id']}';") or die(mysql_error());
$p = mysql_fetch_assoc($p);
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
				if (mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_offers`"),0) > 0) {
				echo "<div id='main-product' class='dvd-inlay' style='margin:50px; background:url(img/product-imgs/{$product['prod_id']}.jpg);background-size:121px 174px;'></div>";
				echo "<div id='product-info'>
				<h1 class='headline' style='font-size:38px; margin: 55px 0 25px 0; text-align:left;'>{$prod_info['name']} [{$prod_info['platform']}] [{$prod_info['year']}]</h1>
				<div id='product-desc'>
					<p class='text' style='font-size:14px; margin: 20px 0 30px 0; text-align:left;'>{$desc}</p>
				</div>
				<div class='actions-list'>";
					switch($p['category'])
					{
						case 1:
							echo "<a href='http://imdb.com/title/".urlencode($prod_info['imdb_id'])."'><div>View on IMDB &#8599;</div></a>";
							break;
						case 2:
							echo "<a href='http://www.amazon.co.uk/s/&field-keywords=".urlencode($prod_info['name'])."+".urlencode($prod_info['author'])."'><div>View on Amazon &#8599;</div></a>";
							break;
						case 3:
							echo "<a href='http://ign.com/search?q=".urlencode($prod_info['name'])."&filter=games'><div>View on IGN &#8599;</div></a>";
							break;
					}
					echo "
					<a href='account.php?watch={$p['id']}'><div>Watch Price &#8599;</div></a>
					<a href='product.php?id={$p['id']}'><div>".noOffers($p['id'])."</div></a>
					<div>Rating: {$p['rating']} &#9733;</div>
				</div>
				<br><div style='clear:both;'>
				<a href='product.php?id={$p['id']}'>
					<div class='sell-btn' style='width: 400px!important; padding:0 1%; overflow: visible!important; margin-top:56px;'>
						<p style='display:inline-block; text-align: left; padding-right: 1%; white-space: nowrap; margin: 0; float: left;'>Sell </p>
						<span style='max-width: 100px; height: inherit; line-height: inherit; float: left; white-space: nowrap; text-overflow: ellipsis; display: inline-block; text-align: left; overflow: hidden;'>\"{$prod_info['name']}</span>
						<p style='display:inline-block; text-align: left; white-space: nowrap; margin: 0; float: left;'>\" for £{$product['price']} through </p>
						<div style='float:right; margin: 0 10px;bottom:9px; position:relative;display:inline-block; padding: 0 10px;' class='offer-logo'>
							<img style='float: left; max-width: 100px; margin-top:5px;' height:80px; src='img/suppliers/{$product['supplier_id']}.jpg'>
						</div>
					</div>
				</a>
				</div></div><br>";
			?>
			<div style='clear:both;'></div>
			<?php
				echo "<h1 class='headline' style='font-size:38px; margin-top:56px; text-align:left;'>Next 30 Most Valuable</h1>";
				$ps = mysql_query("SELECT DISTINCT `prod_id` , MAX( `price` ) AS `m` FROM `db_offers` GROUP BY `prod_id` ORDER BY MAX( `price` ) DESC LIMIT 1 , 30;") or die(mysql_error());
				while($r = mysql_fetch_assoc($ps))
				{
					echo "<div class='dvd'><div class='dvd-inlay' style='background: url(\"img/product-imgs/{$r['prod_id']}.jpg\"); background-size:121px 174px;'></div>";
					echo "<div class='sell-btn' style='width:92%; margin-top:18px;' onclick='goToProduct({$r['prod_id']});'>From £{$r['m']}</div></div>";
				}				
			?>
			<div style='clear:both;'></div>
		</div>
	</div>
</div>
<?php
} else {
	echo "<p class='errorbox' style='margin:10% auto;'>&#10007; No Offers Right Now.</p></div></div></div>";
}
	include_once "include/footer.php";
?>

