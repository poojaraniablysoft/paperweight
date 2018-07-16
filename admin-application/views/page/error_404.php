<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($_SESSION)."</pre>";exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php
	writeMetaTags();
	echo Syspage::getJsCssIncludeHtml(false);
	?>
	
	<script language="javascript" type="text/javascript" src='<?php echo CONF_WEBROOT_URL; ?>public/LiveEditor/scripts/innovaeditor.js'></script>
	
</head>
<body>
	<div id="wrapper">
		<header id="header">

			<section class="topBar">
				<figure class="logo"><a href="<?php echo generateUrl('home');?>"><img src="<?php CONF_WEBROOT_URL;?>/images/logo.png" alt="Legitpapers" title="Legitpapers"></a></figure>
				<div class="right">
				
					<div class="timesection">
						<div class="date"><?php $date = explode(" ",addTimezone(date("l M d, Y, H:i"), CONF_TIMEZONE));echo $date['0'];?></div><div class="time"><?php echo $date['1'];?></div>
					</div>
					<?php if(isset($_SESSION['logged_admin'])) {?>
						<ul class="topLinks">
							<li>
								<a href="#" class="user"><span class="arrow"></span>Welcome <span class="redtext"> <?php echo ucfirst($_SESSION['logged_admin']['admin_username']); ?></span></a>
								<div class="dropWrapper">
									<div class="inner">
										<ul class="dropLinks">
											<li><a href="<?php echo generateUrl('configurations');?>">My Account</a></li>
											<li><a href="<?php echo generateUrl('admin', 'logout'); ?>">Logout</a></li>
										</ul>
									</div>
								</div>							
							</li>
						</ul>
					<?php } ?>
				</div>
			</section>
		</header>	
	
		<!-- body -->
		<div id="body">
    
			<section class="rightPanel wide">
			
				<section class="box">
				
					<!--error section-->
						<div class="errorWrap clearfix">
							<div class="textwrap">
								<div class="text">
									<span>404</span>
									<div class="overtext">
										<h2>Error</h2>
										<h5><span>Sorry,</span> the page you <br />requested could not be found</h5>
									</div>
								</div>
								<img src="/images/error1.png" alt="" class="errorpic" />
							</div>
							<div align="center">
							<a href="<?php echo CONF_WEBROOT_URL;?>" class="buttonGreen">Back to Home</a>
							</div>
						</div>
					<!--error section-->
					
				</section>
			
		   </section> 
    
		</div>
		<!--body end here-->
	 
	</div>
	<!--wrapper end here-->
	  
		<!--footer start here-->
		<footer id="footer">
				 <p class="left">Note: All the times are according to server time. Current server time is <?php echo addTimezone(date("l M d, Y, H:i"), CONF_TIMEZONE);?></p>
				 <p class="right"><a href="<?php echo generateUrl('home');?>">Paperweight</a></p>
		</footer>
		<!--footer end here-->
</body>
</html>