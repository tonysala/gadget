<?php
include_once "include/header.php";
$sort = "price";

function getURL($pid,$sid,$url) {
	$redirect = "redirect.php?pid=".urlencode($pid)."&sid=".urlencode($sid)."&url=".urlencode($url);
	return $redirect;
}
function getRating($sid) {
	$s = mysql_query("SELECT `rating` FROM `db_supplier` WHERE `id` = '{$sid}'") or die (mysql_error());
	$s = mysql_fetch_assoc($s);
	return $s['rating'];
}
inc_pop($p_id,2);
$query = mysql_query("SELECT COUNT(`id`) AS c,`imdb_id`,`name`,`year` FROM `db_list` WHERE `id` = '{$p_id}'");
$query = mysql_fetch_assoc($query);
if($query['c'] != '0') {
	$i_url =  ($query['imdb_id'] !== "" ? "http://imdbapi.org/?id=".urlencode($p['imdb_id'])."&plot=full" : "http://imdbapi.org/?title=".urlencode($p['name'])."&year=".urlencode($p['year'])."&plot=full");
	if(isset($p['imdb_id']) && $json = @file_get_contents($i_url)) {
		$data = json_decode($json);
		$data = ($query['imdb_id'] !== "" ? $data : $data[0]);
		$desc = $data->{'plot'};
	}
	else {
		$desc = "&#10007; No Description Available.";
	}
}
else {
	$e[] = "<p>&#10007; This DVD no longer exists in our system. <br> <a href='javascript:history.go(-1)'>Go Back</a></p>";
}
if(!isset($_GET['sort'])) {
	$_GET['sort'] = 1;
}
?>
<div class='bg-section'>
	<div class='width-reg'>
		<div id='product'>
			<?php
				if (empty($st) === false) {
					echo '<ul class="successbox"><li>';
					echo implode("</li><li>", $st);
					echo '</li></ul>';
				}
				if (empty($e) === false) {
					echo '<ul class="errorbox"><li>';
					echo implode("</li><li>", $e);
					echo '</li></ul>';
					echo "<h1 class='headline' style='font-size:200px; margin:180px;'>404</h1>";
				}
				else {
					echo "<div id='main-product' class='dvd-inlay' style='margin:50px; background:url(img/product-imgs/{$p_id}.jpg);background-size:121px 174px;'></div>";
					echo "<div id='product-info'>
					<h1 class='headline' style='font-size:38px; margin: 55px 0 25px 0; text-align:left;'>{$p['name']} [{$p['platform']}] [{$p['year']}]</h1>
					<div id='product-desc'>
						<p class='text' style='font-size:14px; margin: 20px 0 30px 0; text-align:left;'>{$desc}</p>
					</div><div class='actions-list'>";
					$types = "";
					switch($p['category']) {
						case 1:
							echo "<a href='http://imdb.com/title/".urlencode($p['imdb_id'])."'><div>View on IMDB &#8599;</div></a>";
							$types = "";
							break;
						case 2:
							echo "<a href='http://www.amazon.co.uk/s/&field-keywords=".$p['name']."+".$p['author']."'><div>View on Amazon &#8599;</div></a>";
							$types = "";
							break;
						case 3:
							echo "<a href='http://ign.com/search?q=".$p['name']."&filter=games'><div>View on IGN &#8599;</div></a>";
							break;
					}
					echo "<a href='account.php?watch={$p_id}'><div>Watch Price &#8599;</div></a>
					<div>".noOffers($p['id'])."</div>
					<div>Rating: {$p['rating']} &#9733;</div>
					</div>
					<br></div><div style='clear:both;'></div><br>";
			?>
		</div>
		<div id='suppliers-list' style='text-align:center; font-size:12px; line-height:18px; color:white; text-shadow:0 1px rgba(0,0,0,.5);'>
				<?php
					$c = 0;
					switch($_GET['sort']) {
						default:
							$results = mysql_query("SELECT * FROM `db_offers` WHERE `prod_id` = '{$p_id}' ORDER BY `price` DESC") or die(mysql_error());
							while($r = mysql_fetch_assoc($results)) {
								$c++;
								if ($c == 1) {
									echo 
									"<ul>
										<li id='offer-nav'>
											<ul>
												<li id='oli1'>Title</li>
												<li id='oli2'>Price(Used) &#9660;</li>
												<li id='oli3'>Recycler</li>
												<li id='oli4'>Recycler Rating</li>
												<li onclick='showDropWindow(\"sort-select\");' class='cmb'>Sort &#x25BC;</li>
											</ul>
										</li>";
								}
								echo 
								"
								<li class='offer'>
									<div class='search-prev' style='background:url(img/product-imgs/{$r['prod_id']}.jpg); background-size:32px 51px;'></div>
									<p style='width:300px;'>{$p['name']}</p>
									<p style='width:120px;'>£{$r['price']}</p>
									<div class='offer-logo'><img src='img/suppliers/{$r['supplier_id']}.jpg'></div>
									<a href='".getURL($r['prod_id'], $r['supplier_id'], $r['url'])."'>Sell</a>
									<div class='review-rating stars-".roundRating(getRating($r['supplier_id']))."'></div>
								</li>";
							}
							break;
						case 2:
							$res2 = mysql_query("SELECT `id` FROM `db_supplier` ORDER BY `rating` DESC;");
							while($r2 = mysql_fetch_assoc($res2)) {
								$res = mysql_query("SELECT * FROM `db_offers` WHERE `prod_id` = '{$p_id}' AND `supplier_id` = '{$r2['id']}' ORDER BY `price` DESC") or die(mysql_error());
								while($r = mysql_fetch_assoc($res)) {
									$c++;
									if ($c == 1) {
										echo 
										"<ul>
											<li id='offer-nav'>
												<ul>
													<li id='oli1'>Title</li>
													<li id='oli2'>Price(Used)</li>
													<li id='oli3'>Recycler</li>
													<li id='oli4'>Recycler Rating &#9660;</li>
													<li onclick='showDropWindow(\"sort-select\");' class='cmb'>Sort &#x25BC;</li>
												</ul>
											</li>";
									}
									echo 
									"
									<li class='offer'>
										<div class='search-prev' style='background:url(img/product-imgs/{$r['prod_id']}.jpg); background-size:32px 51px;'></div>
										<p style='width:300px;'>{$p['name']}</p>
										<p style='width:120px;'>£{$r['price']}</p>
										<div class='offer-logo'><img style='max-width: 100px; margin-top:5px;' height:80px; src='img/suppliers/{$r['supplier_id']}.jpg'></div>
										<a href='".getURL($r['prod_id'], $r['supplier_id'], $r['url'])."'>Sell</a>
										<div class='review-rating stars-".roundRating(getRating($r['supplier_id']))."'></div>
									</li>";
								}
								unset($r,$res);
							}
							break;
					}
					if ($c == 0) {
						echo "<p class='errorbox'>&#10007; No Recyclers are buying this DVD right now.</p>";
						if (isset($_SESSION['user'])) {
							echo "<p class='text'>Click <a href='account.php?notify={$p_id}'>here</a> to receive an email notification as soon as someone wants to buy it.</p>";
						}
						else {
							echo "<p class='text'>Click <a href='login.php?fr&next=".urlencode("account.php?notify={$p_id}")."'>here</a> to receive an email notification as soon as someone wants to buy it.</p>";
						}
					}
				}
				?>
			</ul>
			<div class='drop-window' id='sort-select' tabindex='0' style='top:520px; margin-left:936px; width:150px; height:94px; display:none;'>
			<div class='arrow-n'></div>
			<ul class='drop-list'>
				<li class='drop-list-item' id='4'>Sort By Price</li>
				<li class='drop-list-item' id='5'>Sort By Rating</li>
			</ul>
			</div>
			
		</div>
		
	</div>
</div>
<?php
	include_once "include/footer.php";
?>
