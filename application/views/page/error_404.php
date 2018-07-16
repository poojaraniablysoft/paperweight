<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo Utilities::getLabel( 'L_404_Page_Title' ); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta name="description" content="">
<meta name="author" content="">
<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/error.css" />

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!-- favion ================================================== -->
<?php
	$fav_icon = generateUrl('image', 'fav_icon', array( CONF_WEBSITE_FAV_ICON ));
?>
<link rel="shortcut icon" href="<?php echo $fav_icon;?>">
<link rel="apple-touch-icon image_src" href="<?php echo $fav_icon;?>">
</head>
<body>
<div class="container">
  <div class="row">
    <div class="span12">
      <div id="whoops"> 
		<?php
			$logoUrl = generateUrl('image', 'logo', array( CONF_LOGIN_LOGO, 'FULL' ));
		?>  
		  <img src="<?php echo $logoUrl;?>" alt="logo" class="animated swing"></div>
    </div>
    <div class="span8">
      <h1><?php echo Utilities::getLabel( 'L_404' ); ?></h1>
      <h2><?php echo Utilities::getLabel( 'L_404_Page_Description' ); ?></h2>
		<p>
			<a href="javascript:void(0);" onclick="goBack();"><?php echo Utilities::getLabel( 'L_Go_back' ); ?></a>, <?php echo Utilities::getLabel( 'L_or' ); ?> <?php echo sprintf(Utilities::getLabel( 'L_401_Return_Text' ), CONF_SITE_URL, CONF_WEBSITE_NAME); ?>
		</p>
      <hr>
      <div class="menu">
        <p><?php echo sprintf(CONF_FOOTER_COPYRIGHT,date("Y"), CONF_WEBSITE_NAME)?></p>
      </div>
    </div>
    <div class="span2"></div>
  </div>
</div>
<script type="text/javascript"> 
	function goBack(){
		window.history.back();
		return false;
	}
</script>
</body>
</html>