<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Language Management</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Language Management</h2> <a href="<?php echo generateUrl('language','addupdate');?>" class="button green">Add Language</a></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Language</h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
			<?php
				global $post;
				$frmlangSearch->fill(array('keyword'=>$post['keyword'],'lang_active'=>$post['lang_active']));
				$frmlangSearch->setFieldsPerRow(5);
				$frmlangSearch->captionInSameCell(true);
				echo $frmlangSearch->getFormHtml();
			?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Language Listing</h3></div>
		<div class="box_content clearfix">
			<?php	
				$arr_flds = array(
				'listserial'=>'S.No.',
				'lang_name'=>'Language',
				'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				
				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['lang_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'lang_name':
								$td->appendElement('plaintext', array(), $row['lang_name'] , true);
								break;
							case 'action':				
								$td->appendElement('a', array('href'=>generateUrl('language', 'addupdate', array($row['lang_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editlang(' . $row['lang_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'langStatus(' . $row['lang_id'] . ', $(this))', 'class'=>($row['lang_active']==1)?'button small green':'button small red', 'title'=>'Status'), createButton(($row['lang_active']==1)?'Active':'Inactive'), true);
													
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