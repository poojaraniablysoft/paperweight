<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--<script language="javascript" type="text/javascript" src="<?php// echo CONF_WEBROOT_URL; ?>innova/scripts/innovaeditor.js"></script>
<script src="<?php// echo CONF_WEBROOT_URL; ?>innova/scripts/common/webfont.js" type="text/javascript"></script>-->
<section class="rightPanel wide">
	<section class="box filterbox">
		<div class="box_content toggle_container">
			<div class="borderContainer">
				<?php $frm_html = $frmEdit->getFormHtml(); echo $frmEdit->getFormTag(); ?>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable">
						<tbody>
							<tr>
								<td width="25%">Post Title<span class="spn_must_field">*</span></td>
								<td><?php echo $frmEdit->getFieldHtml('post_title'); ?></td>
							</tr>
							<tr>
								<td width="25%">Post SEO Name<span class="spn_must_field">*</span></td>
								<td><?php echo $frmEdit->getFieldHtml('post_seo_name'); ?></td>
							</tr>
							<tr>
								<td width="25%">Post Contibutor Name</td>
								<td><?php echo $frmEdit->getFieldHtml('post_contibutor_name'); ?></td>
							</tr>							
							<tr>
								<td width="25%">Post Short Description</td>
								<td><?php echo $frmEdit->getFieldHtml('post_short_description'); ?></td>
							</tr>
							<tr>
								<td width="25%">Post Content<span class="spn_must_field">*</span></td>
								<td><?php echo $frmEdit->getFieldHtml('post_content'); ?></td>
							</tr>
							<tr>
								<td width="25%">Images</td>
								<td id="image_div"><?php echo $frmEdit->getFieldHtml('post_image_file_name[]'); ?></td>
							</tr>							
							<tr>
								<td width="25%">Post Status</td>
								<td><?php echo $frmEdit->getFieldHtml('post_status'); ?></td>
							</tr>
							<tr>
								<td width="25%">Post Comment Status</td>
								<td><?php echo $frmEdit->getFieldHtml('post_comment_status'); ?></td>
							</tr>
							
							<tr>
								<td width="25%">Categories</td>
								<td class="Categories"><?php
								foreach($categories as $categories1)
								{
									$checked = '';
									foreach($relation_category_id as $relation_category_id1)
									{
										if($relation_category_id1 == $categories1['category_id'])
										{
											$checked = 'checked="checked"';
										}
									}
									echo '<input type="checkbox" name="relation_category_id[]" id="relation_category_id" value='.$categories1['category_id'].' '.$checked.'>'.$categories1['category_title'].'<br>';
									if(isset($categories1['children']))
									{
										$checked = '';
										echo'<ul class="sub-categories">';
											foreach($categories1['children'] as $children)
											{
												$checked = '';
												foreach($relation_category_id as $relation_category_id1)
												{
													if($relation_category_id1 == $children['category_id'])
													{
														$checked = 'checked="checked"';
													}
												}
												
												echo '<li>'.'<input type="checkbox" name="relation_category_id[]" id="relation_category_id" value='.$children['category_id'].' '.$checked.'>'.$children['category_title'].'</li>';
											}
										echo '</ul>';
									}
								}
								 ?></td>
							</tr>
							
							<tr>
								<td width="25%">Meta Title</td>
								<td><?php echo $frmEdit->getFieldHtml('meta_title'); ?></td>
							</tr>
							
							<tr>
								<td width="25%">Meta Keywords</td>
								<td><?php echo $frmEdit->getFieldHtml('meta_keywords'); ?></td>
							</tr>
							
							<tr>
								<td width="25%">Meta Description</td>
								<td><?php echo $frmEdit->getFieldHtml('meta_description'); ?></td>
							</tr>
							
							<tr>
								<td width="25%">Meta Others</td>
								<td><?php echo $frmEdit->getFieldHtml('meta_others'); ?></td>
							</tr>
							
							<tr>
								<td width="25%"></td>
								<td>
									<?php echo $frmEdit->getFieldHtml('btn_submit'); ?>
									<?php //echo $frmEdit->getFieldHtml('btn_publish'); ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php echo $frmEdit->getFieldHtml('post_id'); ?>
					<?php echo $frmEdit->getFieldHtml('meta_id'); ?>
					<?php echo $frmEdit->getFieldHtml('post_removed_images'); ?>
				</form><?php echo $frmEdit->getExternalJs(); ?>
			</div>
		</div>
	</section>
</section>