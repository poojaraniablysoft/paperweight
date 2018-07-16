<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><?php echo Utilities::getLabel( 'L_Manage_Academic_Degrees' ); ?></li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2><?php echo Utilities::getLabel( 'L_Manage_Academic_Degrees' ); ?></h2><a href="<?php echo generateUrl('academic','addupdate');?>" class="button green"><?php echo Utilities::getLabel( 'L_Add_Degree' ); ?></a></div>
	<section class="box search_filter">
		
		<div class="box_content clearfix">
			<?php
				global $post;
				$frmAcademicSearch->fill(array('keyword'=>$post['keyword'],'deg_active'=>$post['deg_active']));
				$frmAcademicSearch->setFieldsPerRow(5);
				$frmAcademicSearch->captionInSameCell(true);
				echo $frmAcademicSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box ">
		<div class="box_head"><h3><?php echo Utilities::getLabel( 'L_Degrees_Listing' ); ?></h3></div>
		<div class="box_content clearfix">
			<?php	
				$arr_flds = array(
				'listserial'=>'S.No.',
				'deg_name'=>'Academic Degree Name',
				'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable', 'id'=>'degree_listing'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$th->appendElement('th', array(), $val);			
				}
				
				foreach ($arr_listing as $sn=>$row){
					/* $tr = $tbl->appendElement('tr');
					if ($row['deg_active'] == 0) $tr->addValueToAttribute('class', 'inactive'); */
					
					$tr = $tbl->appendElement('tr', array('id'=>$row['deg_id']));
					
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'deg_name':
								$td->appendElement('plaintext', array(), $row['deg_name'] , true);
								break;
							case 'action':				
								$td->appendElement('a', array('href'=>generateUrl('academic', 'addupdate', array($row['deg_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'EditCountry(' . $row['country_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'AcademicStatus(' . $row['deg_id'] . ', $(this))', 'class'=>($row['deg_active']==1)?'button small green':'button small red', 'title'=>'Status'), createButton(($row['deg_active']==1)?'Active':'Inactive'), true);
													
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