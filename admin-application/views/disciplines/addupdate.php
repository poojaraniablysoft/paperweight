<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('disciplines');?>">Manage Disciplines</a></li>
		<li>Add/Update Discipline</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Add/Update Discipline</h2></div>
	<?php echo Message::getHtml(); ?>
	<section class="box filterbox">
		<div class="box_head"><h3>Discipline Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				$result['discipline_name'] = html_entity_decode( $result[ 'discipline_name' ] );
				$frmdisc->fill($result);
				echo $frmdisc->getFormHtml();
			?>
		</div>
	</section>
</section>