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
				<th>Author Name</th>
				<th>Author Email</th>
				<th>Content</th>
				<th>Post</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
<?php
	$i=$start_record;
	foreach($records as $record){
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo ucfirst($record['comment_author_name']); ?></td>
				<td><?php echo $record['comment_author_email']; ?></td>
				<td><?php echo myTruncateCharacters($record['comment_content'], 20); ?></td>
				<td>
					
						<a href="<?php echo generateUrl('blogposts', 'edit', array($record['post_id'])); ?>"><?php echo $record['post_title']; ?></a>
						
					<br />
					
						<a target="_balnk" href="<?php echo generateUrl('blog', 'post', array($record['post_seo_name']), '/'); ?>">View<a/>
						
				</td>
				<td>
					<?php if($record['comment_status'] == 1){
						echo 'Approved';	
					}elseif($record['comment_status'] == 2){
						echo 'Deleted';	
					}
					else{
						echo 'Pending';
					}
					?>
				</td>
				<td>	
					<a href="<?php echo generateUrl('blogcomments', 'view', array($record['comment_id'])); ?>" class="edit">View</a>
					<?php if($can_edit == true){ ?>&nbsp; <a href="<?php echo generateUrl('blogcomments', 'delete', array($record['comment_id'])); ?>" class="delete">Delete</a><?php } ?>
				</td>
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