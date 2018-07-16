<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--- popup wrapper Upload Additional Material-->

<div id="body" >
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper ">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Upload_additional_material' ); ?></h2>
                <div class="formWrap">
                    <?php echo $frm->getFormtag();?>
						<table class="uploads-file" width="100%" border="0" cellspacing="0" cellpadding="0">
						  <tr>
							<td><?php echo $frm->getFieldHTML('task_files[]'); ?></td>
							<td><a onclick="return add_new(this);" id="new_add" href="javascript:void(0);" class="add-file"><i class="icon ion-plus-round"></i></a></td>
						  </tr>
						  <tr>
							<td colspan="2"><small><?php 
										$limit = ini_get( 'upload_max_filesize' );
										echo sprintf( Utilities::getLabel( 'L_Additional_File_Upload_Limit' ), $limit );
									?></small></td>
						  </tr>
						  <tr>
							<td colspan="2">
							<input type="submit" value="<?php echo Utilities::getLabel( 'L_Upload' ); ?>" title="<?php echo Utilities::getLabel( 'L_Upload_File(s)' ); ?>" id="btn_submit" name="submit" class="theme-btn">
							</td>
						  </tr>
						  
						</table><?php echo $frm->getFieldHTML('task_id');?>
					</form><?php echo $frm->getExternalJS(); ?>
                </div>
            </div>
        </div>
    </div>
</div>



<!--- popup wrapper end here--> 

