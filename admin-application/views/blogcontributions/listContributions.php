<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="box_content clearfix toggle_container">
	<?php if(!$records || !is_array($records)){ echo 'No Record Found!!'; }else{
		echo createHiddenFormFromPost('frmPaging', '', array(), array());
	?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataTable">
			<tr>
				<th width="6%">S. No.</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				<th>Status</th>
				<?php if($can_edit == true){ echo '<th>Action</th>'; } ?>
			</tr>
<?php
	$i=$start_record;
	foreach($records as $record){
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo ucfirst($record['contribution_author_first_name']); ?></td>
				<td><?php echo ucfirst($record['contribution_author_last_name']); ?></td>
				<td><?php echo $record['contribution_author_email']; ?></td>
				<td>
					<?php 
						if($record['contribution_status'] == 1){
							echo 'Approved';
						}elseif($record['contribution_status'] == 2){
							echo 'Posted';
						}elseif($record['contribution_status'] == 3){
							echo 'Rejected';
						}else{
							echo 'Pending';
						}

				?></td>
				<td><a href="<?php echo generateUrl('blogcontributions', 'view', array($record['contribution_id'])); ?>" class="edit">View</a><?php if($can_edit === true){ ?>&nbsp; <a href="<?php echo generateUrl('blogcontributions', 'delete', array($record['contribution_id'], $record['contribution_file_name'])); ?>" class="delete">Delete</a><?php } ?></td>
			</tr>
		<?php $i++;
	}
?>
		</table>
		<div class="gap"></div>
		<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($page,$pages,'listPages(xxpagexx);','',$total_records,$pagesize);?></div>
<?php
	} ?>
</div>