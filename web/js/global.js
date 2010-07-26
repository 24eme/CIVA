/**
 * Fichier : global.js
 * Description : fonctions JS génériques
 * Auteur : Hamza Iqbal - hiqbal[at]actualys.com
 * Copyright: Actualys
 ******************************************/

/**
 * Initialisation
 ******************************************/
$(document).ready( function()
{
	rolloverImg();
	videInputFocus();
});

/**
 * Rollover
 ******************************************/
var rolloverImg = function()
{
	preloadRolloverImg();
	
	$(".rollover").hover
	(
		function () {$(this).attr( 'src', rolloverNewImg($(this).attr('src')) );}, 
		function () {$(this).attr( 'src', rolloverOldimage($(this).attr('src')) );}
	);
}
 
var preloadRolloverImg = function()
{
	$(window).bind('load', function()
	{
		$('.rollover').each( function()
		{
			$('<img>').attr( 'src', rolloverNewImg( $(this).attr('src') ) );
		});
	});
}

var rolloverNewImg = function(src)
{ 
	return src.substring(0, src.search(/(\.[a-z]+)$/) ) + '_on' + src.match(/(\.[a-z]+)$/)[0]; 
}

var rolloverOldimage = function(src)
{ 
	return src.replace(/_on\./, '.'); 
}

/**
 * Vide la valeur des champs input au focus
 ******************************************/
var videInputFocus = function()
{
	var input = $('input.input_focus[value!=""]');
	
	input.each( function()
	{
		$(this).focus( function() { if(this.value == this.defaultValue) this.value=''; });	
		$(this).blur( function() { if(this.value == '') this.value=this.defaultValue; });
	});
};

/**
 * Colonnes de même hauteur
 ******************************************/
var hauteurEgale = function(blocs)
{
	var hauteurMax = 0;
	$(blocs).each(function()
	{
		var hauteur = $(this).height();
		if(hauteur > hauteurMax) hauteurMax = hauteur;
	});
	$(blocs).height(hauteurMax);
};