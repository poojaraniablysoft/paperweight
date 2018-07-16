<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
echo '<script type="text/javascript">var isUserLogged = "'.User::isUserLogged().'"</script>';
?>
<div id="banner">
	<div class="fix-container">
		<div class="banner-in">
			<h1><?php echo renderHtml($home_1_title);?></h1>
			<?php echo renderHtml($home_1_content);?>
			<section class="orderForm">
				<?php echo renderHtml($home_page_form); ?>
				<?php echo $frm->getFormtag(); ?>
					<table class="orderForm-tbl" cellpadding="0" cellspacing="0">
						<thead>
						  <tr>
							<td colspan="3"><h2><?php echo renderHtml($home_page_form_title);?></h2></td>
							<td colspan="2"><span><?php echo Utilities::getLabel( 'H_Free_Fast_&_Safe' ); ?></span></td>
						  </tr>
						</thead>
						<tbody>
						  <tr>
							<?php if(!User::isUserLogged() || (User::isUserLogged() && User::isWriter())) {?>
							<td><?php echo $frm->getFieldHTML('user_email'); ?></td>
							<?php }else { ?>
							<td><?php echo $frm->getFieldHTML('task_topic'); ?></td>
							<?php }?>
							<td><?php echo $frm->getFieldHTML('task_due_date');?></td>
							<td><?php echo $frm->getFieldHTML('task_paptype_id');?></td>
							<td><?php echo $frm->getFieldHTML('task_pages');?>
								<span class="task_pages"><img class="error-icn" src="images/error-icn.png"/><div class="tootltip"><p><?php echo sprintf( Utilities::getLabel( 'L_words_pages' ), CONF_WORDS_PER_PAGE ); ?></p></div></span></td>
							<td><?php echo $frm->getFieldHTML('btn_submit');?></td>
						  </tr>
						</tbody>
					</table>
				</form><?php echo $frm->getExternalJS(); ?>			
			</section>
		</div>
	</div>
