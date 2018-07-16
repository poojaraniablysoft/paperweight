<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--leftPanel end here--> 
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('user','user_listing');?>">User Management</a></li>
		<li><a href="<?php echo generateUrl('user', 'transaction_details', array($user_id)); ?>">User Wallet Transactions</a></li>
		<li>Add Transaction</li>
	</ul>	
	<div class="title"><h2>Add Transaction</h2></div>
	<?php echo Message::getHtml(); ?>
	<section class="box">
		<div class="box_head">
			<h3>Add Transaction</h3>
		</div>
		<div class="box_content clearfix">
			<?php echo $frm->getFormHtml(); ?>
		</div>
	</section>
</section>