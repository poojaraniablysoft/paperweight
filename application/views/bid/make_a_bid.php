<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $service_types;
//echo '<pre>' . print_r($order,true) . '</pre>';
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel" style="width:990px;">
				<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Order' ); ?>: #<?php echo $order[ 'task_ref_id' ]; ?></h2>
				<div class="gap"></div>
				<?php
				$DTLtbl = new HtmlElement('table',array('width'=>'100%','class'=>'tablelisting'));
				
				$tr = $DTLtbl->appendElement('tr',array());
				$tr->appendElement('td',array('width'=>'30%'), Utilities::getLabel( 'L_Deadline' ) . ': ' . displayDate($order['task_due_date'],true, true, CONF_TIMEZONE).' '. get_time_zone_abbr());
				
				$tr->appendElement( 'td', array( ), Utilities::getLabel( 'L_Status' ) . ' : ' . Utilities::getLabel( 'L_Bidding' ) );
				
				$tr->appendElement( 'td', array( ), Utilities::getLabel( 'L_Pages' ) . ': ' . $order[ 'task_pages' ] );
				
				$tr->appendElement( 'td', array( ), Utilities::getLabel( 'L_Total_bids' ) . ': ' . Task::getnumbids( $order[ 'task_id' ] ) );
				
				echo $DTLtbl->getHtml( );
				$tbl = new HtmlElement( 'table', array( 'width'=>'100%', 'class' => 'formTable' ) );

				$tr = $tbl->appendElement('tr',array());

				$tr->appendElement('td',array('align'=>'right','width'=>'20%'), Utilities::getLabel( 'L_Customer' ) . ' : ' );
				$tr->appendElement('td',array(),$order['user_screen_name'],true);

				$tr = $tbl->appendElement('tr',array());
				$tr->appendElement( 'td', array( 'align' => 'right' ), Utilities::getLabel( 'L_Type_of_Paper' ) . ': ' );
				$tr->appendElement('td',array(),$order['paptype_name']);

				$tr = $tbl->appendElement('tr',array());
				$tr->appendElement('td',array('align'=>'right'), Utilities::getLabel( 'L_Topic' ) . ': ' );
				$tr->appendElement('td',array(), $order[ 'task_topic' ] );

				$tr = $tbl->appendElement('tr',array());
				$tr->appendElement('td',array('align'=>'right'), Utilities::getLabel( 'L_Pages' ) . ': ' );
				$tr->appendElement('td',array(),$order['task_pages'].' ' . Utilities::getLabel( 'L_Pages' ) . ' / '.CONF_WORDS_PER_PAGE.' ' . Utilities::getLabel( 'L_Words' ) );
				
				$tr = $tbl->appendElement('tr',array());
				$tr->appendElement('td',array('align'=>'right'), Utilities::getLabel( 'L_Type_of_Services' ) . ': ' );
				$tr->appendElement('td',array(), $service_types[$order['task_service_type']]);
				
				$tr = $tbl->appendElement('tr',array());
				$tr->appendElement('td',array('align'=>'right'), Utilities::getLabel( 'L_Format_or_Citation_Style' ) . ': ' );
				$tr->appendElement('td',array(),$order['citstyle_name']);
				
				echo $tbl->getHtml();
				$frmbid->addHiddenField('', 'bid_task_id', $order['task_id'], 'task_id');
				
				if($order['bid_id']<1) {
					echo $frmbid->getFormHtml();
				}
				else {
					/* $price = $order['bid_price_per_page']*$order['task_pages']; */$price = $order['bid_price'];
					$comission = $price *(CONF_SERVICE_COMISSION)/100;				
					$price_customer = $price + $comission;
					$newtbl = new HtmlElement('table',array('width'=>'100%','class'=>'tablelisting'));
					
					$tr = $newtbl->appendElement('tr',array());
					$tr->appendElement('td',array('align'=>'left'), Utilities::getLabel( 'L_My_bid_per_page' ) . ': ' );
					$tr->appendElement('td',array(),CONF_CURRENCY.$order['bid_price']);
					
					$tr->appendElement('td',array('align'=>'left'), Utilities::getLabel( 'L_Price_for_me' ) . ': ' );
					$tr->appendElement('td',array(),CONF_CURRENCY.$price);
					
					$tr->appendElement('td',array('align'=>'left'), Utilities::getLabel( 'L_Price_for_customer' ) . ': ' );
					$tr->appendElement('td',array(),CONF_CURRENCY.$price_customer);
					
					$a = $tr->appendElement('td',array(),'',TRUE);
					$a->appendElement('a',array('href'=>'javascript:void(0);','onclick'=>'if(confirm("' . Utilities::getLabel( 'L_Remove_Bid_Alert' ) . '")) {document.location.href=\''.generateUrl('bid','bid_remove',array($order['bid_id'])) . '\';}'), Utilities::getLabel( 'L_REMOVE_MY_BID' ), true );
					$tr->appendElement('td',array(),'<a href="'.generateUrl("bid","bid_update",array($order['bid_id'])).'">' . Utilities::getLabel( 'L_CHANGE_MY_BID' ) . '</a>',TRUE);
										
					echo $newtbl->getHtml();
				}
				?>
				
				<div class="gap"></div>
				<p><?php echo Utilities::getLabel( 'L_Start_writing_your_preview_here' ); ?></p>
				<?php echo $frm->getFormHtml(); ?>
			</div>			
		</div>
	</div>
</div>