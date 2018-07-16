<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Manage Paper Type</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Manage Paper Type</h2><a href="<?php echo generateUrl('paper','addupdate');?>" class="button green">ADD PAPER TYPE</a></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Paper Type</h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
			<?php
				global $post;
				$frmpaptypeSearch->fill(array('keyword'=>$post['keyword'],'paptype_active'=>$post['paptype_active']));
				$frmpaptypeSearch->setFieldsPerRow(5);
				$frmpaptypeSearch->captionInSameCell(true);
				echo $frmpaptypeSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Paper Type Listing</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php	
				$arr_flds = array(
				'listserial'=>'S.No.',
				'paptype_name'=>'Paper Type',
				'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				
				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['paptype_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'paptype_name':
								$td->appendElement('plaintext', array(), $row['paptype_name'] , true);
								break;
							case 'action':				
								$td->appendElement('a', array('href'=>generateUrl('paper', 'addupdate', array($row['paptype_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editpaptype(' . $row['paptype_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'paptypeStatus(' . $row['paptype_id'] . ', $(this))', 'class'=>'button small '.(($row['paptype_active'] == 1)?'green':'red'), 'title'=>'Status'), createButton(($row['paptype_active']==1)?'Active':'Inactive'), true);
													
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