<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl( 'home' ); ?>"><img src="<?php echo CONF_WEBROOT_URL; ?>images/home.png" alt=""> </a></li>
		<li><?php Utilities::getLabel( 'L_Labels_Management' ); ?></li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2><?php Utilities::getLabel( 'L_Search_Labels' ); ?></h2></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Language</h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
			<?php echo $frmlabelSearch->getFormHtml(); ?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3><?php Utilities::getLabel( 'L_Labels_Listing' ); ?></h3></div>
		<div class="box_content clearfix">
			<?php 
				$arr_flds = array (
								'listserial'=>'S.No.',
								'label_key'=>'Key',
								'label_caption' => 'Caption'
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
							case 'label_key':
								$td->appendElement('plaintext', array(), $row['label_key'] , true);
								break;
							case 'label_caption':				
								$td->appendElement('plaintext', array(), '<div class="editmessage" id="' . $row["label_id"] . '~label_caption">' . $row['label_caption'] . '</div>', true);
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
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages, $filter);?></div>
		</div>
	</section>
</section>