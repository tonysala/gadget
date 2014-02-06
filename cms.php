<?php
include_once 'include/cmsheader.php';
include_once 'include/cmsfunctions.php';
$search = $new_id = "";
$e = $st = array();
$page = (!isset($_GET['page']) ? "" : $_GET['page']);
if (isset($_GET['search'])) {
	if (str_replace(" ","",$_GET['search']) != "") {
		$search = get_search_query(mysql_real_escape_string($_GET['search']),$page);
	} else {
		$page = "product";
	}
} else if (isset($_POST['sql'])) {
	execute_sql($_POST['sql'],$st,$e,$page);
} else if (isset($_GET['new_supplier_name'], $_GET['supplier_url'])) {
	set_new_supplier(mysql_real_escape_string(htmlentities($_GET['new_supplier_name'])),$_GET['supplier_url'],$st,$e,$page);
} else if (isset($_POST['new_admin_user'],$_POST['new_admin_pass'])) {
	set_new_admin(sha1(md5($_POST['new_admin_pass'])),mysql_real_escape_string($_POST['new_admin_user']),$page);
} else if (isset($_GET['rm_admin_id'])) {
	unset_admin(mysql_real_escape_string($_GET['rm_admin_id']),$st,$e,$page);
} else if (isset($_GET['rm_db_id'])) {
	unset_product(mysql_real_escape_string($_GET['rm_db_id']),$st,$page);
} else if (isset($_GET['rm_recycler_id'])) {
	unset_supplier(mysql_real_escape_string($_GET['rm_recycler_id']),$st,$page);
} else if (isset($_GET['prod_title'],$_GET['prod_year'],$_GET['prod_type'],$_GET['prod_cat'])) {
	$new_id = set_new_prod($_GET['prod_title'],$_GET['prod_year'],$_GET['prod_type'],substr($_GET['prod_cat'],0,1),$_GET['prod_barcode'],$_GET['prod_poster'],$_GET['prod_imdb'],$_GET['prod_author'],$st,$e,$page);
} else if (isset($_GET['new_bc'],$_GET['pid'])) {
	set_barcode($_GET['new_bc'],$_GET['pid'],$page,$st);
} else if (isset($_GET['empty_db'])) {
	mysql_query("DELETE FROM `db_offers` WHERE `supplier_id` = '".mysql_real_escape_string($_GET['empty_db'])."';");
	$page = "csv";
	$st[] = "&#10003; Emptied Offers.";	
} else if (isset($_GET['new_csv_url'],$_GET['sid'])) {
	mysql_query("UPDATE `db_supplier` SET `url` = '".mysql_real_escape_string($_GET['new_csv_url'])."' WHERE `id` = '".mysql_real_escape_string($_GET['sid'])."';");
	$page = "csv";
	$st[] = "&#10003; Added URL.";	
} else if (isset($_GET['dl_log'])) {
	unlink("cron/logs/{$_GET['dl_log']}");

}
?>
<div class="bg-section">
	<div class='width-reg'>
	<?php
	if (!empty($e)) {
			echo '<ul class="t errorbox"><li>'.implode("</li><li>", $e).'</li></ul><br>';
		}
	if (!empty($st)) {
		echo '<ul class="t successbox"><li>'.implode("</li><li>", $st).'</li></ul>';
	}
	echo "
	<h1 class='headline'>CMS</h1>
	<div class='actions-list var-width actions-emb emb' style='width:1018px;'>
		<a href='cms.php?page=cms'><div>CMS Home</div></a>
		<a href='cms.php?page=product'><div>Products</div></a>
		<a href='cms.php?page=recyclers'><div>Recyclers</div></a>
		<a href='cms.php?page=csv'><div>CSV List</div></a>
		<a href='cms.php?page=outbound'><div>Clicks</div></a>
		<a href='cms.php?page=users'><div>Users</div></a>
		<a href='cms.php?page=admins'><div>Admins</div></a>
		<a href='cms.php?page=errors'><div>Error Log</div></a>
		<a href='cms.php?page=settings'><div>Settings</div></a>
	</div><div style='clear:both;'><br>";
	$c = 0;
	switch($page) {
		default:
			$analytics = mysql_query("SELECT (SELECT `page_views` FROM `db_analytics`) AS `p` , (SELECT COUNT(`id`) FROM `db_clicks`) AS `c` , (SELECT COUNT(`uid`) FROM `db_users`) AS `u` , (SELECT COUNT(`id`) FROM `db_list` WHERE `category` = '1') AS `d` , (SELECT COUNT(`id`) FROM `db_list` WHERE `category` = '2') AS `b` , (SELECT COUNT(`id`) FROM `db_list` WHERE `category` = '3') AS `g`");
			$analytics = mysql_fetch_assoc($analytics);
			echo "<div style='text-align:left; color:white; text-shadow:0 1px rgba(0,0,0,.5); padding-left:100px;'>
				<h3><u>Site Analytics</u></h3>
				<p>Page Views: {$analytics['p']}</p>
				<p>Outbound Clicks: {$analytics['c']}</p>
				<p>Users Registered: {$analytics['u']}</p>
				<p>DVDs in Database: {$analytics['d']}</p>
				<p>Books in Database: {$analytics['b']}</p>
				<p>Games in Database: {$analytics['g']}</p></div>";
			break;
		case "product":
			if (isset($_GET['p'])) {
				$p = mysql_real_escape_string($_GET['p']);
				if (!$p < 1 && is_numeric($p)) 	{
					($p == 1 ? $start = 1 : $start = ($p * 24) - 23);
					($p == 1 ? $end = $start + 23 : $end = $start + 23);
				} else {
					echo "<p class='text'>&#10007 No results found.</p></div></div>";
					include_once "include/footer.php";
					exit;
				}
			} else {
				$p = $start = 1;
				$end = 24;
			}
			if (isset($_GET['prod_poster']) && empty($_GET['prod_poster']) && !empty($new_id)) {
				echo get_poster_images($new_id);
			}
			echo "
			<div class='emb' style='height:35px;border-radius:4px;width:229px;padding:1px;'>
				<div class='actions-list' style='float:left;'>
					<div style='width:229px!important;' onclick='document.getElementById(\"add_prod\").style.display =\"block\";'>&#10003; Show Add Product Form</div>
				</div><br>
			</div>
			<div id='add_prod' class='console' style='display:none; height:432px; width:355px;'>
				<form action='cms.php' method='get' style='padding:14px;'>
					<label><u>General Info</u></label><br><br>
					<label>Product Title & Year [Required]</label><br>
					<input type'text' name='prod_title' style='float:left; width:205px; margin-right:10px;' placeholder='title' class='login-input'>
					<input type'text' placeholder='year' style='width:64px;' name='prod_year' class='login-input'><br>
					<label>Barcode & Type [Required]</label><br>
					<input type'text' placeholder='barcode' style='float:left; width:205px;' name='prod_barcode' class='login-input'>
					<select name='prod_type' class='login-select'>
						<option>DVD</option><option>BLURAY</option>
						<option>BOOK</option><option>PC</option>
						<option>XBOX</option><option>XBOX360</option>
						<option>PS3</option><option>PS2</option>
					</select><br>
					<label>Product Category[Required]</label><br>
					<select name='prod_cat' class='login-select' style='width:320px!important;'>
						<option>1 | DVD</option><option>2 | BOOK</option><option>3 | GAME</option>
					</select><br>
					<label>URL to JPG Poster</label><br>
					<input type='text' name='prod_poster' class='login-input'><br>
					<br><label><u>DVD Info</u></label><br><br>
					<label>IMDB ID [Required for DVDs]</label><br>
					<input type'text' placeholder='i.e tt1411697' name='prod_imdb' class='login-input'><br>
					<br><label><u>Book Info</u></label><br><br>
					<label>Author [Required for Books]</label><br>
					<input type='text' name='prod_author' class='login-input'><br>
					<br>
					<input type='submit'  value='Add Product' style='padding:3px 2px!important;color:white!important;height:26px!important;border:1px solid #22499C;' class='blue-button'>
					<input type='button' onclick='document.getElementById(\"add_prod\").style.display =\"none\";' value='Cancel' style='padding:3px 2px!important;color:white!important;height:26px!important;border:1px solid #9C2C22;' class='red-button'>
					<div style='clear:both;'></div>
				</form></div>";
			echo "
			<br><div style='clear:both'></div>
			<form action='cms.php' method='get'>
			<input type='text' style='height:26px;' name='search' placeholder='Search Title' class='login-input'>
			<input type='submit' style='margin:0 10px 0 0; height: 32px; float:left; font-weight: 800; cursor: pointer; border: 1px solid #0965B9;' value='search' class='blue-button'></form>
			<table style='width:1024px; class='reviewtable'>
			<tr>
				<th style='width:42px;'>Image</th>
				<th>ID</th>
				<th>Title</th>
				<th style='width:125px;'>Barcode</th>
				<th style='width:75px;'>Type</th>
				<th style='width:42px;'>Year</th>
				<th style='width:88px;'>Popularity</th>
				<th style='width:75px;'>Last Sold</th>
				<th style='width:108px;'>Delete</th>
			</tr>";
			$result = mysql_query("SELECT `id`,`sold`,`name`,`year`,`barcode`,`platform`,`popularity` FROM `db_list` {$search} ORDER BY `id` ASC") or die (mysql_error());
			$maxpop = mysql_result(mysql_query("SELECT MAX(`popularity`) FROM `db_list`;"),0);
			$curr_p = (isset($_GET['p']) ? $_GET['p'] : 1);
			while ($row = mysql_fetch_assoc($result)) {
				$pop = round(($row['popularity']/$maxpop)*100,0);
				if ($row['sold'] > 0) {
					$sold = date("d/m/y",$row['sold']);
				}
				else {
					$sold = "0 Sold";
				}
				$c++;
				$results[] =
				"<tr>
					<td><div class='search-prev' style='margin:1px; background:url(img/product-imgs/{$row['id']}.jpg); background-size:32px 51px;'></td>
					<td>{$row['id']}</td>
					<td>{$row['name']}</td>
					<td><input type='text' id='bc_box_{$row['id']}' value='{$row['barcode']}' style='padding-right:32px; width:120px; float:left; margin:12px 0;' class='login-input'><div onclick='location.href = \"cms.php?new_bc=\"+document.getElementById(\"bc_box_{$row['id']}\").value+\"&pid={$row['id']}\";' style='float:left; opacity:1; width:22px; height:22px; line-height:22px; margin:14px -26px;' class='t green-button'>&#10133;</div></td>
					<td>{$row['platform']}</td>
					<td>{$row['year']}</td>
					<td>{$pop}%</td>
					<td>{$sold}</td>
					<td><a class='red-button' style='float:left; margin:0;' href='cms.php?rm_db_id={$row['id']}&p={$curr_p}'>Delete</a></td>
				</tr>";
			}
			if($c == 0) {
				echo "<td colspan='9'><ul class='errorbox' style='margin:20px auto;'><li>No Results.</li></ul></td></table>";
			}
			else {
				$totalpages = ceil((count($results)-1) / 24);
				if ($p > $totalpages) {
					$p = $totalpages;
					echo "<td colspan='9'><ul class='errorbox' style='margin:20px auto;'><li>Page Number Requested Does Not Exist.</li></ul></td>";
				}
				for ($c = $start; $c <= $end; $c++) {	
					if (isset($results[$c])) {
						echo $results[$c];
					}
				}
				echo "</table>";
				if ($totalpages > 9) {
					$startpage = $p - 4;
					$endpage = $p + 4;
					$endpage = ($endpage < 9 ? 9: $endpage);
					$endpage = ($endpage > $totalpages ? $totalpages : $endpage);
					$startpage = ($startpage < 1 ? 1 : $startpage);
					$startpage = ($startpage + 8 != $endpage ? ($endpage - 8) : $startpage);
				}
				else {
					$startpage = 1;
					$endpage = $totalpages;
				}
				echo "<div style='clear:both;'></div><div class='actions-list' style='width:". (37 * ($endpage + 2)) ."px; margin:20px auto;'>";
				$search_url = ($search != "" ? "&search=".urlencode($_GET['search']) : "");
				for ($c = $startpage - 1; $c <= $endpage + 1; $c++) {
					if ($c == $startpage - 1) {
						if ($p == 1) {
							echo "<a style='width:36px!important;' class='disabled'>&#9664;</a>";
						}
						else {
							echo "<a style='width:36px!important;' class='page_btn' href='cms.php?page=product&p=".($p - 1)."{$search_url}'>&#9664;</a>";
						}
					}
					else if($c == $endpage + 1) {
						if ($p == $endpage) {
							echo "<a style='width:36px!important;' class='disabled'>&#9654;</a>";
						}
						else {
							echo "<a style='width:36px!important;' class='page_btn' href='cms.php?page=product&p=".($p + 1)."{$search_url}'>&#9654;</a>";
						}
					}
					else if ($c != $p) {
						echo "<a style='width:36px!important;' href='cms.php?page=product&p={$c}{$search_url}'>{$c}</a>";
					}
					
					else {
						echo "<a style='width:36px!important;' class='disabled' >{$c}</a>";
					}
				}
				echo "</div><br><br><p class='text'>Returned {$totalpages} Pages.</p></div>";
			
			}
			break;
		case "recyclers":	
			echo "<h3 style='color:white; text-shadow:0 1px rgba(0,0,0,.5);'><u>Add Recycler</u></h3>
				<form action='cms.php?page=product' method='get' class='emb' style='padding:14px;'>
				<label>Recycler Name</label><br><br>
				<input type'text' name='new_supplier_name' placeholder='name' class='login-input'><br><br>
				<label>URL to <u>JPG</u> recycler logo</label><br><br>
				<input type'text' placeholder='url' name='supplier_url' class='login-input'><br>
				<input type='hidden' name='page' value='product'>
				<br><br><input type='submit' value='Add Recycler' class='sell-btn'>
				<br><br><br>
				</form>";
			echo 
			"<table style='width:1024px; class='reviewtable'>
			<tr>
				<th>Logo</th>
				<th>ID</th>
				<th>Name</th>
				<th>Rating</th>
				<th>Voted</th>
				<th>Offers</th>
				<th>Remove</th>
			</tr>";
			$result = mysql_query("SELECT * FROM `db_supplier` ORDER BY `id` DESC");
			while ($row = mysql_fetch_assoc($result)) {
				echo 
				"<tr>
					<td><div class='offer-logo'><img src='img/suppliers/{$row['id']}.jpg'></div></td>
					<td>{$row['id']}</td>
					<td>{$row['name']}</td>
					<td>{$row['rating']}</td>
					<td>{$row['votes']}</td><td>";
					
					echo mysql_result(mysql_query("SELECT COUNT(`id`) FROM `db_offers` WHERE `supplier_id` = '{$row['id']}'"),0);
					
					echo "</td><td><a class='red-button' style='float:left; margin:0;' href='cms.php?rm_recycler_id={$row['id']}'>Delete</a></td>
				</tr>";
			}
			echo "</table>";
			break;
		case "csv":
			$q = mysql_query("SELECT * FROM `db_supplier`") or showError(mysql_error());
			echo "<table style='width:1024px; class='reviewtable'>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>CSV URL</th>
				<th>Update Schema</th>
				<th>Products</th>
				<th>Empty</th>
			</tr>";
			if (is_resource($q)) {
				while ($row = mysql_fetch_assoc($q)) {
					$products = mysql_query("SELECT (SELECT COUNT(`id`) FROM `db_offers` WHERE `category` = '1' AND `supplier_id` = '{$row['id']}') AS `a` , (SELECT COUNT(`id`) FROM `db_offers` WHERE `category` = '2' AND `supplier_id` = '{$row['id']}') AS `b` , (SELECT COUNT(`id`) FROM `db_offers` WHERE `category` = '3' AND `supplier_id` = '{$row['id']}') AS `c`;") or die(mysql_error());
					$products = mysql_fetch_assoc($products);
					$c++;
					echo
					"<tr>
						<td>{$row['id']}</td>
						<td>{$row['name']}</td>
						<td><input type='text' id='csv_url_{$row['id']}' style='padding-right:32px; width:220px; float:left; margin:12px 0;' value='{$row['url']}' class='login-input'><div onclick='location.href = \"cms.php?new_csv_url=\"+document.getElementById(\"csv_url_{$row['id']}\").value+\"&sid={$row['id']}\";' style='float:left; width:22px; height:22px; line-height:22px; margin:14px -26px;' class='t green-button'>&#10133;</div></td>
						<td><div style='width:150px;' onclick='location.href = \"schema.php?recycler={$row['id']}\";' class='t blue-button'>&#10133; Updata Schema</div></td>
						<td>DVDs : {$products['a']}<br>Books : {$products['b']}<br>Games : {$products['c']}</td>
						<td><a class='red-button' style='float:left; margin:0;' href='cms.php?empty_db={$row['id']}'>&#10060; Empty</a></td>
					</tr>";
				}
			}
			echo "</table><div style='clear:both;'></div>";
			break;
		case "outbound":
			echo "<table style='width:1024px; class='reviewtable'>
			<tr>
				<th>User ID</th>
				<th>Supplier ID</th>
				<th>DVD ID</th>
				<th>URL</th>
				<th>Date</th>
			</tr>";
			$result = mysql_query("SELECT * FROM `db_clicks` ORDER BY `id` DESC");
			while ($row = mysql_fetch_assoc($result)) {
				$c ++;
				echo 
				"<tr>
					<td>{$row['uid']}</td>
					<td>{$row['sid']}</td>
					<td>{$row['pid']}</td>
					<td>{$row['url']}</td>
					<td>".date("d/m/y",$row['timestamp'])."</td>
				</tr>";
			}
			echo "</table>";
			if($c == 0) {
			echo "<ul class='errorbox' style='margin:200px auto;'><li>No Results.</li></ul>";
			}
			break;	
		case "users";
			echo "<table style='width:1024px; class='reviewtable'>
			<tr>
				<th>User ID</th>
				<th>Username</th>
				<th>Name</th>
				<th>Email</th>
				
			</tr>";
			$result = mysql_query("SELECT * FROM `db_users` ORDER BY `uid` DESC");
			while ($row = mysql_fetch_assoc($result)) {
				$c ++;
				echo 
				"<tr>
					<td>{$row['uid']}</td>
					<td>{$row['username']}</td>
					<td>{$row['title']} {$row['fname']} {$row['sname']}</td>
					<td>{$row['email']}</td>
				</tr>";
			}
			echo "</table>";
			if($c == 0) {
			echo "<ul class='errorbox' style='margin:200px auto;'><li>No Results.</li></ul>";
			}
			break;
		case "admins";
			echo "<h3 style='color:white; text-shadow:0 1px rgba(0,0,0,.5);'><u>Add Admin</u></h3>
				<form action='cms.php' method='post' class='emb' style='padding:14px;'>
				<label>Admin Userame</label><br><br>
				<input type'text' name='new_admin_user' placeholder='username' class='login-input'><br><br>
				<label>Admin Password</label><br><br>
				<input type='password' placeholder='password' name='new_admin_pass' class='login-input'><br>
				<br><br><input type='submit' value='Add Admin' class='sell-btn'>
				<br><br><br>
				</form>";
			echo "<table style='width:1024px; class='reviewtable'>
			<tr>
				<th>Admin ID</th>
				<th>Username</th>
				<th>Delete</th>
			</tr>";
			$result = mysql_query("SELECT * FROM `db_admins` ORDER BY `uid` DESC");
			while ($row = mysql_fetch_assoc($result)) {
				$c ++;
				echo 
				"<tr>
					<td>{$row['uid']}</td>
					<td>{$row['username']}</td>
					<td><a class='red-button' style='float:left; margin:0;' href='cms.php?rm_admin_id={$row['uid']}'>Delete</a></td>
				</tr>";
			}
			echo "</table>";
			if($c == 0) {
			echo "<ul class='errorbox' style='margin:200px auto;'><li>No Results.</li></ul>";
			}
			break;
		case "errors":
			$logs = scandir("cron/logs/",SCANDIR_SORT_DESCENDING);
			echo "<label><u>Cron Job Logs.</u></label>";
			if (isset($_GET['op_log'])) {
				echo "<div class='console' style='display:block;padding-left:16px;width:850px;overflow-y:scroll;overflow-x:scroll;'><pre>";
				echo file_get_contents("cron/logs/{$_GET['op_log']}");
				echo "</pre></div><a  href='cms.php?page=errors'><div style='position:absolute;bottom:15%;opacity:1;margin:0 auto;left:0;right:0;' class='red-button'>Close</div></a>";
			}
			echo "<br><div class='emb' style='width:600px;padding-left:24px;overflow-y:scroll;overflow-x:hidden;'>";
			if (count($logs) > 2) {
				foreach($logs as $log) {
					if ($log != "." && $log != "..") {
						$date = date("F j, Y, g:i a",(str_replace(".log","",$log) * 3600)); 
						echo "<br><div class='actions-list var-width' style='width:226px;'>";
						echo "<a href='cms.php?page=errors&op_log=".urlencode($log)."'>Open Log</a><a href='cms.php?dl_log=".urlencode($log)."'>&#10007; Delete Log</a></div><label style='line-height: 38px;'>{$date}</label>";
					}
				}
				echo "</div>";
			} else {
				echo "<p class='text'>No Logs Found.</p>";
			}
			break;
		case "settings":
				echo "<form action='' method='post'>
				<br><br>
				<label><u>Execute SQL Queries</u></label><br><br>
				<input type='text' name='sql' class='login-input' placeholder='sql query..'>
				<br><br>
				<div class='actions-list'>
					<input type='submit' value='&#10003; Validate & Execute'>
				</div>
				</form>
				<br><br><br><label>Run Scripts</label><br><br>
				<div class='red-button-list'>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/add-dvds.php\"; d.style.display = \"block\";'>Load New Films From IMDB</a>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/add-games.php\"; d.style.display = \"block\";'>Load New Games From IMDB</a>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/auto-img.php\"; d.style.display = \"block\";'>Find Missing Artwork</a>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/get-barcode.php\"; d.style.display = \"block\";'>Find Missing Barcodes</a>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/img-optimize.php\"; d.style.display = \"block\";'>Optimize Images</a>
					<a onclick='document.getElementById(\"stop_frame\").style.display = \"block\"; var d = document.getElementById(\"cnsl_window\"); d.src = \"console/find-alts.php\"; d.style.display = \"block\";'>Find Possible Duplicates</a>
				</div>
				<iframe src='' id='cnsl_window' class='console'></iframe>
				<br>
				<div id='stop_frame' style='display:none;' class='red-button' onclick='document.getElementById(\"stop_frame\").style.display = \"none\"; if (navigator.appName == \"Microsoft Internet Explorer\") {window.frames[0].document.execCommand(\"Stop\");} else {window.frames[0].stop();} document.getElementById(\"cnsl_window\").style.display = \"none\";'>Stop Script & Close</div>";
			break;
	}
	?>
</div>
<?php
include_once 'include/cmsfooter.php';
?>
