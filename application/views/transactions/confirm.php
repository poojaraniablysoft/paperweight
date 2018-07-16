<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo Utilities::getLabel( 'L_504_Error' ); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta name="description" content="">
<meta name="author" content="">
<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/error.css" />

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!-- favicon ================================================== -->
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
	<?php
		$logoUrl = generateUrl('image', 'logo', array( CONF_LOGIN_LOGO, 'FULL' ));
	?>
      <div id="whoops"> <img src="<?php echo $logoUrl;?>" alt="logo" class="animated swing"></div>
    </div>
    <div class="span8">
      <h1><?php echo Utilities::getLabel( 'L_Thank_you' ); ?></h1>
      <h2>
		<?php// if ( $payment_info['payment_status'] == 'Completed' ) { ?>
					<?php echo Utilities::getLabel( 'L_Wallet_Info_After_Complete_Payment' ); ?>
				<?php //} else if ( $payment_info['payment_status'] == 'Pending' ) { ?>
					<?php //echo Utilities::getLabel( 'L_The_payment_is_pending' ); ?>&nbsp;<?php //echo $payment_info['pending_reason']; ?>
				<?php //} else { ?>
					<?php //echo Utilities::getLabel( 'L_Payment_failed' ); ?>
				<?php //} ?>
		</h2>
       <div class="menu">
		<p> <a href="javascript:void(0);" onclick="goBack()"><?php echo Utilities::getLabel( 'L_Go_Back' ); ?></a> </p>
		<hr>
     
        <p> <?php echo sprintf(CONF_FOOTER_COPYRIGHT,date("Y"), CONF_WEBSITE_NAME)?></p>
      </div>
    </div>
    <div class="span2"></div>
  </div>
</div>
<script type="text/javascript"> 
	function goBack(){
		window.location.href = '<?php echo $refrer_url; ?>';
		//window.history.back();
		return false;
	}
</script>
</body>
</html>


 