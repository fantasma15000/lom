var xmlHttp;
/*JavaScript*/

function GetXmlHttpObject(){ 
	var objXMLHttp=null;
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	return objXMLHttp;
}

function sendRecommend(){
	 xmlHttp=GetXmlHttpObject();
	 var tunombre = document.getElementById('tunombre').value;
	 var tuemail = document.getElementById('tuemail').value;
	 var rnombre1 = document.getElementById('rnombre1').value;
	 var remail1 = document.getElementById('remail1').value;
	 var rnombre2 = document.getElementById('rnombre2').value;
	 var remail2 = document.getElementById('remail2').value;
	 var url = "ajax/recomendar.php?save=1&tunombre=" + tunombre + "&tuemail=" + tuemail + "&rnombre1=" + rnombre1 + "&remail1=" + remail1 + "&rnombre2=" + rnombre2 + "&remail2=" + remail2;
	 xmlHttp.onreadystatechange=savedRecomendar;
	 xmlHttp.open("GET",url,true);
	 xmlHttp.send(null);
}

function savedRecomendar(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		var bringcode = xmlHttp.responseText;
		if(bringcode){
			alert('El email fue enviado.');
		} else {
			alert('El email no pudo ser enviado.');
		}
		document.getElementById('tunombre').value = "";
		document.getElementById('tuemail').value = "";
		document.getElementById('rnombre1').value = "";
		document.getElementById('remail1').value = "";
		document.getElementById('rnombre2').value = "";
		document.getElementById('remail2').value = "";
		showPopUp('recomendar', 0);			
	}
}

function sendSuscribe(){
	 xmlHttp=GetXmlHttpObject();
	 var email = document.getElementById('subemail').value;
	 var ciudad = document.getElementById('tuciudad').value;
	 var url = "ajax/suscribe.php?save=1&email=" + email + "&ciudad=" + ciudad;
	 xmlHttp.onreadystatechange=savedSuscribe;
	 xmlHttp.open("GET",url,true);
	 xmlHttp.send(null);
}

function savedSuscribe(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		var bringcode = xmlHttp.responseText;
		if(bringcode){
			alert('El email fue suscripto.');
		} else {
			alert('El email no pudo ser suscripto.');
		}
		document.getElementById('subemail').value = "";
		document.getElementById('tuciudad').value = "18";
		showPopUp('inicioPopUp', 0);
	}
}


function sendSuscribeTop(){
	 xmlHttp=GetXmlHttpObject();
	 var email = document.getElementById('subemail2').value;
	 var ciudad = document.getElementById('ciudadtop').value;
	 var url = "ajax/suscribe.php?save=1&email=" + email + "&ciudad=" + ciudad;
	 xmlHttp.onreadystatechange=savedSuscribeTop;
	 xmlHttp.open("GET",url,true);
	 xmlHttp.send(null);
}

function savedSuscribeTop(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		var bringcode = xmlHttp.responseText;
		if(bringcode){
			alert('El email fue suscripto.');
		} else {
			alert('El email no pudo ser suscripto.');
		}
		document.getElementById('subemail2').value = "";
		document.getElementById('ciudadtop').value = "18";
		showPopUp('inicioPopUp', 0);
	}
}
