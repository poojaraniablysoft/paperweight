<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
echo Message::getHtml();
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Service Field Management</li>
	</ul>
	<div class="title"><h2>Service Field Management</h2><a href="<?php echo generateUrl('services','addupdate');?>" class="button green">ADD WORK FIELD</a></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Service Field</h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
			<?php
				global $post;
				$frmservicesSearch->fill(array('keyword'=>$post['keyword'],'service_active'=>$post['service_active']));
				$frmservicesSearch->setFieldsPerRow(5);
				$frmservicesSearch->captionInSameCell(true);
				echo $frmservicesSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Service Field Listing</h3></div>
		<div class="box_content clearfix">
			<?php	
			$arr_flds = array(
			'listserial'=>'S.No.',
			'service_name'=>'Service Field',
			'action' => 'Action'
			);
			
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);			
			}
			
			foreach ($arr_listing as $sn=>$row){
				$tr = $tbl->appendElement('tr');
				if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sn+$start_record);
							break;
						case 'service_name':
							$td->appendElement('plaintext', array(), $row['service_name'] , true);
							break;
						case 'action':				
							$td->appendElement('a', array('href'=>generateUrl('services', 'addupdate', array($row['service_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
											
							//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editservice(' . $row['service_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
							
							$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'serviceStatus(' . $row['service_id'] . ', $(this))', 'class'=>($row['service_active']==1)?'button small green':'button small red', 'title'=>'Status'), createButton(($row['service_active']==1)?'Active':'Inactive'), true);
												
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