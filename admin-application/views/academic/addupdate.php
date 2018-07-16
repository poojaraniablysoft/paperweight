<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""></a></li>
		<li><a href="<?php echo generateUrl('academic');?>"><?php echo Utilities::getLabel( 'L_Manage_Academic_Degrees' ); ?></a></li>
		<li><?php echo Utilities::getLabel( 'L_Add/Update_Degrees' ); ?></li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2><?php echo Utilities::getLabel( 'L_Add/Update_Degrees' ); ?></h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3><?php echo Utilities::getLabel( 'L_Degrees_Form' ); ?></h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				$frmAcademic->fill($result);
				echo $frmAcademic->getFormHtml();
			?>
		</div>
	</section>
</section>
