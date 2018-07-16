<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Email Templates</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Email Templates</h2></div>
	<section class="box">
		<div class="box_head"><h3>Templates Listing</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php	
					
				$arr_flds = array(
				'listserial'=>'S.No.',
				'tpl_name'=>'Email Template',
				'tpl_subject'=>'Subject',
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
							case 'tpl_name':
								$td->appendElement('plaintext', array(), $row['tpl_name'] , true);
								break;
							case 'tpl_subject':
								$td->appendElement('plaintext', array(), $row['tpl_subject'] , true);
								break;
							case 'action':				
								$td->appendElement('a', array('href'=>generateUrl('emailtemplate', 'addupdate', array($row['tpl_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'contentStatus(' . $row['tpl_id'] . ', $(this))', 'class'=>(($row['tpl_active']==1)?'button small green':'button small red'), 'title'=>'Status','width'=>'20%'), createButton(($row['tpl_active']==1)?'Active':'Inactive'), true);
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
