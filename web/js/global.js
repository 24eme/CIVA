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

/**
 * Colonnes de même hauteur
 ******************************************/
function var_dump(arr,level)
{
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	console.log(dumped_text);
};







;(function(h){var m=h.scrollTo=function(b,c,g){h(window).scrollTo(b,c,g)};m.defaults={axis:'y',duration:1};m.window=function(b){return h(window).scrollable()};h.fn.scrollable=function(){return this.map(function(){var b=this.parentWindow||this.defaultView,c=this.nodeName=='#document'?b.frameElement||b:this,g=c.contentDocument||(c.contentWindow||c).document,i=c.setInterval;return c.nodeName=='IFRAME'||i&&h.browser.safari?g.body:i?g.documentElement:this})};h.fn.scrollTo=function(r,j,a){if(typeof j=='object'){a=j;j=0}if(typeof a=='function')a={onAfter:a};a=h.extend({},m.defaults,a);j=j||a.speed||a.duration;a.queue=a.queue&&a.axis.length>1;if(a.queue)j/=2;a.offset=n(a.offset);a.over=n(a.over);return this.scrollable().each(function(){var k=this,o=h(k),d=r,l,e={},p=o.is('html,body');switch(typeof d){case'number':case'string':if(/^([+-]=)?\d+(px)?$/.test(d)){d=n(d);break}d=h(d,this);case'object':if(d.is||d.style)l=(d=h(d)).offset()}h.each(a.axis.split(''),function(b,c){var g=c=='x'?'Left':'Top',i=g.toLowerCase(),f='scroll'+g,s=k[f],t=c=='x'?'Width':'Height',v=t.toLowerCase();if(l){e[f]=l[i]+(p?0:s-o.offset()[i]);if(a.margin){e[f]-=parseInt(d.css('margin'+g))||0;e[f]-=parseInt(d.css('border'+g+'Width'))||0}e[f]+=a.offset[i]||0;if(a.over[i])e[f]+=d[v]()*a.over[i]}else e[f]=d[i];if(/^\d+$/.test(e[f]))e[f]=e[f]<=0?0:Math.min(e[f],u(t));if(!b&&a.queue){if(s!=e[f])q(a.onAfterFirst);delete e[f]}});q(a.onAfter);function q(b){o.animate(e,j,a.easing,b&&function(){b.call(this,r,a)})};function u(b){var c='scroll'+b,g=k.ownerDocument;return p?Math.max(g.documentElement[c],g.body[c]):k[c]}}).end()};function n(b){return typeof b=='object'?b:{top:b,left:b}}})(jQuery);