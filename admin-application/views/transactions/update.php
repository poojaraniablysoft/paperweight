<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--leftPanel end here--> 
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('transactions','withdrawal_requests')?>">Funds Withdrawal Requests</a></li>
		<li>Update</li>
	</ul>	
	<div class="title"><h2>Funds Withdrawal Requests</h2></div>
	<?php echo Message::getHtml(); ?>
	<section class="box filterbox">
		<div class="box_head">
			<?php if ($req_status == 0) { ?>
				<h3>Withdrawal Request Form</h3>
			<?php } else { ?>
				<h3>Transaction Summary</h3>
			<?php } ?>
		</div>
		<div class="box_content clearfix">
			<?php echo $frm->getFormHtml(); ?>
		</div>
	</section>
</section>