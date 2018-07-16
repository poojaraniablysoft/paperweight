<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$post = Syspage::getPostedVar();
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Admin Roles</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Admin Roles</h2><a href="<?php echo generateUrl('role','addupdate');?>" class="button green">Add Role</a></div>
	
	<section class="box">
		<div class="box_head"><h3>Roles List</h3></div>
		<div class="box_content clearfix">
			<?php	
				$arr_flds = array(
					'listserial'=>'S.N.',
					'role_name'=>'Role Name',
					'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				
				foreach ($arr_listing['data'] as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$arr_listing['start_record']);
								break;
							case 'role_name':
								$td->appendElement('plaintext', array(), $row['role_name'] , true);
								break;
							case 'action':	
								
								$td->appendElement('a', array('href'=>generateUrl('role', 'addupdate', array($row['role_id'])), 'title'=>'Edit','class'=>'button small black'), 'Edit', true);
												
								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'Editwork(' . $row['quest_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Edit')." | ", true);
								
								
								/* $td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'deleteRole(' . $row['role_id'] . ', $(this))', 'class'=>'button small red', 'title'=>'Delete'), createButton('Delete'), true); */
													
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
		</div>
			<?php $data = array(
					'pages'=>$arr_listing['pages'],
					'page'=>$arr_listing['page'],
					'pagesize'=>$arr_listing['pagesize'],
					'total_records'=>$arr_listing['total_records']
				);
			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($data['pagesize'],$data['total_records'],$data['page'],$data['pages']); ?></div>
	</section>
</section>