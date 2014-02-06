function save_img(url,id,div)
{
	var xmlhttp;
	div.style.display = "none";
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			if(xmlhttp.responseText == "false")
			{
				div.style.display = "block";
				alert(xmlhttp.responseText);
			}
		}
	}
	xmlhttp.open("GET","ajax.php?dvd_id="+id+"&poster_url="+url,true);
	xmlhttp.send();
}
function save_pla(pla,id,div)
{
	var xmlhttp;
	div.style.display = "none";
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			if(xmlhttp.responseText !== "true")
			{
				div.style.display = "block";
			}
		}
	}
	xmlhttp.open("GET","ajax.php?dvd_id="+id+"&platform="+pla,true);
	xmlhttp.send();
}
function duplicate(id1,id2,div) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			div.innerText = "Added!";
			setTimeout(function(){div.style.display = "none";},500)
		}
	}
	xmlhttp.open("GET","ajax.php?dup_id1="+id1+"&dup_id2="+id2,true);
	xmlhttp.send();
}
function diff(id1,id2,div) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			div.innerText = "Added!";
			setTimeout(function(){div.style.display = "none";},500)
		}
	}
	xmlhttp.open("GET","ajax.php?diff_id1="+id1+"&diff_id2="+id2,true);
	xmlhttp.send();
}
