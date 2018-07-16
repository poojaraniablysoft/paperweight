<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Custom Html</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Custom Html</h2></div>
	<section class="box">
		
		<div class="box_content clearfix toggle_container">
			<?php echo $frmCust->getFormTag(); ?>
			<div class="setting-form-wrapper">
				<h2>Custom HTML Tags</h2>
				<div class="settings_form">
					<table width="100%" class="formTable">
						<tbody>
						<tr>
						<td>Custom HTML for header</td>
						<td><?php echo $frmCust->getFieldHtml('custom_html_head');?></td>
						</tr>
						<tr>
						<td>Custom HTML for footer</td>
						<td><?php echo $frmCust->getFieldHtml('custom_html_footer');?></td>
						</tr>												
						</tbody>
					</table>
				</div>
				<table width="100%" class="formTable">
				<tr>
				<td></td>
				<td><?php echo $frmCust->getFieldHtml('btn_submit');?></td>
				</tr>
				</table>
				</div>
			</form>
			<?php echo $frmCust->getExternalJs();?>
		</div>
	</section>
</section>