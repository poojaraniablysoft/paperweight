<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>
<div class="order-bids">
	<div class="white-box clearfix">
		<div class="order-step">
		 <?php if($task_ref_id > 0) {?><div class="sectionTitle">
			  <h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $task_ref_id;?></span></h4>
		  </div>
		  <?php }?>
		  <div id="rootwizard" class="register-step">
			<ul>
			  <li class="active"> <a href="#" data-toggle="tab">
				<figure class="figure">1</figure>
				<span><?php echo Utilities::getLabel( 'L_Fill_in_Order_Details' ); ?></span> </a></li>
			  <?php 			  			  $first_order = Common::is_first_order_done();			  			  if ($task_id > 0 && $first_order == true) { ?>
			  <li> <a href="<?php echo generateUrl('task', 'order_bids', array($task_id)); ?>" data-toggle="tab">
				<figure class="figure">2</figure>
				<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a></li>
			  <?php }else{ ?>
			  <li> <a  data-toggle="tab">
				<figure class="figure">2</figure>
				<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a></li>
			  <?php }?>
			  <li> <a href="#tab3" data-toggle="tab">
				<figure class="figure">3</figure>
				<span><?php echo Utilities::getLabel( 'L_Choose_writer_&_Reserve_money' ); ?></span></a></li>
			  <li> <a href="#tab4" data-toggle="tab">
				<figure class="figure">4</figure>
				<span><?php echo Utilities::getLabel( 'L_Track_Progress' ); ?></span></a></li>
			</ul>
			<div id="bar" class="progress progress-striped active">
			  <div style="width: 1.33%;" class="bar"></div>
			</div>
		  </div>	
		</div>
	</div>
	<div class="white-box clearfix padding_30">
		<?php echo Message::getHtml(); ?>
		<?php echo $frm->getFormTag();
			echo $frm->getFieldHTML('task_id');
			echo $frm->getFieldHTML('invitee_ids');
			if($task_id > 0) echo $frm->getFieldHTML('task_ref_id');
		?>
		<div class="register-form">
            <div class="cols_1 full-width">
              <table>
                <?php if(!$task_topic && $task_id != 0) { ?>
				<tr>
                  <td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Personal_Information' ); ?></h2></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_First_name' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('user_first_name');?></td>
                </tr>
                <tr>
                  <td  align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Last_name' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('user_last_name');?></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Country' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('user_country_id');?></td>
                </tr>
				<?php } ?>
				<tr>
                  <td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Basic_Information' ); ?></h2></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_paptype_id');?></td>
                </tr>
                <tr>
                  <td  align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Topic' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_topic');?></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Pages' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_pages');?></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Order_Budget' ); ?><small>(<?php echo Utilities::getLabel( 'L_in_USD' ); ?>)</small></td>
                  <td><?php echo $frm->getFieldHTML('task_budget');?></td>
                </tr>
				<?php if($task_id == 0) { ?>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Deadline' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_due_date');?></td>
                </tr>
				<?php }?>
                <tr>
                  <td colspan="2"><h2 class="top-margin15"><?php echo Utilities::getLabel( 'L_Additional_Information' ); ?></h2></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Discipline' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_discipline_id');?></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Type_of_service' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_service_type');?></td>
                </tr>
                <tr>
                  <td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Format_or_citation_style' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_citstyle_id');?></td>
                </tr>
                <tr>
                  <td align="right" valign="top" ><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Paper_instructions' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('task_instructions');?></td>
                </tr>
                <tr>
                  <td valign="top" align="right"><?php echo Utilities::getLabel( 'L_Upload_additional_material' ); ?></td>
                  <td><table class="uploads-file" width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><?php echo $frm->getFieldHTML('task_files[]');?>
                          <a href="javascript:void(0);" onclick="add_new(this);" class="add-file"><i class="icon ion-plus-round"></i></a></td>
                      </tr>
                      <tr>
                        <td><small><?php 
										$limit = ini_get( 'upload_max_filesize' );
										echo sprintf( Utilities::getLabel( 'L_Additional_File_Upload_Limit' ), $limit );
									?></small></td>
                      </tr>
                    </table></td>
                </tr>
				<?php if($invitee_id == 0) { ?>
                <tr>
                  <td colspan="2"><h2 class="top-margin15"><?php echo Utilities::getLabel( 'L_Request_a_specific_writer' ); ?></h2></td>
                </tr>				
                <tr>
                  <td align="right" ><?php echo Utilities::getLabel( 'L_Invite_writer' ); ?></td>
                  <td><?php echo $frm->getFieldHTML('invitees');?></td>
                </tr>
				<?php } ?>
                <tr>
                  <td></td>
                  <td><?php echo $frm->getFieldHTML('term_cond');?></td>
                </tr>
                <tr>
					<td></td>
					<td>
						<?php echo $frm->getFieldHTML( 'btn_submit' ); ?>
						<br/>
						<small><?php echo sprintf( Utilities::getLabel( 'L_Agree_Tc_After_Click_On_Publish_For_Writers_Btn' ), generateUrl( 'cms',  'terms-conditions' ) ); ?></small>
					</td>
                </tr>
              </table>
            </div>
          </div>
		</form><?php echo $frm->getExternalJS(); ?>
	</div>
 <!-- body -->
</div>