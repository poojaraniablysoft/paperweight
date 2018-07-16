<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($_SESSION)."</pre>";exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php
		writeMetaTags();
		echo Syspage::getJsCssIncludeHtml( false );
		?>
		<!--link rel="shortcut icon" href="<?php #echo CONF_WEBROOT_URL;?>images/favicon.ico">
		<link rel="apple-touch-icon image_src" href="<?php #echo CONF_WEBROOT_URL;?>images/favicon.png"-->
		
		<?php
			$fav_icon = generateUrl('image', 'fav_icon', array( CONF_WEBSITE_FAV_ICON ));
		?>
		
		<link rel="shortcut icon" href="<?php echo $fav_icon;?>">
		<link rel="apple-touch-icon image_src" href="<?php echo $fav_icon;?>">
		
		<script language="javascript" type="text/javascript" src='<?php echo CONF_WEBROOT_URL; ?>public/innova-lnk/scripts/innovaeditor.js'></script>
		
	</head>
	<body>
		<div id="common_msg"><?php echo Message::getHtml();?></div>
		<div id="wrapper">
			<header id="header">
    
			<section class="topBar">
			<?php
				$logoUrl  = generateUrl('image', 'logo', array( 'meduim', CONF_WEBSITE_LOGO ));
			?>
				<figure class="logo"><a href="<?php echo generateUrl('home');?>"><img src="<?php echo $logoUrl;?>" alt="Legitpapers" title="Legitpapers"></a></figure>
				<div class="right">
				
					<div class="timesection">
						<div class="date"><?php $date = explode(" ",addTimezone(date("l M d, Y, H:i"), CONF_TIMEZONE));echo $date['0'];?></div><div class="time"><?php echo $date['1'];?></div>
					</div>
			   
					<ul class="topLinks">
						<li>
							<a href="#" class="user"><span class="arrow"></span>Welcome <span class="redtext"> <?php echo ucfirst($_SESSION['logged_admin']['admin_username']); ?></span></a>
							<div class="dropWrapper">
								<div class="inner">
									<ul class="dropLinks">
										<li><a href="<?php echo generateUrl('configurations');?>">My Account</a></li>
										<li><a href="<?php echo generateUrl('admin', 'logout'); ?>">Logout</a></li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</section>
			<?php $arr = explode('/',$_SERVER['REQUEST_URI']);?>
      
			<section class="navs">
				<div id="cbp-hrmenu" class="navWrap cbp-hrmenu">
					<ul class="navigations">
						<li <?php if($arr['2']=='home'){ echo'class="current"';}?>><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_home.png" alt=""> Dashboard</a></li>
						<li <?php if($arr['2']=='orders' || $arr['2']=='rating'){ echo'class="current"';}?>>
							<a href="javascript:void(0);"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_deals.png" alt="">ORDERS</a>
							<div class="cbp-hrsub">
								<div class="fixedWrap">
									<ul>
										<li><a href="<?php echo generateUrl('orders'); ?>">ORDERS MANAGEMENT</a></li>
										<li><a href="<?php echo generateUrl('rating'); ?>">Ratings & Review Management</a></li>
									</ul>
								</div>
							</div>
						</li>
						<li <?php if($arr['2']=='blogcategories' || $arr['2']=='blogposts' || $arr['2']=='blogcomments' || $arr['2']=='blogcontributions'){ echo'class="current"';}?>>
							<a href="javascript:void(0);"><img src="<?php echo CONF_WEBROOT_URL;?>images/iconblog.png" alt="">Blog Mgnt</a>
							<div class="cbp-hrsub">
								<div class="fixedWrap">
									<ul>
										<li><a href="<?php echo generateUrl('blogcategories'); ?>">Manage Blog Categories</a></li>
										<li><a href="<?php echo generateUrl('blogposts'); ?>">Manage Blog Posts</a></li>
										<li><a href="<?php echo generateUrl('blogcomments'); ?>">Manage Blog Comments</a></li>
										<li><a href="<?php echo generateUrl('blogcontributions'); ?>">Manage Blog Contributions</a></li>
									</ul>
								</div>
							</div>
						</li>
						
						<li <?php if($arr['2']=='money' || $arr['2']=='transactions'){ echo'class="current"';}?>>
							<a href="javascript:void(0);"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_money.png" alt="">Money Management</a>
							<div class="cbp-hrsub">
								<div class="fixedWrap">
									<ul>
										<li><a href="<?php echo generateUrl('money');?>">Overview</a></li>
										<li><a href="<?php echo generateUrl('transactions', 'withdrawal_requests');?>">FUNDS WITHDRAWAL REQUESTS</a></li>
									</ul>
								</div>
							</div>
						</li>
						
						
							
						<li <?php if($arr['2']=='user'){ echo'class="current"';}?>>
							<a href="<?php echo generateUrl('user', 'user_listing'); ?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_users.png" alt=""> Users</a>							
						</li>
							
						<li <?php if($arr['2']=='contentpages' || $arr['2']=='emailtemplate'){ echo'class="current"';}?>>
							<a href="javascript:void(0);"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_cms.png" alt="">CMS</a>
							<div class="cbp-hrsub">
								<div class="fixedWrap">
									<ul>
										<!--<li><a href="<?php //echo generateUrl('contentpages', 'home');?>"><?php //echo 'Manage Home Page'?></a></li>-->
										<li><a href="<?php echo generateUrl('contentpages');?>"><?php echo 'Manage Content Pages'?></a></li>
										<li><a href="<?php echo generateUrl('cmsblock');?>"><?php echo 'Manage Page Blocks'?></a></li>
										<li><a href="<?php echo generateUrl('emailtemplate'); ?>"><?php echo 'Email Template'; ?></a></li>
										<li><a href="<?php echo generateUrl('contentpages','service_tag'); ?>"><?php echo 'Manage Infotips'; ?></a></li>
										<li><a href="<?php echo generateUrl('faq'); ?>"><?php echo 'Manage FAQ\'s'; ?></a></li>
										<li><a href="<?php echo generateUrl('howitworks'); ?>"><?php echo 'Manage How It Works Steps'; ?></a></li>
									</ul>
								</div>
							</div>
						</li>
						<li <?php if($arr['2']=='configurations' || $arr['2']=='language' || $arr['2']=='countries' || $arr['2']=='work' || $arr['2']=='disciplines' || $arr['2']=='paper' || $arr['2']=='academic' || $arr['2']=='citation'){ echo'class="current"';}?>>
							<a href="javascript:void(0);"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_settings.png" alt=""> Settings</a>
							<div class="cbp-hrsub">
								<div class="fixedWrap">
									<ul>
										<li><a href="<?php echo generateUrl('configurations'); ?>"><?php echo 'GENERAL SETTINGS'; ?></a>
										
										<li><a href="<?php echo generateUrl('language'); ?>"><?php echo 'LANGUAGE MANAGEMENT'; ?></a></li>
										<li><a href="<?php echo generateUrl('countries'); ?>"><?php echo 'MANAGE COUNTRIES'; ?></a></li>
										<!--<li><a href="<?php// echo generateUrl('work'); ?>"><?php// echo 'MANAGE WORK FIELD'; ?></a></li>-->
										<li><a href="<?php echo generateUrl('disciplines'); ?>"><?php echo 'MANAGE DISCIPLINES'; ?></a></li>
										<li><a href="<?php echo generateUrl('paper'); ?>"><?php echo 'MANAGE PAPER TYPE'; ?></a></li>
										<li><a href="<?php echo generateUrl('academic'); ?>"><?php echo 'ACADEMIC DEGREES'; ?></a></li>
										<li><a href="<?php echo generateUrl('citation'); ?>"><?php echo 'MANAGE CITATION STYLE'; ?></a></li>
										<li><a href="<?php echo generateUrl('services'); ?>"><?php echo 'MANAGE SERVICES TYPE'; ?></a></li>
										<li><a href="<?php echo generateUrl('customhtml'); ?>"><?php echo 'MANAGE CUSTOM HTML'; ?></a></li>
										<li><a href="<?php echo generateUrl('meta/listing'); ?>"><?php echo 'MANAGE META TAGS'; ?></a></li>
									</ul>
								</div>
							</div>
						</li>
						<li <?php if($arr['2']=='question'){ echo'class="current"';}?>>
							<a href="<?php echo generateUrl('question');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_reports.png" alt="">GRAMMAR TEST MANAGEMENT</a></li>
						
						<li <?php if($arr['2']=='admin'){ echo'class="current"';}?>>
							<a href="<?php echo generateUrl('admin','password_change_form'); ?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/icon_cp.png" alt=""> Change Password</a></li>
					</ul>
					 
				</div>
			</section>
      
		</header>
		<div id="body">