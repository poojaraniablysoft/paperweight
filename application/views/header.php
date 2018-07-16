<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>

<!-- Basic Page Needs
  ================================================== -->
<meta charset="utf-8">
<title><?php echo CONF_WEBSITE_NAME; if($arr_listing['meta_title']!=""){echo ' | '.$arr_listing['meta_title'];}?></title>
<?php if($arr_listing['meta_description'] != '') {?><meta name="description" content="<?php echo $arr_listing['meta_description'];?>"><?php } ?>

<?php if($arr_listing['meta_keywords'] != '') {?><meta name="keywords" content="<?php echo $arr_listing['meta_keywords'];?>">
<?php }
if($arr_listing['other_tags'] != '') {echo html_entity_decode($arr_listing['other_tags']);}
?>

<meta name="author" content="">

<!-- Mobile Specific Metas
  ================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- CSS
  ================================================== -->
<?php 
	//writeMetaTags();
	echo Syspage::getJsCssIncludeHtml(false);
	/* if($_SERVER['REQUEST_URI'] != '/' && $_SERVER['REQUEST_URI'] != 'home' ) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/inner.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/skeleton.css" />
<?php } */
?>
<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/mobile.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<?php/*  <link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/tablet.css" />
<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/jquery.bxslider.css" /> */?>
<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
<!-- All JavaScript at the bottom, except for Modernizr and Respond.
Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries -->

<!-- Favicons
	================================================== -->

<?php
	$fav_icon = generateUrl('image', 'fav_icon', array( CONF_WEBSITE_FAV_ICON ));
?>

<link rel="shortcut icon" href="<?php echo $fav_icon;?>">
<link rel="apple-touch-icon image_src" href="<?php echo $fav_icon;?>">

<!--[if gte IE 9]>
<style type="text/css">.gradient {filter: none;}</style>
<![endif]-->
<script type="text/javascript">
/*$(document).ready(function() {
	var tmzoneName=jstz.determine().name();				
	document.cookie = "tmzoneName="+tmzoneName; 	
})	*/
</script>
<?php #date_default_timezone_set($_COOKIE['tmzoneName']); ?>
<script language="javascript" type="text/javascript" src='<?php echo CONF_WEBROOT_URL; ?>public/innova-lnk/scripts/innovaeditor.js'></script>
<?php echo CUSTOM_HTML_HEAD; ?>
</head>
<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=1522668851316365";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<div id="common_error_msg"><?php echo Message::getHtml(); ?></div>
	
	
	<!-- BEGIN LivePerson Monitor. 
	<script type="text/javascript"> window.lpTag=window.lpTag||{};if(typeof window.lpTag._tagCount==='undefined'){window.lpTag={site:'21615661'||'',section:lpTag.section||'',autoStart:lpTag.autoStart===false?false:true,ovr:lpTag.ovr||{},_v:'1.5.1',_tagCount:1,protocol:location.protocol,events:{bind:function(app,ev,fn){lpTag.defer(function(){lpTag.events.bind(app,ev,fn);},0);},trigger:function(app,ev,json){lpTag.defer(function(){lpTag.events.trigger(app,ev,json);},1);}},defer:function(fn,fnType){if(fnType==0){this._defB=this._defB||[];this._defB.push(fn);}else if(fnType==1){this._defT=this._defT||[];this._defT.push(fn);}else{this._defL=this._defL||[];this._defL.push(fn);}},load:function(src,chr,id){var t=this;setTimeout(function(){t._load(src,chr,id);},0);},_load:function(src,chr,id){var url=src;if(!src){url=this.protocol+'//'+((this.ovr&&this.ovr.domain)?this.ovr.domain:'lptag.liveperson.net')+'/tag/tag.js?site='+this.site;}var s=document.createElement('script');s.setAttribute('charset',chr?chr:'UTF-8');if(id){s.setAttribute('id',id);}s.setAttribute('src',url);document.getElementsByTagName('head').item(0).appendChild(s);},init:function(){this._timing=this._timing||{};this._timing.start=(new Date()).getTime();var that=this;if(window.attachEvent){window.attachEvent('onload',function(){that._domReady('domReady');});}else{window.addEventListener('DOMContentLoaded',function(){that._domReady('contReady');},false);window.addEventListener('load',function(){that._domReady('domReady');},false);}if(typeof(window._lptStop)=='undefined'){this.load();}},start:function(){this.autoStart=true;},_domReady:function(n){if(!this.isDom){this.isDom=true;this.events.trigger('LPT','DOM_READY',{t:n});}this._timing[n]=(new Date()).getTime();},vars:lpTag.vars||[],dbs:lpTag.dbs||[],ctn:lpTag.ctn||[],sdes:lpTag.sdes||[],ev:lpTag.ev||[]};lpTag.init();}else{window.lpTag._tagCount+=1;} </script>
	<!-- END LivePerson Monitor. -->
	<!-- page -->
	
	<script type="text/javascript">
		var DEFAULT_CURRENCY = "<?php echo CONF_CURRENCY; ?>";
	</script>
	<?php					
		$arr = explode('/',$_SERVER['REQUEST_URI']);
		global $requested_action;
	?>
	<script>
		var serverDate = "<?php echo date('Y-m-d'); ?>" ;
	</script>
 