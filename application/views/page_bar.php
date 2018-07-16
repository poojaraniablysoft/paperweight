<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');

global $requested_controller;
global $requested_action;
global $requested_query;

switch($requested_controller) {
	case 'UserController':
		switch($requested_action) {
			case 'top_writers':
				$page_heading = 'Top Writers';
				break;
			case 'dashboard':
				$page_heading = 'Dashboard';
				break;
			default:
				$page_heading = User::isUserLogged() ? 'My Account' : '';
				break;
		}				
		break;
	case 'TaskController':
		switch($requested_action) {
			case 'assign_task':
				$page_heading = 'Assign Writer';
				break;
			case 'my_orders':
				$page_heading = 'My Orders';
				break;
			case 'order_bids':
				$page_heading = 'Order Bids';
				break;
			case 'orders':
				$page_heading = 'Browse Orders';
				break;
			case 'bid_preview':
				$page_heading = 'Bid Details';
				break;
			case 'add':
				foreach($requested_query as $val);
				$page_heading = ($val > 0) ? 'Change Order':'Place Order';
				break;
			case 'order_process':
				$page_heading = 'Order Process';
				break;
			case 'order_history':
				$page_heading = 'Order History';
				break;
		}
		break;
	case 'TransactionsController':
		switch($requested_action) {
			case 'payments':
				$page_heading = 'Payments';
				break;
			case 'earnings':
				$page_heading = 'Earnings';
				break;
			default:
				$page_heading = User::isUserLogged() ? 'Transactions' : '';
				break;
		}
		break;
	case 'MessagesController':
		switch($requested_action) {
			default:
				$page_heading = User::isUserLogged() ? 'Messages' : '';
				break;
		}
		break;
	case 'ReviewsController':
		switch($requested_action) {
			case 'testimonials':
				$page_heading = 'Testimonials';
				break;
			case 'user_reviews':
				$page_heading = 'User Reviews';
			default:
				$page_heading = User::isUserLogged() ? 'Reviews' : '';
				break;
		}				
		break;
	case 'SupportController':
		switch($requested_action) {
			
			default:
				$page_heading = User::isUserLogged() ? 'Support' : '';
				break;
		}				
		break;
	case 'BidController':
		switch($requested_action) {
			
			default:
				$page_heading = User::isUserLogged() ? 'My Orders' : '';
				break;
		}				
		break;
	default:
		$page_heading = User::isUserLogged() ? 'My Account' : '';
		break;
}
?>

<!-- New Html integration -->

<div class="heading<?php if(!User::isWriter()) {?> mob-green-bg<?php }?>">
	<?php
		$logoUrl = generateUrl('image', 'logo', array( CONF_WEBSITE_LOGO ));
	?>
	<figure class="db-logo tablet-show mobile-hide"><a href="<?php echo generateUrl('home');?>"><img src="<?php echo $logoUrl; ?>" alt="logo"></a></figure>
	<a href="javascript:void(0)" class="mob-nav"><i class="icon ion-navicon-round"></i> </a>
	<h1 class="mob-hide"> <?php echo $page_heading;?></h1>
	<div class="right-side <?php if(!User::isWriter()) {echo "green-icon";}?>">
		<ul>
			<li><a href="<?php echo generateUrl('messages'); ?>"><i class="icon ion-email"></i><span class="count"></span></a></li>
			<!--<li><a href="<?php echo generateUrl('messages'); ?>"><i class="icon ion-android-notifications"></i><font><span class="count">0</span></font></a></li>-->
		</ul>
		<div class="my-account"><a><img src="<?php echo generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),32,32));?>" alt="img"><span class="mobile-hide">My Account</span><i class="ion-android-arrow-dropdown"></i></a>
			<ul class="drop-down">
				<?php if (!User::isWriter()) { ?>
				<li><a href="<?php echo generateUrl('task', 'my_orders'); ?>">My Orders</a></li>
				<li><a href="<?php echo generateUrl('transactions', 'payments'); ?>">Balance&nbsp;(<?php echo CONF_CURRENCY . $page_data['wallet_balance']; ?>)</a></li>
				<li><a href="<?php echo generateUrl('task', 'add'); ?>">Add New Order</a></li>
				<?php }else { ?>
				<li><a href="<?php echo generateUrl('bid', 'my_bids'); ?>">My Bids</a></li>
				<li><a href="<?php echo generateUrl('task', 'orders'); ?>">+ Check New Order</a></li>
				<?php } ?>
				<li><a href="<?php echo generateUrl('support'); ?>">Support</a></li>
				<li><a href="<?php echo generateUrl('user', 'logout'); ?>" title="Logout">Logout</a></li>
			</ul>
		</div>
	</div>
</div>
<h1 class="mob-show"><?php echo $page_heading;?></h1>
<!--End here-->

<?php/*  <div class="pageBar">
  	<div class="fix-container">
    	<div class="grid_1">
            <h2><?php echo $page_heading; ?></h2>
            <ul class="breadcrumb">
                <li><a href="<?php echo User::isUserLogged() ? generateUrl('user', 'dashboard') : generateUrl('home'); ?>" class="home"><img src="<?php echo CONF_WEBROOT_URL; ?>images/home_icon.png" alt=""></a></li>
                <li><span><?php echo getBreadCrumb($controller,$action); ?></span></li>
            </ul>
        </div> 
		<?php if(User::isUserLogged()) { ?>
			<div class="grid_2">
				<div class="countsWrap">
					<span class="currency"><?php echo CONF_CURRENCY; ?></span>                
					<span class="values">My Wallet&nbsp;<strong><?php echo CONF_CURRENCY . $page_data['wallet_balance']; ?></strong></span>
				</div>
				<?php if (User::isWriter()) { ?>
					<a href="<?php echo generateUrl('task', 'orders'); ?>" class="buttonGreen">+ Check New Order</a>
				<?php } else { ?>
					<a href="<?php echo generateUrl('task', 'add'); ?>" class="buttonGreen">+ Place New Order</a>
				<?php } ?>
			</div>
		<?php } ?>
    </div>
</div> */?>