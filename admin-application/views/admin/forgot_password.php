<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo CONF_WEBSITE_NAME; ?> Forgot Password</title>
	<?php 
	echo Syspage::getJsCssIncludeHtml(false);
	?>
	<link rel="shortcut icon" href="<?php echo CONF_WEBROOT_URL;?>images/favicon.ico">
	<link rel="apple-touch-icon image_src" href="<?php echo CONF_WEBROOT_URL;?>images/favicon.png">
	<script language="javascript">
		$(document).ready(function(){document.frmForgotPassword.admin_email.focus();});
	</script>
	<script type="text/javascript" language="javascript" src="/public/includes/functions.js.php"></script>
	<script type="text/javascript" language="javascript" src="/public/includes/form-validation.js.php"></script>
</head>
<body class="frontbg">
	<div id="common_msg"><?php global $msg;		echo $msg->display();?></div>
	<div class="frontForms">
		<?php
			$logoUrl  = generateUrl('image', 'logo', array( 'meduim', CONF_WEBSITE_LOGO ));
		?>
    	<div class="frontLogo"><img src="<?php echo $logoUrl;?>"></div>
        <div class="whitesection">
		
        	<?php
				$frm_password->captionInSameCell(true); 
				//$frm_password->setJsErrorDisplay('afterfield');
				echo $frm_password->getFormHtml();
			?>
           <p class="ptext">Go to Login Page? <a href="<?php echo generateUrl('admin','loginform');?>">Click here</a></p>

        </div>
    </div>


</body>

</html>