</div>
<div id="body">
	<div class="row whiteBg strip">
		<div class="fix-container">
			<div class="counter-sectn">
				<div class="colm">
					<div class="img-sctn"><img src="<?php echo CONF_WEBROOT_URL;?>images/file-icon.png" alt="" /></div>
					<div class="txt-sctn"><span class="number"><?php echo $content['order_completed'];?></span> <span class="title"><?php echo Utilities::getLabel( 'L_Completed_Work' ); ?></span></div>
				</div>
				<div class="colm">
					<div class="img-sctn"><img src="<?php echo CONF_WEBROOT_URL;?>images/pen-icn.png" alt="" /></div>
					<div class="txt-sctn"><span class="number"><?php echo $content['writers_active'];?></span> <span class="title"><?php echo Utilities::getLabel( 'L_Qualified_Writers' ); ?></span></div>
				</div>
				<div class="colm">
					<div class="img-sctn"><img src="<?php echo CONF_WEBROOT_URL;?>images/author-icn.png" alt="" /></div>
					<div class="txt-sctn"><span class="number"><?php echo $content['writers_online'];?></span> <span class="title"><?php echo Utilities::getLabel( 'L_Writers_Online_Now' ); ?></span></div>
				</div>
				<div class="colm">
					<div class="img-sctn"><img src="<?php echo CONF_WEBROOT_URL;?>images/star-icn.png" alt="" /></div>
					<div class="txt-sctn"><span class="number"><?php echo $content['avg_user_ratings'];?></span> <span class="title"><?php echo Utilities::getLabel( 'L_Writers_Star_Ratings' ); ?></span></div>
				</div>
			</div>
		</div>
	</div>
	<?php if($home_2_content != '' || $home_3_content != '') { ?>
	<div class="row greyBg paddng">
		<div class="fix-container">
			<div class="about-sectn cmsContainer">
				<?php if($home_2_content != '') { ?>
				<div class="vdo-sectn">
					<h2><?php echo renderHtml($home_2_title); ?></h2>
					<?php echo renderHtml($home_2_content); ?>
					<div class="vdo-frame">
						<iframe src="<?php echo $video_url;?>" width="500" height="340" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
					</div>
				</div>
				<?php } if($home_3_content != '') { ?>
				<div class="why-choose-us">
					<h2><?php echo renderHtml($home_3_title); ?></h2>
					<?php echo renderHtml($home_3_content); ?>					
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
	<!--Top writers-->
	<?php if($home_5_content != '') { ?>
	<div class="row whiteBg paddng strip">
		<div class="fix-container">
		  <div class="our-wrtiter">
			<h2 class="page-title"><?php echo renderHtml($home_5_title);?></h2>
			<?php echo renderHtml($home_5_content); ?>
			<ul class="wrtiter-list">
			 <?php foreach ($arr_writers['data'] as $key=>$ele) { ?> 
			  <li>
				<div class="profile">
				  <div class="img-sectn"><a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><img src="<?php echo generateUrl('user', 'photo', array($ele['user_id'],100,100)); ?>"></a></div>
				  <div class="txt-detail">
					<h2><a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><?php echo ucfirst($ele['user_screen_name']); ?></a></h2> 
					<div class="language">
					  <?php 
						$html = $html_drop = '';
						
						foreach($ele['user_languages'] as $k=>$v) {
							if($k < 2){
								$html .= ($v['lang_name'] != '')?'<a class="btn no-cursor">'.ucfirst($v['lang_name']).'</a>':'';
							}else {
								$html_drop .= '<li><a>'.ucfirst($v['lang_name']).'</a></li>';
							}
						}
						echo $html;
						
						if(count($ele['user_languages']) > 2) {
					  ?>                 
					  <ul>
						<li><span class="plus">+</span>			   
							<div class="tooltip">				
								<ul>
									<?php echo $html_drop;?>					
								</ul>
							</div>
						</li>			   
					  </ul>
					  <?php }?>
		   
					</div>
					<?php if ($ele['user_rating'] > 0) { ?>
						<div class="ratingsWrap"><span class="p<?php echo $ele['user_rating']*2; ?>"></span></div>
					<?php } else { ?>
						<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
					<?php } ?>  
								
				  </div>
				</div>

				<div class="txt-box clearfix">
					<div class="txt-content"><p><?php echo (strlen($ele['user_more_cv_data']) > 300) ? substr($ele['user_more_cv_data'],0,299).'...':$ele['user_more_cv_data'];?>    </p></div>
					<div class="grydtn-btn"><span><?php echo $ele['orders_in_process']; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_In_Progress' ); ?></p></div>
					<div class="grydtn-btn"><span><?php echo $ele['orders_completed']; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_Completed' ); ?></p></div>
				</div>            
				
				<ul class="request-writer clearfix">				              
				  <?php if(!User::isWriter()) {?><li><a class="initialism" onclick="chooseWriter(<?php echo $ele['user_id']; ?>)" href="javascript:void(0);"><?php echo Utilities::getLabel( 'L_Request_Writer' ); ?></a></li><?php }?>
				<li>  
				  <a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><?php echo Utilities::getLabel( 'L_View_Details' ); ?></a>
				</li>			
				</ul>
			  </li>
			  
			  <?php } ?>
			</ul>
			<div class="wrap-wrtiter-list"> <a href="<?php echo generateUrl('top-writers');?>" class="theme-btn"><?php echo Utilities::getLabel( 'L_View_All' ); ?></a> </div>
		  </div>
		</div>
	</div>
	<?php } ?>
	<!--Testimonials-->
	<?php if($home_4_content != '') { ?>
	<div class="row greyBg paddng">
		<div class="fix-container">
			<div class="reviews">
				<h2 class="page-title"><?php echo renderHtml($home_4_title);?></h2>
				<?php echo renderHtml($home_4_content); ?>
				<div class="review-tab" id="horizontalTab">
					<ul class="resp-tabs-list">
						<li><a class="toggle-button" href="#tabs1"><span class="testi_cl_img"><img alt="img" src="<?php echo CONF_WEBROOT_URL;?>images/customer-icon.png" ></span>&nbsp;<?php echo Utilities::getLabel( 'L_Clients_review' ); ?></a></li>
						<li><a class="toggle-button" href="#tabs2"><span class="testi_cl_img"><img alt="img" src="<?php echo CONF_WEBROOT_URL;?>images/writer-icon.png"></span>&nbsp;<?php echo Utilities::getLabel( 'L_Writers_review' ); ?></a></li>
					</ul>
					<div class="resp-tabs-container">
						<div class="rivews-box tab_content" id="tabs1">
						
							<div class="slider_roundthumbs slideer1">
								<div class="viewport">
									<ul class="overview">
										<?php foreach ($arr_reviews['review'] as $key=>$ele) { ?>
											<li>
												<div class="slidecontent">
													<div class="img-sctn">
														<div class="client-img"><a href="<?php echo generateUrl('writer',seoUrl($ele['reviewee']));?>"><img src="<?php echo generateUrl('user', 'photo', array($ele['user_id']));?>"/></a></div>
													</div>
													<div class="txt-sctn">
														<p>
														<?php if(strlen($ele['comments'])>=250){
															echo substr($ele['comments'],0,250) . '...';
														} else {
															echo $ele['comments'];
														} ?>
														</p>
													<span><strong><?php echo $ele['customer'];?></strong></span> 
													</div>
												</div>
											</li>
										<?php } ?>
									</ul>
									</div>
									<ul class="bullets">
										<?php
											$i = 0;
										foreach ($arr_reviews['review'] as $key=>$ele) { 
											echo '<li><a href="#" class="bullet" data-slide="'.$i++.'">'.$i.'</a></li>';
											
										} ?>									
									</ul>
								
							</div>								
						</div>
						<div class="rivews-box tab_content" id="tabs2">
						
							<div class="slider_roundthumbs slideer2">
								<div class="viewport">
									<ul class="overview">
										<?php foreach ($arr_feedback['feedbacks'] as $key=>$ele) { ?>
											<li>
												<div class="slidecontent">
													<div class="img-sctn">
														<div class="client-img"> <img src="<?php echo generateUrl('user', 'photo', array($ele['user_id'],100,100));?>"/> </div>
													</div>
													<div class="txt-sctn">
														<p>
														<?php if(strlen($ele['comments'])>=250){
															echo substr($ele['comments'],0,250) . '...';
														} else {
															echo $ele['comments'];
														} ?>
														</p>
													<span><strong><a href="<?php echo generateUrl('writer',seoUrl($ele['reviewee']));?>"><?php echo $ele['reviewee'];?></a></strong></span> </div>
												</div>
											</li>									
										<?php } ?>
									</ul>
									</div>
									<ul class="bullets">
										<?php
											$i = 0;
										foreach ($arr_feedback['feedbacks'] as $key=>$ele) { 
											echo '<li><a href="#" class="bullet" data-slide="'.$i.'">'.$i.'</a></li>';
											$i++;
										} ?>									
									</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--Latest Blogs-->
	<?php
		}
		if(isset($records) && is_array($records) && $home_6_content != ''){ 	
	?>
	<div class="row darkgry paddng">
		<div class="fix-container">
		  <div class="recent-blog">
			<h2 class="page-title"><?php echo renderHtml($home_6_title); ?></h2>
			<?php echo renderHtml($home_6_content); ?>
			<ul class="blog-list">
			<?php echo createHiddenFormFromPost('frmPaging', '', array(), array());
				foreach($records as $records1){
			?>
			  <li>
				<div class="img-sctn">
				  <div class="blog-img img-responsive"><img alt="" src="<?php echo generateUrl('image', 'post', array('medium', $records1['post_image_file_name'])); ?>"></div>
				</div>
				<div class="txt-sctn">
				  <div class="blog-detail"> <span><i class="icon ion-clock"></i> <?php echo dateFormat("d M, Y",$records1['post_published']);?></span>
					<h2><?php echo ucfirst(myTruncateCharacters($records1['post_title'], 20));?></h2>
					<p><?php 
						
						if(!empty($records1['post_short_description'])){
							$post_short_description = html_entity_decode($records1['post_short_description'], ENT_QUOTES, 'UTF-8');
							echo myTruncateCharacters($post_short_description, 100);
						}
						else{
							$post_content = html_entity_decode($records1['post_content'], ENT_QUOTES, 'UTF-8');
							echo myTruncateCharacters($post_content, 100);
						}
						?><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>"><?php echo Utilities::getLabel( 'L_more' ); ?></a>
					</p>
					<div class="blog-reviews">
						<span><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>"><i class="icon ion-ios-eye"></i> <?php echo $records1['post_view_count'];?></a></span>
						<span><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>#comment-form"><i class="icon ion-chatbubble-working"></i> <?php echo $records1['comment_count'];?></a></span>  </div>
				  </div>
				</div>
			  </li>
			<?php	} ?>
		
			  
			</ul>
			<div class="clr">&nbsp;</div>
			<a class="greenBtn" href="<?php echo CONF_WEBROOT_URL;?>blog"><?php echo Utilities::getLabel( 'L_View_All' ); ?></a>
		  </div>
		</div>
	</div>
	<?php
	} 
	if($home_7_content != '') {
	?>
	<div class="row strip paddng">
		<div class="fix-container">
			<div class="order-now-sectn">
				<h2 class="page-title"><?php echo renderHtml($home_7_title); ?></h2>
				<?php echo renderHtml($home_7_content); ?>
				<br>
				<a class="greenBtn" onclick="$('html, body').animate({scrollTop:120}, 'slow'); $('#user_email').focus();" href="#"><?php echo Utilities::getLabel( 'L_Order_Now' ); ?></a>
			</div>
		</div>
	</div>
	<?php }?>
<script type="text/javascript" src="<?php echo CONF_WEBROOT_URL;?>js/jquery.tinycarousel.js"></script>
<!-- <div class="index-order" >
    <h2>Hire expert writers fast and easy <span>from a reliable  custom writing service</span></h2>
	<div id="common_error_msg"></div>
	<div class="order-form">
		<div class="name">Place New Order <span>It's Free, Fast & Safe</span></div>
		[PLACE_ORDER_FORM]
	</div>
</div>

<div id="body">
    <div class="how-works">
		<div class="fix-container">
			<h2>Submit Your Paper Instructions For FREE!</h2>
			<ul>
				<li> <span class="icon"><img src="/images/one-icn.png" width="140" height="144" alt=""></span>
					<p>Get started for free. Post your essay within seconds.</p>
				</li>
				<li> <span class="icon"><img src="/images/two-icn.png" width="140" height="144" alt=""></span>
					<p>Compare prices, view writer profiles. Hire only the most suitable writer.</p>
				</li>
				<li> <span class="icon"><img src="/images/three-icn.png" width="140" height="144" alt=""></span>
					<p>Track progress, give suggestions. Pay once you are 100% satisfied.</p>
				</li>
			</ul>
		</div>
    </div>	
    <div class="why-us clearfix">
		<div class="fix-container">
			<div class="why-cells">
				<div class="cell">
					<h3>Why Us?</h3>
					<p>Lorem ipsum dolor sit amet, nsecte.</p>
					<div class="sq-box">
						<ul>
							<li>Satisfaction guaranteed pay only when satisfied </li>
							<li>Affordable prices starting at only $10/page </li>
							<li>Seamless communication with your writer 24/7 </li>
						</ul>
					</div>
				</div>
				<div class="cell">
					<h3>Video Guide</h3>
					<p>Watch a Quick Tour</p>
					<div class="video">[VIDEO_GUIDE]</div>
				</div>
				<div class="cell">
					<h3> Guarantees</h3>
					<p>Lorem ipsum dolor sit amet, nsecte.</p>
					<div class="sq-box">
						<ul>
							<li>100% plagiarism-free papers.</li>
							<li> Native English-speaking wnters.</li>
							<li>Guaranteed security and confidentiality. </li>
						</ul>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="current-activity">
				<h3>Current Activity</h3>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed diam nonummy nibh </p>
				<div class="activity-level clearfix">
					<div class="sts completed"><span>[ORDERS_COMPLETED]</span>&nbsp;Orders completed</div>
					<div class="sts active"><span>[WRITERS_ACTIVE]</span>&nbsp;Writers active</div>
					<div class="sts online"><span>[WRITERS_ONLINE]</span>&nbsp;Writers online now</div>
				</div>
				<div class="btns-row">
					<a href="[TESTIMONIALS_LINK]" class="btn-white">Read Testimonials</a>
					<a href="[NEWS_LINK]" class="btn-white right">Read Project News</a>
				</div>
			</div>
		</div>
    </div>
    <div class="white-row">
		<div class="fix-container">
			<div class="cta-box">
				<div class="cta-line">
					<h3>Submit Essay Instructions <strong> For FREE!</strong></h3>
					<p>Get your essay written at the lowest prices in the market. Prices starting at<strong> $7.50/page</strong></p>
				</div>
				<a href="javascript:void(0);" class="btn-theme" onclick="$('html, body').animate({scrollTop:120}, 'slow'); $('#user_email').focus();">Order Now</a>
			</div>
		</div>
    </div>
    <div class="blue-bar clearfix">
		<div class="fix-container">
			<h2>Our Unique Features</h2>
			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing <br> elit. Sed diam nonummy nibh </p>
			<div class="unique-time-line">
				<ul>
					<li class="event left">
						<div class="icon"><img src="/images/personalizedapproach.png" width="107" height="100" alt=""></div>
						<div class="txt">
							<h3>Personalized approach to academic help!</h3>
							<p>Place your order, shortlist and contact your preferred writer within minutes based on their reviews, profile and experience. </p>
							<a href="[MORE_INFO_LINK]" class="btn-white">More Info</a>
						</div>
					</li>
					<li class="event right">
						<div class="icon"><img src="/images/manageyourpaper.png" width="107" height="100" alt=""></div>
						<div class="txt">
							<h3>Manage your paper! </h3>
							<p>Place your order, shortlist and contact your preferred writer within minutes based on their reviews, profile and experience. </p>
							<a href="[MORE_INFO_LINK]" class="btn-white">More Info</a>
						</div>
					</li>
					<li class="event left">
						<div class="icon"><img src="/images/beinvolved.png" width="107" height="100" alt=""></div>
						<div class="txt">
							<h3>Be involved. Stay updated! </h3>
							<p>Sit back and watch as your writer starts to work on your paper, review their progress and request for amendments if need be. </p>
							<a href="[MORE_INFO_LINK]" class="btn-white">More Info</a>
						</div>
					</li>
					<li class="event right">
						<div class="icon"><img src="/images/timelycommunication.png" width="107" height="100" alt=""></div>
						<div class="txt">
							<h3>Timely communication!</h3>
							<p>Chat with your writer and make your suggestions, ideas and expectations known throughout till your paper is delivered. </p>
							<a href="[MORE_INFO_LINK]" class="btn-white">More Info</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
    </div>
    <div  class="clear"></div>
	[TESTIMONIALS_SECTION]
    <div class="light-blue-row clearfix">
		<div class="fix-container">
			<div class="feature-piller blue">
				<div class="name">Order Custom Essays<br>
				From LegitPapers.com</div>
				<h3>Why should I trust you to write my essay?
				The answer is simple. Are you looking for a;</h3>
				<ul class="listing">
					<li>High quality and reliable academic writing services?</li>
					<li>Custom essay that will meet your needs and exceed your expectations?</li>
					<li>Custom writing service that will meet your deadline?</li>
					<li>Reasonably priced custom essay?</li>
					<li>Custom writing service that will guarantee your security and confidentiality?</li>
				</ul>
				<p>If your answer is yes, then you have absolutely found the best place to seek help and order custom essays. LegitPapers.com revolutionary custom essay writing offers you an opportunity to choose your writer, sit back, relax and enjoy your free time. Of course you will be required to review the progress of your paper. </p>
				<a href="[COMPLETED_PROJECTS_LINK]" class="btn-white">View Latest Completed Projects</a>
			</div>
			<div class="feature-piller white">
				<div class="name">The Most Reliable<br>
				Essay Writing Service</div>
				<ul class="listing">
					<li>Pay for what you see. At LegitPapers.com, you only pay after you are 100% satisfied! </li>
					<li>High quality custom papers. All papers are written from scratch and strictly as per the instructions provided by the customer. </li>
					<li>Plagiarism free guarantee - Our writers use the most advanced plagiarism detection system to scan for any match before a paper is delivered to the customer. </li>
					<li>On-time delivery - No matter how urgent your situation is, our writers honor deadlines. </li>
					<li>Hire from a pool of professional and experienced native writers. Our writers have verified academic credentials, writing skills and several years of experience. </li>
					<li>Be safe with us. We guarantee you 100% security and confidentiality. The information you provide to us is encrypted and is never disclosed to any third parties. </li>
				</ul>
				<a href="[REVIEWS_LINK]" class="btn-white">Reviews From LegitPapers.com Users</a>
			</div>
		</div>
    </div>
    <div class="blue-second clearfix">
		<div class="fix-container">
			<div class="cta-box">
				<div class="cta-line">
					<h3>Submit Your Instructions to Writers <strong> For FREE!</strong></h3>
				</div>
				<a href="javascript:void(0);" class="btn-theme" onclick="$('html, body').animate({scrollTop:120}, 'slow');$('#user_email').focus();">Order Now</a>
			</div>
		</div>
    </div>
</div> -->
<script>
/* var today = new Date();
var tomorrow = new Date(today.getTime() + 24 * 60 * 60 * 1000);
console.log(tomorrow);
$('#task_due_date').datepicker({
    minDate: tomorrow,
    
}); */
var serverDate = "<?php echo date('Y-m-d'); ?>" ;
</script>