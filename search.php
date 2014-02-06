<?php
	include_once "include/header.php";
?>
<div class='bg-section'>
	<h1 class='headline'>Search Results</h1>
	<div class='width-reg'>
	<?php
	if (isset($_GET['q'])) {
		$mtime = explode(" ",microtime()); 
		$starttime = $mtime[1] + $mtime[0]; 
		// GETS PAGE & DETERMINES PRODUCT LIST TO FETCH
		if (isset($_GET['p'])) {
			$p = mysql_real_escape_string($_GET['p']);
			if (!$p < 1 && is_numeric($p) === true) {
				($p == 1 ? $start = 1 : $start = ($p * 24) - 23);
				($p == 1 ? $end = $start + 23 : $end = $start + 23);
			}
			else {
				echo "<p class='text'>&#10007 No results found.</p></div></div>";
				include_once "include/footer.php";
				exit;
			}
		}
		else {
			$p = $start = 1;
			$end = 24;
		}
		$sort = $cat = "";
		$q = mysql_real_escape_string($_GET['q']);
		$curr_sort = (isset($_GET['sort']) ? $_GET['sort'] : 1);
		$curr_type = (isset($_GET['type']) ? $_GET['type'] : 0);
		echo "<div id='search-wrap'>
			<form id='home-search-form' autocomplete=off action='search.php' method='get'>
				<input name='q' type='text' style='border-radius:2px;border: 1px solid #BBB;width:614px;' id='search-text' value='{$q}'>
				<div id='search-button' style='border-radius: 2px;' onclick='submitForm(\"home-search-form\")'>Search</div></div>
			</form>";
		// GETS QUERY & GENERATES QUERIES ARRAY
		echo "<div class='actions-list' style='float:left;'>";

		$btn_style = "style='width:120px;'";
		switch(@$_GET['type']) {
			case 0:
			default:
				$cat = "";
				echo "<a {$btn_style} class='inuse'>Search All</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=1'>Search DVDs</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=2'>Search Books</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=3'>Search Games</a>";
				break;
			case 1:
				$cat = "AND `category` = '1'";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=0'>Search All</a>";
				echo "<a {$btn_style} class='inuse'>Search DVDs</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=2'>Search Books</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=3'>Search Games</a>";
				break;
			case 2:
				$cat = "AND `category` = '2'";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=0'>Search All</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=1'>Search DVDs</a>";
				echo "<a {$btn_style} class='inuse'>Search Books</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=3'>Search Games</a>";
				break;
			case 3:
				$cat = "AND `category` = '3'";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=0'>Search All</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=1'>Search DVDs</a>";
				echo "<a {$btn_style} href='search.php?q=".urlencode($q)."&sort={$curr_sort}&type=2'>Search Books</a>";
				echo "<a {$btn_style} class='inuse'>Search Games</a>";
				break;
		}
		echo "</div><div class='actions-list' style='float:right;'>";
		switch(@$_GET['sort']) {
			case 1:
			default:
				$sort = "ORDER BY `popularity` DESC";
				echo "<a class='inuse'>Sort By Relevance</a>";
				echo "<a href='search.php?q=".urlencode($q)."&sort=2&type={$curr_type}'>Sort By Release Year</a>";
				echo "<a href='search.php?q=".urlencode($q)."&sort=3&type={$curr_type}'>Sort By Rating</a>";
				break;
			case 2:
				$sort = "ORDER BY `year` DESC";
				echo "<a href='search.php?q=".urlencode($q)."&sort=1&type={$curr_type}'>Sort By Relevance</a>";
				echo "<a class='inuse'>Sort By Release Year</a>";
				echo "<a href='search.php?q=".urlencode($q)."&sort=3&type={$curr_type}'>Sort By Rating</a>";
				break;
			case 3:
				$sort = "ORDER BY `rating` DESC";
				echo "<a href='search.php?q=".urlencode($q)."&sort=1&type={$curr_type}'>Sort By Relevance</a>";
				echo "<a href='search.php?q=".urlencode($q)."&sort=2&type={$curr_type}'>Sort By Release Year</a>";
				echo "<a class='inuse'>Sort By Rating</a>";
				break;
		}
		echo "</div><div style='clear:both;'></div><br><br>";
		
		// CHECKS FOR VALID QUERY
		if (trim($q) !== "") {	
			$queries = explode(" ", $q);
			$results = array();
			foreach ($queries as $query) {	
				// GET MATCHES & CHECK IF SUCCESS
				$matches = mysql_query("SELECT DISTINCT `name`,`id` FROM `db_list` WHERE `name` REGEXP '{$query}' {$cat} {$sort};") or die(mysql_error());
				if (is_resource($matches)) {
					// LOOP THROUGH MATCHES
					while($row = mysql_fetch_assoc($matches)) {
						$result = "<div class='dvd'><div class='dvd-inlay' style='background: url(\"img/product-imgs/{$row['id']}.jpg\"); background-size:121px 174px;'></div><div class='btn' style='font-size:12px; line-height:24px;' onclick='goToProduct({$row['id']});'>Sell {$row['name']}</div></div>";
						if (in_array($result, $results) === true) {
							array_unshift($results, $result);
						}
						else {
							$results[] = $result;
						}
					}
				}
			}
			$results = array_unique($results);
			$results = array_values($results);
			array_unshift($results,"");
		}
		else {
			echo "<p class='text'>&#10007 No results found.</p></div></div>";
			include_once "include/footer.php";
			exit;
		}
		if ((count($results)-1) === 0) {
			echo "<p class='text'>&#10007; No Results Found.</p>";
		}
		else {
			$totalpages = ceil((count($results)-1) / 24);
			if ($p > $totalpages) {
				echo "<p class='text'>&#10007; No Results Found.</p>";
			}
			else {
				for ($c = $start; $c <= $end; $c++) {	
					if (isset($results[$c])) {
						echo $results[$c];
					}
				}
				if ($totalpages > 9) {
					$startpage = $p - 4;
					$endpage = $p + 4;
					($endpage < 9 ? $endpage = 9: $endpage = $endpage);
					($endpage > $totalpages ? $endpage = $totalpages : $endpage = $endpage);
					($startpage < 1 ? $startpage = 1 : $startpage = $startpage);
					($startpage + 8 != $endpage ? $startpage = ($endpage - 8) : $startpage = $startpage);
				}
				else {
					$startpage = 1;
					$endpage = $totalpages;
				}
				echo "<div style='clear:both;'></div><div class='actions-list' style='width:". (35 * ($endpage + 2)) ."px; max-width:385px; margin:20px auto;'>";
				
				for ($c = $startpage - 1; $c <= $endpage + 1; $c++) {
					if ($c == $startpage - 1) {
						if ($p == 1) {
							echo "<a style='width:34px;' class='disabled'>&#9664;</a>";
						}
						else {
							echo "<a style='width:34px;' class='page_btn' href='search.php?q=".urlencode($_GET['q'])."&p=".($p - 1)."&sort={$curr_sort}&type={$curr_type}'>&#9664;</a>";
						}
					}
					else if($c == $endpage + 1) {
						if ($p == $endpage) {
							echo "<a style='width:34px;' class='disabled'>&#9654;</a>";
						}
						else {
							echo "<a style='width:34px;' class='page_btn' href='search.php?q=".urlencode($_GET['q'])."&p=".($p + 1)."&sort={$curr_sort}&type={$curr_type}'>&#9654;</a>";
						}
					}
					else if ($c != $p) {
						echo "<a style='width:34px;' href='search.php?q=".urlencode($_GET['q'])."&p={$c}&sort={$curr_sort}&type={$curr_type}'>{$c}</a>";
					}
					else {
						echo "<a style='width:34px;' class='disabled' >{$c}</a>";
					}
				}
			echo "</div>";
			$mtime = explode(" ",microtime()); 
			$endtime = $mtime[1] + $mtime[0];
			$time = round(($endtime - $starttime),6); 
			echo "<div style='clear:both;'></div><p class='text'>Returned ". (count($results)-1) ." Results in {$time} seconds</p>";
			}
		}
	}
	else {
		echo "<p class='text'>&#10007; No Results Found.</p>";
	}
	?>
	<div style='clear:both;'></div>
	</div>
</div>
<?php
	include_once "include/footer.php";
?>
