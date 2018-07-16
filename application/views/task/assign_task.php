<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($arr_tip,true)."</pre>";exit;
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>
<div class="choicewriter">
  <div class="white-box clearfix">
	<?php echo Message::getHtml(); ?>
	<div class="sectionTitle">
		<h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $arr_bid['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo $arr_bid['task_topic'];?></span></h4>	
	</div>
	<div id="rootwizard" class="register-step">
		<ul>
		  <li class="fill-up"> <a href="<?php echo generateUrl('task','add',array($arr_bid['bid_task_id']));?>" data-toggle="tab">
			<figure class="figure">1</figure>
			<span><?php echo Utilities::getLabel( 'L_Fill_in_Order_Details' ); ?></span> </a></li>
		  <li class="fill-up"><a href="<?php echo generateUrl('task','bid_preview',array($arr_bid['bid_id']));?>" data-toggle="tab">
			<figure class="figure">2</figure>
			<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a></li>
		  <li class="active"><a data-toggle="tab">
			<figure class="figure">3</figure>
			<span><?php echo Utilities::getLabel( 'L_Choose_writer_&_Reserve_money' ); ?></span></a></li>
		  <li> <a data-toggle="tab">
			<figure class="figure">4</figure>
			<span><?php echo Utilities::getLabel( 'L_Track_Progress' ); ?></span></a></li>
		</ul>
		<div id="bar" class="progress progress-striped active">
		  <div style="width: 66.33%;" class="bar"></div>
		</div>
	</div> 
  </div>
  <div class="white-box clearfix padding_30">
	<div class="gray-box clearfix">
	  <div class="grid_1">	  
		<div class="sectionphoto">
			<figure class="photo"> <img src="<?php echo generateUrl('user', 'photo', array($arr_bid['bid_user_id'],150,150));?>" alt="img"></figure>
			<p><strong><?php echo $arr_bid['writer'];?></strong></p>
			<?php if ($writer_rating > 0) { ?>
				<div class="ratingsWrap"><span class="p<?php echo ($writer_rating*2); ?>"></span></div>
				<span class="nrmltxt"><?php echo $writer_rating; ?></span>												
			<?php } else { ?>
				<div class='ratingsWrap'><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></div>
			<?php } ?>
			<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $writer_completed_orders; ?></p>
		</div>
	  </div>
	  <div class="grid_2">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Topic' ); ?>:</strong></td>
			  <td><?php echo $arr_bid['task_topic'];?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>:</strong></td>
			  <td><?php echo $arr_bid['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?> / <?php echo $arr_bid['task_pages']*$arr_bid['task_words_per_page']?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?></strong></td>
			  <td><?php echo $date = displayDate($arr_bid['task_due_date'],true, true, CONF_TIMEZONE);?><?php print_time_zone(); ?>  (<span <?php if(strtotime($arr_bid['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>><?php echo time_diff($arr_bid['task_due_date']);?></span>)</td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Price' ); ?>:</strong></td>
			  <td><?php $price = $arr_bid['bid_price'];echo CONF_CURRENCY.number_format(round($price,2),2);?></td>
			</tr>
			<tr>
			  <td> <strong><?php echo Utilities::getLabel( 'L_Service_charges' ); ?></strong> <div class="qmark tooltips">?<span><?php echo $arr_tip['infotip_description'];?></span></div></td>
			  <td><?php echo CONF_CURRENCY.number_format(round(CONF_SERVICE_CHARGE,2),2);?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Amount_to_load' ); ?></strong></td>
			  <td><?php echo CONF_CURRENCY . number_format(getOrderAmount($arr_bid['bid_price'],$arr_bid['task_pages']),2); ?></td>
			</tr>
			<tr>
			  <td></td>
			  <td>
				<p><?php echo Utilities::getLabel( 'L_Reserve_Money_Description' ); ?></p>
				<a href="javascript:void(0);" class="theme-btn" id="btn_reserve_amount" data-bid-id="<?php echo $arr_bid['bid_id']; ?>"	><?php echo Utilities::getLabel( 'L_Reserve_Money_&_Start_work_Process' ); ?></a>
				<a href="<?php echo generateUrl('task','bid_preview',array($arr_bid['bid_id']));?>" class="theme-btn"><?php echo Utilities::getLabel( 'L_Back_to_Bidding' ); ?></a>
			  </td>
			</tr>
		</table>
	  </div>	
	</div>
  </div> 
  
  <div class="white-box clearfix padding_30 blockpayments hide">
	<div class="gray-box">	 
		<div class="blockpayments reserve-Money-div"> 
			<div class="innerpart ">
			
				<img src="<?php echo CONF_WEBROOT_URL;?>images/card_payments.jpg" alt="" class="cards">
				<?php 
					/* @DD Start */
					if( $walletLimit > $availableWalletAmount && ( ( $walletLimit - $availableWalletAmount ) > $arr_bid['bid_price'] ) ) { ?>
					<h4><?php echo Utilities::getLabel( 'L_Load_money_to_your_current_balance' ); ?></h4>
					<small><?php 
							echo sprintf( Utilities::getLabel( 'L_Load_Maximum_Amount_Description' ), '$ ' . ( $walletLimit - $availableWalletAmount ) );
						?>
					</small>
				<?php 
					echo $frm->getFormTag();
					echo $frm->getFieldHtml('business');echo $frm->getFieldHtml('currency_code');echo $frm->getFieldHtml('return');
					echo $frm->getFieldHtml('cancel_return');echo $frm->getFieldHtml('notify_url');echo $frm->getFieldHtml('cmd');
					echo $frm->getFieldHtml('item_name');echo $frm->getFieldHtml('rm');echo $frm->getFieldHtml('custom');
					$fld = $frm->getField('amount');
					$fld->extra='readonly="readonly"';
					
					$fld->value = $arr_bid['bid_price'] - $availableWalletAmount + $serviceCharges;
					?>
						<div class="Payment-method-div">
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable">		
								<tr>
									<td><?php echo Utilities::getLabel( 'L_Payment_method' ); ?> <input type="radio" checked="" title="Payment method" value="0" name="payment_method"> <?php echo Utilities::getLabel( 'L_PayPal' ); ?></td>					 
									<td><?php echo Utilities::getLabel( 'L_Amount_to_be_loaded' ); ?><span class="mandatory">*</span> <div class="symbolfield"><span class="currency">$</span><?php echo $frm->getFieldHtml('amount');?></div></td>
									<td><?php echo $frm->getFieldHtml('btn_load');?></td>
								</tr>			  
							</table>
						</div>
						<?php echo $frm->getFieldHtml('extra');?>
					</form>
					<?php echo $frm->getExternalJS(); ?>
			<?php } else { ?>
				<small><?php echo Utilities::getLabel( 'L_Exceed_Your_Wallet_Limit_Description' ); ?></small>
			<?php } /* @DD End */ ?>
			</div>
		</div>
	</div>
  </div>
  
</div>
