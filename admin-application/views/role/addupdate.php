<?php  
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li> <a href="<?php echo generateUrl('role');?>">Admin Roles</a></li>
		<li>Admin Role Form</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Form</h2></div>
	<section class="box filterbox">
			<div class="box_content clearfix">
				<?php 
					$frm->fill($data);
					echo $frm->getFormHtml(); 
				?>
			</div>
		</div>
	</section>
</section>