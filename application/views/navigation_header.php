<header id="header">
<?php
	$logoUrl = generateUrl('image', 'logo', array( CONF_WEBSITE_LOGO ));
?>
	<div class="fix-container">
		<div class="logo"> <a href="<?php echo generateUrl('/'); ?>"><img src="<?php echo $logoUrl;?>" alt="Logo" /></a> </div>
		<div class="<?php echo (!User::isUserLogged()) ? 'right-side':'right-side';?>">
		  <nav id="nav"> <a id="pull" href="#"><span><i class="icon ion-navicon-round"></i></span></a>
			<ul id="menu">
				<!--li <?php if($arr['2']=='how-it-works'){echo 'class="active"';}?>> <a href="<?php echo generateUrl('cms','page',array('how-it-works'));?>">How it works? </a></li-->
				<li <?php if(isset($howitworks)){echo 'class="active"';}?>><a href="<?php echo generateUrl('howitworks'); ?>">How it works?</a></li>
			<?php if (!User::isUserLogged() || (User::isUserLogged() && User::isWriter())) { ?>
			  <li <?php if($requested_action=='latest_orders'){echo 'class="active"';}?>> <a href="<?php echo generateUrl('latest-orders'); ?>">Latest Orders </a></li>
			<?php } ?>
			
			  <li <?php if($requested_action=='top_writers'){echo 'class="active"';}?>> <a href="<?php echo generateUrl('top-writers'); ?>">Top Writers </a></li>
			  <!--li <?php //if($arr['2']=='faq'){echo 'class="active"';}?>> <a href="<?php //echo generateUrl('cms','page',array('faq'));?>">FAQ </a></li-->
			  
			  <li <?php if(isset($faq)){echo 'class="active"';}?>> <a href="<?php echo generateUrl('faq'); ?>">FAQ</a></li>
			  <li <?php if($arr['2']=='blog'){echo 'class="active"';}?>> <a href="<?php echo generateUrl('blog');?>">Blog </a></li>
			</ul>
		  </nav>
		  <div class="my-account">
			<?php if(!User::isUserLogged()) {?>
			  <a href="<?php echo generateUrl('user', 'signin'); ?>">
				<span class="login-icn"><i class="icon ion-person"></i></span>
				<span class="mobile-hide">Log In</span>
			 </a> 
			<?php }else {?>
				<a>
					<img src="<?php echo generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),28,28));?>" alt="">
					<span class="mobile-hide">
						<?php 
							/* if (!User::isWriter(User::getLoggedUserAttribute('user_id'))) {
								if(strlen(User::getLoggedUserAttribute('user_screen_name')) > 10) { echo substr(User::getLoggedUserAttribute('user_screen_name'),0,10)."...";
								}else {
									echo User::getLoggedUserAttribute('user_screen_name');
								}
							}
							if (User::isWriter(User::getLoggedUserAttribute('user_id'))) {
								echo substr(User::getLoggedUserAttribute('user_screen_name'),0,10);
							} */
						?>
						My Account
						<i class="icon ion-android-arrow-dropdown"></i>
					</span>
					<i class="ion-android-arrow-dropdown"></i>
				</a>
				<ul class="drop-down">
					<li><a href="<?php echo generateUrl('user', 'dashboard'); ?>">Dashboard</a></li>
					<li><a href="<?php echo generateUrl('user', 'update_profile'); ?>">My Profile</a></li>
					<?php if (!User::isWriter()) { ?>
					<li><a href="<?php echo generateUrl('task', 'my_orders'); ?>">My Orders</a></li>
					<li><a href="<?php echo generateUrl('transactions', 'payments'); ?>">Balance&nbsp;(<?php echo CONF_CURRENCY . $page_data['wallet_balance']; ?>)</a></li>
					<li><a href="<?php echo generateUrl('task', 'add'); ?>">Add New Order</a></li>
					<li><a href="<?php echo generateUrl('support'); ?>">Support</a></li>
					<?php } ?>
					<?php if (User::isWriter()) { ?>
					<li><a href="<?php echo generateUrl('task', 'orders'); ?>">Browse Orders</a></li>
					<li><a href="<?php echo generateUrl('bid', 'my_bids'); ?>">My Bids</a></li>
					<?php } ?>
					<li><a title="Logout" href="<?php echo generateUrl('user','logout');?>">Logout</a></li>
				</ul>					
			<?php }?>
		  </div>
		</div>
	</div>
</header>