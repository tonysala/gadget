<?php
include_once "include/header.php";
?>
<script>
		(function($){
			$(window).load(function(){
				$("#scrl").mCustomScrollbar({
					horizontalScroll:true,
					scrollButtons:{
						enable:true,
						scrollType:"pixels",
						scrollAmount:140
					}
				});
			});
		})(jQuery);
		window.onload = function(){
			setTimeout(function(){
			document.getElementById("ajax-loader").style.display = "none";
			document.getElementById("scrl-wrap").style.visibility = "visible";
		},450);
			
		}
</script>
<div class='bg-section'>
	<div class='width-reg'>
		<h1 class='headline' style='font-size:38px;'>Recent Sales</h1>
		<div id='scrl-wrap' style="visibility:hidden;">
				<div id='scrl'>
					<?php
					$ps = mysql_query("SELECT * FROM `db_list` ORDER BY `sold` DESC LIMIT 0,200;") or die(mysql_error());
					while ($r = mysql_fetch_array($ps))
					{
						echo "<div class='dvd'><div class='dvd-inlay' style='background: url(\"img/product-imgs/{$r['id']}.jpg\"); background-size:121px 174px;'></div>";
						echo "<div class='sell-btn' style='width:92%; margin-top:18px;' onclick='goToProduct({$r['id']});'>Sell Yours!</div></div>";
					}
					?>
				</div>
			</div>
		</div>
		<img id='ajax-loader' style='margin-left:49%; position:relative; top:-250px;' src='img/ajax-load.gif'>
	</div>
</div>
<?php
include_once "include/footer.php";
?>
