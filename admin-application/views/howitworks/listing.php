<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 

$arr_flds = array(
'listserial'=>'S.No.',
'step_image'=>'Image',
'step_title'=>'Title',
'step_type_id'=>'Type',
'step_display_order'=>'Order',
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
			case 'step_image':
				$image = generateUrl('image', 'step', array( 'thumb', $row[$key] ));
				$td = $tr->appendElement('td',array());
				$td->appendElement('img',array('src'=>$image), '', true);
				break;
			case 'step_title':
				$td = $tr->appendElement('td',array());
				$td->appendElement('plaintext',array(),$row[$key],true);
				break;
			case 'step_type_id':
				$cats = Applicationconstants::$arr_cats;
				
				$text = (isset($cats[$row[$key]]))?$cats[$row[$key]]:'NA';
				
				$td = $tr->appendElement('td',array());
				$td->appendElement('plaintext',array(), $text,true);
				break;
			case 'step_display_order':
/* 				$cats = Applicationconstants::$arr_cats;
				
				$text = (isset($cats[$row[$key]]))?$cats[$row[$key]]:'NA';*/
				
				$td = $tr->appendElement('td',array('width'=>'5%')); 
				$td->appendElement('plaintext',array(), '<input type="text" name="'.$row["step_id"].'" value="'.$row['step_display_order'].'"/>',true);
				break;					
			case 'action':
				$td = $tr->appendElement('td');
				$td->appendElement('a', array('href'=>generateUrl('howitworks', 'form', array($row['step_id'])), 'rel'=>'', 'title'=>'Edit', 'class'=>'button small black'), createButton('Edit'), true);
				$td->appendElement('a', array('href'=>generateUrl('howitworks', 'delete', array($row['step_id'])), 'rel'=>'', 'title'=>'Delete', 'class'=>'button small black', 'onclick'=>'return confirm("Are you sure you want to delete this step?");'), createButton('Delete'), true);
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
<form class="siteForm" id="frm_mbs_id_frmSort" name="frmSort" method="post">
<?php echo $tbl->getHtml();?>
<input type="submit" value="Re Order" name="sort" />
<div class="paginationwrap"><?php echo generatePaging($pagesize,$total_records,$page,$pages);?></div>
</form>