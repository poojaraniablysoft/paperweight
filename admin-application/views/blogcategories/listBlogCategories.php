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
				<th>Category Title</th>
				<th>Category Description</th>
				<th>Category SEO Name</th>
				<th>Category Parent</th>
				<th>Category Status</th>
				<?php if($can_edit == true){ echo '<th>Action</th>'; } ?>
			</tr>
<?php
	$i=$start_record;
	foreach($records as $record){
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $record['category_title']; ?></td>
				<td><?php echo myTruncateCharacters(nl2br($record['category_description']), 40); ?></td>
				<td><?php echo $record['category_seo_name']; ?></td>
				<td><?php echo ($record['cat_parent'] != '')?$record['cat_parent']:'--';  ?></td>
				<td><?php echo ($record['category_status'] == 1)?'Active':'Inactive'; ?></td>
				<td><a href="<?php echo generateUrl('blogcategories', 'edit', array($record['category_id'])); ?>" class="edit">Edit</a>&nbsp; <a href="<?php echo generateUrl('blogcategories', 'delete', array($record['category_id'])); ?>" class="delete">Delete</a></td>
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