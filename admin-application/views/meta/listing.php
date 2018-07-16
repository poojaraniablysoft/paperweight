<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 
require_once( CONF_THEME_PATH . '_partials/common/left.php' );

$arr_flds = array(
'listserial'=>'S.No.',
'page_title'=>'Page Title',
'meta_title'=>'Meta Title',
'action' => 'Action'
);

$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) $th->appendElement('th', array(), $val);

foreach ($arr_listing as $sn=>$row){
	$tr = $tbl->appendElement('tr');
	foreach ($arr_flds as $key=>$val){
		switch ($key){
			case 'listserial':
				$td = $tr->appendElement('td');
				$td->appendElement('plaintext', array(), $sn+$start_record);
				break;
			case 'action':
				$td = $tr->appendElement('td');
				$td->appendElement('a', array('href'=>generateUrl('meta', 'form', array($row['page_id'])), 'rel'=>'', 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
				break;
			default:
				$td = $tr->appendElement('td');
				$td->appendElement('plaintext', array(), $row[$key], true);
				break;
		}
	}
}

if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');
?>

<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Meta Tags Management</li>
	</ul>	
	<div class="title"><h2>Meta Tags Management</h2></div>			
	<section class="box">
		<div class="box_head"><h3>Meta Tags Listing</h3></div>
		<div class="box_content clearfix">		
			<?php echo $tbl->getHtml();?>
		</div>
	</section>		
		
	</section>
