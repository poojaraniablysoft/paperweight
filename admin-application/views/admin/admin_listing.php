<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 
$arr_flds = array(
	'listserial'=> 'Serial NO.',
	'admin_name'	=> 'Name',
	'admin_username'=>'UserName',
	'admin_email'	=>	'Email',
	'admin_active'=>'Status',
	'role_name'	=>	'User Role',
	'action' =>'Action' ); ?>

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Admin User Management</li>
	</ul>
	<div class="flash"><?php echo Message::getHtml(); ?></div>
	<div class="title"><h2>Admin Users</h2><a href="<?php echo generateUrl('admin', 'admin_form'); ?>" class="button green">Add New Admin</a></div>
	
	<section class="box">
	<div class="content">
		<?php				
		$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
		$th = $tbl->appendElement('thead')->appendElement('tr');
		foreach ($arr_flds as $val) $th->appendElement('th', array(), $val);

		foreach ($arr_listing as $sn=>$row) {
			$tr = $tbl->appendElement('tr');
			foreach ($arr_flds as $key=>$val){
				$td = $tr->appendElement('td');
				switch ($key){
					case 'listserial':
						$td->appendElement('plaintext', array(), $sn+1);
						break;
					case 'admin_name':
						$td->appendElement( 'plaintext',array(), $row['admin_firstname'].' '.$row['admin_lastname'] );
					break;
					case 'admin_email':
						$td->appendElement( 'plaintext',array(), $row['admin_email']);
					break;
					case 'admin_active':
						$td->appendElement('plaintext',array(),($row['admin_active'] == 0?'Inactive':'Active'));
						break;
					case 'action':
						$td->appendElement('a', array('href'=>generateUrl('admin', 'admin_form', array($row['admin_id'])), 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
						break;
					default:
						$td->appendElement('plaintext', array(), $row[$key]);
						break;
				}
			}
		}
		if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');
		echo $tbl->getHtml();
		?>
	</div>
	</div>
</section>		