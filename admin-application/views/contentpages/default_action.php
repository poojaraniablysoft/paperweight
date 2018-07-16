<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Content Management System</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Content Management System</h2><a href="<?php echo generateUrl('contentpages','addupdate');?>" class="button green">Add CMS Pages</a></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Pages</h3></div>-->
		<div class="box_content clearfix toggle_container">
			<?php
				global $post;
				$frmcontentSearch->fill(array('keyword'=>$post['keyword'],'cmspage_active'=>$post['cmspage_active']));
				$frmcontentSearch->setFieldsPerRow(5);
				$frmcontentSearch->captionInSameCell(true);
				echo $frmcontentSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Pages Listing</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php
				$arr_flds = array(
				'listserial'=>'S.No.',
				'cmspage_title'=>'Page Title',
				//'cmspage_name'=>'Page Name',
				'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				
				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['cmspage_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array('width'=>'20%'), $sn+$start_record);
								break;
							case 'cmspage_title':
								$td->appendElement('plaintext', array('width'=>'40%'), html_entity_decode($row['cmspage_title']) , true);
								break;
							case 'cmspage_name':
								$td->appendElement('plaintext', array(), html_entity_decode($row['cmspage_name']) , true);
								break;
							case 'action':				
								$td->appendElement('a', array('href'=>generateUrl('contentpages', 'addupdate', array($row['cmspage_id'])), 'title'=>'Edit', 'class'=>'button small black','width'=>'20%'), createButton('Edit'), true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editwork(' . $row['exfld_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								/* if($row['cmspage_active']==1) {
									$status = '<a href="javascript:void(0);" onclick="contentStatus(' . $row['cmspage_id'] . ', $(this))" class="button small green">Active</a>';
								}else {
									$status = '<a href="javascript:void(0);" onclick="contentStatus(' . $row['cmspage_id'] . ', $(this))" class="button small red">Inactive</a>';
								}							
								
								$td->appendElement('a',array('onclick'=>'contentStatus(' . $row['cmspage_id'] . ', $(this))'),$status,true); */
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'contentStatus(' . $row['cmspage_id'] . ', $(this))', 'class'=>(($row['cmspage_active']==1)?'button small green':'button small red'), 'title'=>'Status','width'=>'20%'), createButton(($row['cmspage_active']==1)?'Active':'Inactive'), true);
													
								break;
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();

			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
		</div>
	</section>
</section>