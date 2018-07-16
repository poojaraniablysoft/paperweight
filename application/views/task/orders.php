<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo '<pre>' . print_r($arr_orders,true) . '</pre>';
?> 
<div class="order-page"> 
	<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ). '</a>';}?>
			</p>
		</div>
	<?php  }?>
	<?php echo Message::getHtml(); ?>
	<div class="sortwrap">
		<?php echo $frm->getFormtag(); ?>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="filter-table">
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><?php echo Utilities::getLabel( 'L_Filter_by_pages' ); ?></td>
							<td><?php echo $frm->getFieldHTML('filter_page'); ?></td>
							</td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="right"><?php echo Utilities::getLabel( 'L_Filter_by_deadline' ); ?></td>
							<td><?php echo $frm->getFieldHTML('filter_date'); ?></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="right"><?php echo Utilities::getLabel( 'L_Type_of_Papers' ); ?></td>
							<td><?php echo $frm->getFieldHTML('paptype_name'); ?></td>
						</tr>
					</table>
				</td>
				<td align="right"><?php echo $frm->getFieldHTML('btn_submit');?></td>
				</tr>
			
		  </table>
		</form><?php echo $frm->getExternalJS(); ?>
	</div>

	<div class="dataTable padding_30 ">
		<div class="table-scroll">
		  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
			  <th width="25%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Pages_words' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Bids' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
			  <th><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
			</tr>
			<?php foreach($arr_orders['orders'] as $key=>$ele) { ?>
				<tr>
					<td><a href="<?php echo generateUrl('bid','add',array($ele['task_id']),CONF_WEBROOT_URL);?>"><?php echo '#'.$ele['task_ref_id'];?></a></td>
					<td><?php echo $ele['task_topic'];?>	</td>
					<td><?php echo $ele['paptype_name'];?></td>
					<td><?php echo $ele['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $ele['task_pages']*CONF_WORDS_PER_PAGE; ?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
					<td><?php echo $date = displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone(); ?><br><small <?php if(strtotime($ele['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo time_diff($ele['task_due_date']); ?>)</small></td>
					<td><?php echo Task::getnumbids($ele['task_id']);?></td>
					<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
					<?php if(User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?><td><a class="theme-btn" href="<?php echo generateUrl('bid','add',array($ele['task_id']),CONF_WEBROOT_URL);?>"><?php echo Utilities::getLabel( 'L_Place_Bid' ); ?></td><?php }else { echo '<td><a class="theme-btn" href="'.generateUrl('bid','add',array($ele['task_id']),CONF_WEBROOT_URL).'">' . Utilities::getLabel( 'L_View_Details' ) . '</a></td>';}?>
				</tr>
			<?php } ?>
			<?php if (count($arr_orders['orders']) == 0) echo '<tr><td colspan="7">' . Utilities::getLabel( 'L_No_orders_to_show_here' ) . '</td></tr>'; ?>
			
		  </table>
		  <div class="pagination"><?php echo generatePagingStringHtml($arr_orders['page'], $arr_orders['pages']); ?></div>
		</div>
	</div>
</div>