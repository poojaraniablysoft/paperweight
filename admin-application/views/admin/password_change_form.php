<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
?>

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Change Password</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Change Password</h2></div>
	<section class="box filterbox">
	
		<?php
			/* @var $frmPassword Form */
			$frmPassword->setTableProperties('class="formTable" width="100%"');
			$frmPassword->setExtra('class="siteForm"');
		?>
	
		<div class="box_content clearfix space">
			<?php echo $frmPassword->getFormHtml(); ?>
		</div>
	</section>
</section>