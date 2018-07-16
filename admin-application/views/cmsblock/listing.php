<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 

$arr_flds = array(
'listserial'=>'S.No.',
'title'=>'Block Title',
'page_name'=>'Page Name',
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
			case 'title':
				$td = $tr->appendElement('td',array('width'=>'40%'));
				$td->appendElement('plaintext',array(),$row[$key],true);
				break;
			case 'action':
				$td = $tr->appendElement('td');
				$td->appendElement('a', array('href'=>generateUrl('cmsblock', 'form', array($row['id'])), 'rel'=>'', 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
				break;
			default:
				$td = $tr->appendElement('td');
				$td->appendElement('plaintext', array(), $row[$key], true);
				break;
		}
	}
}

if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

echo $tbl->getHtml();

