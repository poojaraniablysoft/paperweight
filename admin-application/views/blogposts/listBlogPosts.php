<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($records,true)."</pre>";
?>
<div class="box_content clearfix toggle_container">
	<?php if(!$records || !is_array($records)){ echo 'No Record Found!!'; }else{
		echo createHiddenFormFromPost('frmPaging', '', array(), array());
	?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataTable">
			<tr>
				<th width="6%">S. No.</th>
				<th>Post Title</th>
				<th>Post Category</th>
				<th>Post Status</th>
				<?php if($can_edit == true){ ?><th>Action</th><?php } ?>
			</tr>
<?php
	$i=$start_record;
	foreach($records as $record){
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $record['post_title']; ?></td>
				<td>
					<?php 
						$post_category_counter = 1;
						foreach($record['post_categories'] as $cat){
							echo $cat['category_title'];
							if($post_category_counter != count($record['post_categories'])){
								echo ', ';
							}
							$post_category_counter++;
						}
					?>
				</td>
				<td><?php echo ($record['post_status'] == 1)?'Published':'Draft'; ?></td>
				<?php if($can_edit == true){ ?><td><a href="<?php echo generateUrl('blogposts', 'edit', array($record['post_id'])); ?>" class="edit">Edit</a>&nbsp; <a href="<?php echo generateUrl('blogposts', 'delete', array($record['post_id'], $record['relation_category_id'])); ?>" class="delete">Delete</a></td><?php } ?>
			</tr>
		<?php $i++;
	}
?>
		</table>
		<div class="gap"></div>
	<?php	/* $data = array(
								'pagesize'=>$pagesize,
								'total_records'=>$total_records,
								'page'=>$page,
								'pages'=>$pages
				); */
			?>
			
			<div class="paginationwrap"><?php echo generateNewPagingStringHtml($page,$pages,'listPages(xxpagexx);','',$total_records,$pagesize);?></div>
<?php }?>
</div>