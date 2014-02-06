<?php
	include_once "include/header.php";
?>
<div id='home-splash-wrap'>
	<div id='home-splash'>
		<h1 class='headline'style='font-size:36px; line-height:36px;'>Get the best price for your DVDs, Books and Games.</h1>
		<div id='search-wrap'>
			<div id='search-select' onclick='showDropWindow("year-select");'></div>
			<form id='home-search-form' autocomplete="off" action='search.php' method='get'>
				<input name='q' type='text' onkeyup="showResult(this.value);" id='search-text' placeholder='search'>
				<input name='type' type='hidden' id='sort-para' value='0'>
				<div id='search-filter'></div>
				<div id="livesearch">
				</div>
			</form>
			<div id='search-button' onclick='submitForm("home-search-form")'>
				Search
			</div>
		</div>
		<div class='sell-btn' onclick='barcodeScan();' style='margin: 0 auto; float:none; width:300px; border-radius:25px;'>
			Scan Barcode Using Your Webcam<img src='img/webcam.png' style='margin:9px 14px 9px -18px; float:right;' alt='webcam'>
		</div>
		<div id='barcode-scan-wrapper'>	
			<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
			<script>
				$(document).ready(function() {
					$("#webcam").scriptcam({useMicrophone:false,cornerRadius:6,onWebcamReady:onWebcamReady});
					console.log = function(){return true}
					window.alert = function(){return true}
					onerror = function(){return true}
				});
				function changeCamera(){$.scriptcam.changeCamera($('#cameraNames').val());}
				function onError(errorId,errorMsg){return true;}			
				function onWebcamReady(cameraNames,camera,microphoneNames,microphone,volume) {
					$.each(cameraNames, function(index, text) {
						$('#cameraNames').append( $('<option></option>').val(index).html(text) )
					}); 
					$('#cameraNames').val(camera);
				}
			</script>
			<div style='width:320px; float:left; margin-right:16px;'>
				<div id="webcam" style='width:320px;'>
				</div>
				<div style='margin-top:8px; width:320px;'>
					<img src="img/webcam.png" style="margin:1px 12px 0 14px; vertical-align:text-top"/>
					<select id="cameraNames" size="1" class="login-select" onChange="changeCamera()" style="border: 1px solid #ddd;">
					</select>
				</div>
			</div>
			<div style='float:right; width:249px;'>
				<div id='scan_results' style='width:247px; background-image: url("img/back.jpg"); background-size: 100% 100%; border-bottom: 1px solid #031572!important;height: 238px;border: 1px solid #1128A3; border-radius:3px; float:left;'>
				</div>
				<div class='red-button' onclick='stopScan();' style='float:right; margin:14px 0 0 0;'>&#10007; Close</div>
			</div>
		</div>
		<br>
		<div style='width:470px; margin:0 auto; height:210px;'>
			<div class='dvd'>
				<div class='dvd-inlay' style='background: url("img/product-imgs/2.jpg"); background-size:121px 174px;'></div>
				<div class='btn' style='opacity:1;' onclick='goToProduct(2);'>Sell Now</div>
			</div>
			<div class='dvd'>
				<div class='dvd-inlay' style='background: url("img/product-imgs/3.jpg"); background-size:121px 174px;'></div>
				<div class='btn' style='opacity:1;' onclick='goToProduct(3);'>Sell Now</div>
			</div>
			<div class='dvd'>
				<div class='dvd-inlay' style='background: url("img/product-imgs/4.jpg"); background-size:121px 174px;'></div>
				<div class='btn' style='opacity:1;' onclick='goToProduct(4);'>Sell Now</div>
			</div>
		</div>
		<div style='width:471px; margin:0 auto; height:52px;'>
			
		</div>
		</center>
		<div class='drop-window' id='year-select' tabindex='0' style='top:245px; margin-left:128px; width:150px; height:130px; display:none;'>
			<div class='arrow-n'></div>
			<ul class='drop-list'>
				<li class='drop-list-item' id='1'>Search DVDs</li>
				<li class='drop-list-item' id='2'>Search Books</li>
				<li class='drop-list-item' id='3'>Search Games</li>
			</ul>
		</div>
	</div>
</div>
<div class='section' style='height:400px;'>
	<div class='width-reg'>
		<h1 class='headline' style='color: #349FF3; text-shadow: 0 1px 1px rgba(12, 17, 77, 0.79); margin:0; padding:30px 0 0 0; line-height:70px;'>Recent Sales and Feedback</h1>
		<div  style='width:600px; height:250px; margin-top:20px; padding:10px; float:left;'>
				<div class='dvd'>
					<div class='review-dvd-inlay' style='background: url("img/product-imgs/2.jpg"); background-size:121px 174px;'></div>
					<div class='sell-btn' style='position: relative; bottom: 35px; width: 91.9%;' onclick='goToProduct(2);'>Sold for £18.50</div>
				</div>
				<div class='dvd'>
					<div class='review-dvd-inlay' style='background: url("img/product-imgs/3.jpg"); background-size:121px 174px;'></div>
					<div class='sell-btn' style='position: relative; bottom: 35px; width: 91.9%;' onclick='goToProduct(3);'>Sold for £19.25</div>
				</div>
				<div class='dvd'>
					<div class='review-dvd-inlay' style='background: url("img/product-imgs/4.jpg"); background-size:121px 174px;'></div>
					<div class='sell-btn' style='position: relative; bottom: 35px; width: 91.9%;' onclick='goToProduct(4);'>Sold for £21.00</div>
				</div>
		</div>
		<div style='padding:10px; float:right;'>
			<div class='review-wrap'>
				<p class='review-text'>
				"Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
				Lorem Ipsum has been the industry's standard dummy text ever since the 1500s"
				</p>
				<p class='review-author'>
				Fred Bloggs, UK
				</p>
				<div class='review-rating stars-50'></div>
			</div>
			<div class='review-wrap'>
				<p class='review-text'>
				"Lorem Ipsum is simply dummy text of the printing."
				</p>
				<p class='review-author'>
				John Smith, Wales
				</p>
				<div class='review-rating stars-50'></div>
			</div>
		</div>
	</div>
</div>

<div class='section' style='background:url(img/back3.png); height:300px;'>
	<div class='width-reg'>
		<h1 class='headline' style='margin:0; padding-top:42px;'>Compare prices from many recyclers.</h1>
		<div style='margin:60px auto; width:382px;'>
			<div style='display:inline-block; margin:12px 0 0 4px; box-shadow: 0 1px 8px rgba(0, 0, 0, .3); padding: 0 10px;' class='offer-logo'>
				<img src='img/suppliers/1.jpg'>
			</div>
			<div style='display:inline-block; margin:12px 0 0 4px; box-shadow: 0 1px 8px rgba(0, 0, 0, .3); padding: 0 10px;' class='offer-logo'>
				<img src='img/suppliers/2.jpg'>
			</div>
			<div style='display:inline-block; margin:12px 0 0 4px; box-shadow: 0 1px 8px rgba(0, 0, 0, .3); padding: 0 10px;' class='offer-logo'>
				<img src='img/suppliers/3.jpg'>
			</div>
		</div>
	</div>
</div>
</body>
<?php
	include_once "include/footer.php";
?>
