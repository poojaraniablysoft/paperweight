<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$post = Syspage::getPostedVar();
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Questions And Answers</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Questions And Answers</h2><a href="<?php echo generateUrl('question','addupdate');?>" class="button green">ADD QUESTION</a></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Questions</h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix">
			<?php 
				$frmqueSearch->fill($post);
				echo $frmqueSearch->getFormHtml();?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Questions List</h3></div>
		<div class="box_content clearfix">
			<?php	
				

				$arr_flds = array(
				'listserial'=>'S.No.',
				'quest_question'=>'Questions',
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
							case 'quest_question':
								$td->appendElement('plaintext', array(), $row['quest_question'] , true);
								break;
							case 'action':	
								
								$td->appendElement('a', array('href'=>generateUrl('question', 'addupdate', array($row['quest_id'])), 'title'=>'Edit','class'=>'button small black'), 'Edit', true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editwork(' . $row['quest_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'queStatus(' . $row['quest_id'] . ', $(this))', 'class'=>(($row['quest_active']==1)?'button small green':'button small red'), 'title'=>'Status'), createButton(($row['quest_active']==1)?'Active':'Inactive'), true);
													
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