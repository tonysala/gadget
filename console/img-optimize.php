<?php
echo "<link type='text/css' rel='stylesheet' href='console.css'/>
<script type='text/javascript' src='console.js'></script>";
require_once '../include/setup.php';
$items = mysql_query("SELECT `id` FROM `db_list` WHERE `category` = 1 OR `category` = 3 ;") or die("error: ".mysql_error());
$u = $freed = $bfs = $afs = $already_optimized = 0;
mkdir("../img/product-imgs-2",0777);
while($item = mysql_fetch_assoc($items)) {
	if (file_exists("../img/product-imgs/{$item['id']}.jpg")) {
		$imgsize = getimagesize("../img/product-imgs/{$item['id']}.jpg");
		if ($imgsize[0] !==  121 || $imgsize[1] !== 174) {	
			$bfs = filesize("../img/product-imgs/".$item['id'].".jpg");
			$img = imagecreatefromjpeg("../img/product-imgs/{$item['id']}.jpg");
			$new = imagecreatetruecolor(121, 174);
			imagecopyresampled($new, $img, 0, 0, 0, 0, 121, 174, $imgsize[0], $imgsize[1]);
			imagejpeg($new,"../img/product-imgs-2/{$item['id']}.jpg",100);
			$afs = filesize("../img/product-imgs-2/{$item['id']}.jpg");
			$freed += ($bfs - $afs);
			echo "
			<div style='width:105px; height:177px; margin:10px; float:left;'>
				<img class='dvd' src='../img/product-imgs/{$item['id']}.jpg'/>
				<div class='blue-button' style='font-size:14px;'>-".abs(round((($bfs - $afs) / 1024), 2))."kB</div>
			</div>";
		} else $already_optimized++;
	}
	ob_end_flush(); 
	@ob_flush(); 
	flush(); 
	ob_start();
	$total++;
}

$dir = "../img/product-imgs";
$tmp_dir = "../img/product-imgs-2";
$objects = scandir($dir); 
foreach ($objects as $object) { 
	if ($object != "." && $object != "..") { 
		copy($tmp_dir."/".$object,$dir."/".$object);
		unlink($tmp_dir."/".$object); 
	} 
}
rmdir($tmp_dir); 
rename("../img/product-imgs-2","../img/product-imgs");
echo "<br><p style='margin:10px;clear:both;'>Already Optimized ({$already_optimized} / {$total})</p>
<div class='blue-button' style='font-size:14px;float:left;margin: 20px 8px;width: 95%;padding: 0 9px;'>Load Size Decreased By : ".round($freed/1024,2)."kB ( ~ ".round(($freed/(1024*1024)),2)." MB )</div>
<p style='margin:10px;clear:both;'>Script Executed.</p>";
?>
