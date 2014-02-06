<?php
//error_reporting(E_ALL);
//ini_set('display_errors', -1);
echo "<script>
var int=self.setInterval(function(){scrollme()},100);

function scrollme()
{
	dh=document.body.scrollHeight;
	ch=document.body.clientHeight;
	if(dh>ch)
	{
		moveme=dh-ch;
		window.scrollTo(0,moveme);
	}
}
</script>";
echo "<link type='text/css' rel='stylesheet' href='console.css'/>";
require_once '../include/setup.php';
$url = "http://www.imdb.com/search/title?view=simple&count=10&title_type=feature,tv_movie,documentary&genres=";
$cats = array(
	"action",
	"adventure",
	"animation",
	"biography",
	"comedy",
	"crime",
	"documentary",
	"drama",
	"family",
	"fantasy",
	"film_noir",
	"history",
	"horror",
	"music",
	"musical",
	"mystery",
	"romance",
	"sci_fi",
	"short",
	"sport",
	"thriller",
	"war",
	"western");
foreach($cats as $cat) {
	echo "<br><p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>Using Category `{$cat}` with url : {$url}{$cat}&start=1</p>";
	$from = 0; // Which page to start from
	$to = 1; // Change this to increase no of page to crawl through.
	for( $c = 0 ; $c < $to ; $c++) {
		$start = "&start=".(( $c * 250 ) + 1 );
		$page = file_get_contents($url.$cat.$start);
		$ids = array();
		preg_match_all("/tt\d{7}/", $page, $ids);
		$ids = array_unique($ids);
		$ids = $ids[0];
		$maxlen = strlen(count($ids));
		echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>Found ".count($ids)." URLs to query.</p>";
		echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>
		------------------------------------------------------------------------------------------------------------</p><br>";
		$c = 0;
		foreach($ids as $id) {
			$c++;
			$padc = str_pad($c,$maxlen,"0",STR_PAD_LEFT);
			if($json = file_get_contents("http://imdbapi.org/?type=json&episode=0&id=".urlencode($id))) {
				$data = json_decode($json);
				if (!isset($data->{'code'},$data->{'error'})) {
					$id = mysql_real_escape_string($data->{'imdb_id'});
					$title = mysql_real_escape_string($data->{'title'});
					$year = mysql_real_escape_string($data->{'year'});
					$rating = mysql_real_escape_string($data->{'rating'}) / 2;
					mysql_query("INSERT INTO `db_list` (`name`,`imdb_id`,`year`,`rating`,`category`) VALUES ('{$title}','{$id}','{$year}','{$rating}','1');");
					if (mysql_affected_rows() < 1) {
						echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Failed Adding ".$data->{'title'}." | ".mysql_error()."</p>";
					} else {
						$pid = mysql_insert_id();
						if (file_put_contents("../img/product-imgs/{$pid}.jpg", file_get_contents($data->{'poster'})))
						{
							echo "<p style='font-family:Andale Mono;font-size:12px;color:green;margin:2px;'>[{$padc}] &#10003; Success! Added: ".$data->{'title'}."</p>";
						} else {
							echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Failed Getting Poster for ".$data->{'title'}."</p>";
						}
					}
				} else {
					echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Error Occured : Code : ".$data->{'code'}." | Error : ".$data->{'error'}." | imdb id = {$id}</p>";
				}
			} else {
				echo "<p style='font-family:Andale Mono;font-size:12px;color:red;margin:2px;'>[{$padc}] &#10007; Error unable to connect to imdb api.</p>";
			}
			ob_end_flush(); 
			@ob_flush(); 
			flush(); 
			ob_start();
			unset($data,$json,$id,$title,$year,$rating);
		}
	}
}
?>
