<?php include("cselfunc.php"); ?>
<!DOCTYPE HTML>
<html>
<head>
<title>CSE Games Lending</title>
<link href="http://www.uow.edu.au/content/groups/webasset/@web/documents/siteelement/css_uow_font.css" rel="stylesheet" type="text/css" media="screen">
<link href="http://www.uow.edu.au/content/groups/webasset/@web/documents/siteelement/css_uow_responsive.css" rel="stylesheet" type="text/css" media="screen">
<style>
	.infobox {
		float: left;
		font-size: small;
		margin-top: 2px;
		margin-left: 10px;
		padding: 2px;
		width: 30%;
	}
	
	.flabel {
		float: left;
		width: 22%;
	}
	
	input {
		float: left;
		width: 13%;

	}
	
	#container {
		width: 75%;
		margin-left:auto;
		margin-right:auto;
		margin-top: 20px;
		margin-bottom: 20px;
	}
	
	#mform {
		margin-top: 20px;
		margin-bottom: 20px;
	}
	
	.button {
		border: 1px solid black;
		width: 13%;
		border-collapse: collapse;
		padding: 5px;
		float: left;
		margin-left: -1px;
	}
	
	.fbutton {
		border: 1px solid black;
		width: 13%;
		border-collapse: collapse;
		padding: 5px;
		float: left;
	}
	
	#datatab {
		margin-top: -1px;
		padding: 5px;
	}
	
	body {
		overflow-y: scroll;
	}

</style>
<script language="javascript" type="text/javascript">
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>
<script type="text/javascript">
function showStudent(str)
{
	var sd = document.getElementById("stuDetails");

	if (str=="") {
		sd.innerHTML="";
		setStyle(sd, 'bla');
		return;
	} 

	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			sd.innerHTML=xmlhttp.responseText;
			if (xmlhttp.responseText == "Student not found" || xmlhttp.responseText == "Error in database") {
				setStyle(sd, 'err');
			} else {
				setStyle(sd, 'ok');
			}
		}
	}

	xmlhttp.open("GET","studentlookup.php?q="+str,true);
	xmlhttp.send();
}

function showItem(str)
{
	var si = document.getElementById("itemDetails");

	if (str=="") {
		si.innerHTML="";
		setStyle(si, 'bla');
		return;
	} 

	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			si.innerHTML=xmlhttp.responseText;
			if (xmlhttp.responseText == "Item not found" || xmlhttp.responseText == "Error in database") {
				setStyle(si, 'err');
			} else {
				setStyle(si, 'ok');
			}
		}
	}

	xmlhttp.open("GET","itemlookup.php?q="+str,true);
	xmlhttp.send();
}

function showAllItems()
{
	var dt = document.getElementById("datatab");
	var sal = document.getElementById("showall");
	if (dt.innerHTML != "") {
		dt.innerHTML = "";
		setStyle(dt, 'nor');
	}
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				dt.innerHTML=xmlhttp.responseText;
			}
		}
	

		xmlhttp.open("GET","showallitems.php",true);
		xmlhttp.send();
}

function checkOut(str1,str2)
{
	var dt = document.getElementById("datatab");
	var err = 0;
	
	dt.innerHTML = "";
	setStyle(dt, 'nor');

	if (str1=="" || str2=="") {
		dt.innerHTML="Please enter a student or item";
		setStyle(dt, 'err');
		err = 1;
	} 
	
	if (!err) {
	
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				dt.innerHTML=xmlhttp.responseText;
				if (xmlhttp.responseText == "Student not found" || xmlhttp.responseText == "Error in database") {
					setStyle(dt, 'err');
				}
			}
		}

		xmlhttp.open("GET","checkout.php?s="+str1+"&i="+str2,true);
		xmlhttp.send();
		
		document.getElementById("sbc").value = "";
		document.getElementById("item").value = "";
		
	}
}

function setStyle(str, type) {
	if (type == 'nor') {
		str.style.border = '1px solid #000';
		str.style.background = '#fff';
	} else if (type == 'ok') {
		str.style.border = '1px solid #8cac5e';
		str.style.background = '#e5ffbf';
	} else if (type == 'err') {
		str.style.border = '1px solid #ff0000';
		str.style.background = '#ffcccc';
	} else if (type == 'bla') {
		str.style.background = '#fff';
		str.style.border = '0';
	}
}

function showCheckedOutItems()
{
	var dt = document.getElementById("datatab");
	var sal = document.getElementById("chkin");
	dt.innerHTML = "";	
	setStyle(dt, 'nor');
		
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				dt.innerHTML=xmlhttp.responseText;
			}
		}
	

		xmlhttp.open("GET","showcheckedout.php",true);
		xmlhttp.send();
}

function checkIn(str) {
	var dt = document.getElementById("datatab");
	dt.innerHTML = "";	
	setStyle(dt, 'nor');
	//var st = document.getElementById(sid);
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				dt.innerHTML=xmlhttp.responseText;
				if (xmlhttp.responseText == "Student not found" || xmlhttp.responseText == "Error in database") {
					setStyle(dt, 'err');
				}
			}
		}

		xmlhttp.open("GET","checkin.php?t="+str,true);
		xmlhttp.send();
}
		
function showReport() {
		

	var dt = document.getElementById("datatab");
	setStyle(dt, 'nor');
	dt.innerHTML = "";
	dt.innerHTML = "<iframe id='rep' scrolling='no' frameborder='0' width='100%' align='middle' onload='javascript:resizeIframe(this);' src='http://uc.unicentre.uow.edu.au/feedback/cse/lending/checkoutrep.php'></iframe>";
	/*
	if (rf.style.display == "block") {
		rf.style.display = "none";
	} else {
		rf.style.display = "block";
	}*/
	//setStyle(dt, 'nor');
	
	
		
		/*if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				dt.innerHTML=xmlhttp.responseText;
			}
		}
	

		xmlhttp.open("GET","checkoutrep.php",true);
		xmlhttp.send();*/
}		
	
</script>
</head>
<body>
<?php
	
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		$me = $_SERVER['PHP_SELF'];
	}
?>
<div id="container">
<div style="margin-bottom: 10px;"><h1>CSE Games Lending</h1></div>
<div id="mform">
<form id="cse_ls" action="<?php echo $me; ?>" method="post" enctype="multipart/form-data">
    <div class="flabel">Student Number / Barcode</div>
	<input type="text" class="text-input" name="sbc" id="sbc" onkeyup="showStudent(this.value);" />
	<div id="stuDetails" class="infobox"></div><div style="clear:both"></div>
	<div class="flabel">Item Barcode</div>
	<input type="text" class="text-input" name="item" id="item" onkeyup="showItem(this.value);" />
	<div id="itemDetails" class="infobox"></div><div style="clear:both"></div>

</div>
<div style="clear:both"></div>
<div id="chkin" class="fbutton"><a href='#' onClick="showCheckedOutItems();">Check In</a></div>
<div id="chkout" class="button"><a href='#' onClick="checkOut(cse_ls.sbc.value, cse_ls.item.value);">Check Out</a></div>
<div id="showall" class="button"><a href='#' onClick="showAllItems();">All Items</a></div>
<div id="report" class="button"><a href='#' onClick="showReport();">Report</a></div>
<div style="clear:both"></div>
<div id="datatab"></div>
<div id="repf" style="display: none;"></div>
</div>
</form> 
</body>
</html>