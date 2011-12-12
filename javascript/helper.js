function showPopUp(window, action){	
	if(action == 0){
		showBlack(0);
		$('#' + window).fadeOut('fast');
	}
	if(action == 1){
		showBlack(1);
		$('#' + window).fadeIn('fast');
	}
}

function showBlack(act){
	if(act == 0){
		$('#blackback').fadeOut('fast');
	} else {
		$('#blackback').fadeIn('fast');
	}
}

function showMoreProv(window){
	var doc = document.getElementById(window).style.display;
	if(doc == 'block'){
		$('#' + window).slideUp('fast');
	}
	if(doc == 'none'){
		$('#' + window).slideDown('fast');
	}
}

function swapButtonHover(buttonid, action){
	if(action == 1){
		$('#' + buttonid).css('backgroundColor', '#fff');
		$('#' + buttonid).css('color', '#4AA0CF');
	}else{
		$('#' + buttonid).css('backgroundColor', '#4AA0CF');
		$('#' + buttonid).css('color', '#fff');
	}
}


function validateRecoForm(){
  validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
  strEmail1 = document.getElementById('remail1').value;
  strEmail2 = document.getElementById('remail2').value;
  
	if(document.getElementById('rnombre1').value == ""){
		alert('Ingrese el nombre de al menos 1 amigo.');
		return false;
	}
	if(strEmail1.search(validRegExp) == -1){
		alert('Ingrese un email valido.');
		return false;
	}
	
	if(document.getElementById('rnombre2').value != ""){
		if(strEmail2.search(validRegExp) == -1){
			alert('Ingrese un email valido.');
			return false;
		}
	}
	return true;
}

function validateSubsFormTop(){
 validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
  strEmail1 = document.getElementById('subemail2').value;	
	if(strEmail1.search(validRegExp) == -1){
		alert('Ingrese un email valido.');
		return false;
	}
	return true;
}

function validateSubsForm(){
 validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
  strEmail1 = document.getElementById('subemail').value;	
	if(strEmail1.search(validRegExp) == -1){
		alert('Ingrese un email valido.');
		return false;
	}
	return true;
}

function check_login_form() {
	var login_email = g("login_email");
	var login_pass = g("login_pass");
	if(login_email.value.indexOf("@") == -1 || login_email.value.indexOf(".") == -1) {
	alert("Por favor, ingrese un email como por ejemplo: miki@gmail.com");
	login_email.focus();
	login_email.select();
	return false;
	}
	if(login_pass.value == "") {
	alert("Por favor, escriba su password.");
	login_pass.focus();
	return false;
	}
	return true;
}
function check_login_form2() {
	var login_email = g("login_email2");
	var login_pass = g("login_pass2");
	if(login_email.value.indexOf("@") == -1 || login_email.value.indexOf(".") == -1) {
	alert("Por favor, ingrese un email como por ejemplo: miki@gmail.com");
	login_email.focus();
	login_email.select();
	return false;
	}
	if(login_pass.value == "") {
	alert("Por favor, escriba su password.");
	login_pass.focus();
	return false;
	}
	return true;
}

/* override */
function showProductThumbImage(ThumbIndex) {
	$('.ProductThumbImage img').attr('src', ThumbURLs[ThumbIndex]);
	$('.ProductThumbImage img').attr('alt', ProductImageDescriptions[ThumbIndex]);


	CurrentProdThumbImage = ThumbIndex;
	ShowVariationThumb = false;
	highlightProductTinyImage(ThumbIndex);
	if(ShowImageZoomer) {
		$('.ProductThumbImage a').attr("href", ZoomImageURLs[ThumbIndex]);
		$('.ProductThumbImage a').css({'cursor':'pointer'});
	}
}

function highlightProductTinyImage(ThumbIndex) {
	$('.ProductTinyImageList li').css('border', '1px solid gray');
	$('.ProductTinyImageList li .TinyOuterDiv').css('border', '2px solid white');

	$('#TinyImageBox_'+ThumbIndex).css('border', '1px solid #075899');
	$('#TinyImageBox_'+ThumbIndex+' .TinyOuterDiv').css('border', '2px solid #075899');
}


function initiateImageCarousel() {

	if(!$('.ImageCarouselBox').is(':visible')) {
		var seeMoreImageHeight = $("#ProductDetails .SeeMorePicturesLink").height();
		$("#ProductDetails .ProductThumb").width(ProductThumbWidth+20);
		$("#ProductDetails .ProductThumb").height(ProductThumbHeight+seeMoreImageHeight+10);
		return false;
	}

	highlightProductTinyImage(0);

	var carouselHeight = $("#ProductDetails .ProductTinyImageList").height();
	$("#ProductDetails .ProductThumb").width(ProductThumbWidth+20);
	$("#ProductDetails .ProductThumb").height(ProductThumbHeight+carouselHeight+10);

	var CarouselImageWidth = $('#ProductDetails .ProductTinyImageList > ul > li').outerWidth(true);

	$("#ImageScrollPrev").show();
	var CarouselButtonWidth =  $("#ProductDetails #ImageScrollPrev").outerWidth(true);
	$("#ImageScrollPrev").hide();

	var MaxCarouselWidth = $("#ProductDetails .ProductThumb").width() - (CarouselButtonWidth * 2);
	var MaxVisibleTinyImages = Math.floor(MaxCarouselWidth/CarouselImageWidth);

	if (MaxVisibleTinyImages<=0) {
		MaxVisibleTinyImages = 1;
	}

	var visible = MaxVisibleTinyImages;

	if (ThumbURLs.length <= MaxVisibleTinyImages) {
		visible = ThumbURLs.length;
		CarouselButtonWidth = 0;
	} else {
		$("#ImageScrollPrev").show();
		$("#ImageScrollNext").show();
	}

	var scroll = Math.round(visible/2);

	if($('#ProductDetails .ProductTinyImageList li').length > 0) {
		$("#ProductDetails .ProductTinyImageList").jCarouselLite({
			btnNext: ".next",
			btnPrev: ".prev",
			visible: visible,
			scroll: scroll,
			circular: false,
			speed: 200
		});
	}

	// end this floating madness
	$('#ImageScrollNext').after('<br clear="all" />');

	// pad the carousel box to center it
	$('#ProductDetails .ImageCarouselBox').css('padding-left', Math.floor(($("#ProductDetails .ProductThumb").width() - (visible * CarouselImageWidth) - (2 * CarouselButtonWidth)) / 2));

	// IE 6 doesn't render the carousel properly, the following code is the fix for IE6
	if($.browser.msie && $.browser.version.substr(0,1) == 6) {
		$("#ProductDetails .ProductTinyImageList").width($("#ProductDetails .ProductTinyImageList").width()+4);
		var liHeight = $("#ProductDetails .ProductTinyImageList li").height();
		$("#ProductDetails .ProductTinyImageList").height(liHeight+2);
	}
}

function validateValidarCuponesForm(){
	if(document.getElementById('cod1').value == ""){
		alert('Ingrese el codigo 1.');
		return false;
	}
	if(document.getElementById('cod2').value == ""){
		alert('Ingrese el codigo 2.');
		return false;
	}
	if(document.getElementById('validarcuponessent').value != 1){
		return false;
	}	
	return true;
}