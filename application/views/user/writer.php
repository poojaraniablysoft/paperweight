<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
/* if(User::isUserLogged()) {
	include CONF_THEME_PATH . 'page_bar.php';
} */
echo '<script type="text/javascript">var isUserLogged = "'.User::isUserLogged().'"</script>';
?>

<!--header end here-->
<div id="body">
  <div class="row greyBg strip">
    <div class="fix-container">
	  <div class="row_12">
        <div class="writer-info">
          <figure class="img-sect"><img src="<?php echo generateUrl('user','photo',array($arr_user['user_id'],130,130));?>" alt="writer"></figure>
          <h5><?php echo $arr_user['user_first_name'].' '.$arr_user['user_last_name'];?></h5>
          <div class="average"><strong><?php echo Utilities::getLabel( 'L_Average_rating' ); ?></strong> :
            <?php if ($arr_user['user_rating'] > 0) { ?>
				<figure class="star-rtng"><div class="ratingsWrap ratingSection"><span class="p<?php echo $arr_user['user_rating']*2; ?>"></span></div><span class="rating"><?php echo $arr_user['user_rating'];?></span></figure>
			<?php } else { ?>
				<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
			<?php } ?>
          </div>
          <p><strong><?php echo Utilities::getLabel( 'L_Member_Since' ); ?></strong> : <?php echo displayDate($arr_user['user_regdate']);?></p>
          <p class="language">
			<?php 
				$html = $html_drop = '';
				
				foreach($arr_user['user_languages'] as $k=>$v) {
					$html .= ($v['lang_name'] != '')?'<a class="btn no-cursor">'.$v['lang_name'].'</a>':'';
				}
				echo $html;				
			?>
		  </p>
        </div>
        <div class="cols_3">
          <div class="txt-box">
            <div class="grydtn-btn"><span><?php echo $arr_user['activities']['orders_in_progress'];?></span>
              <p><?php echo Utilities::getLabel( 'L_Orders_In Progress' ); ?></p>
            </div>
            <div class="grydtn-btn"><span><?php echo $arr_user['activities']['orders_completed'];?></span>
              <p><?php echo Utilities::getLabel( 'L_Orders_Completed' ); ?></p>
            </div>			
			<?php if(!User::isWriter()) {?>
				<a class="initialism btn light-grydtn" href="javascript:void(0);" onclick="chooseWriter(<?php echo $arr_user['user_id']; ?>)"><?php echo Utilities::getLabel( 'L_Request_Writer' ); ?></a> 
			<?php }?>
				</div>
        </div>
      </div>
      <div class="about-us">
        <p><strong><?php echo Utilities::getLabel( 'L_About' ); ?> <?php echo $arr_user['user_first_name'].' '.$arr_user['user_last_name'];?></strong></p>
        <p class="expander"><?php echo $arr_user['user_more_cv_data'];?> </p>
      </div>
	  <div class="row_8">
		<a class="theme-btn bck-btn" href="<?php echo generateUrl('user','top_writers');?>"><i class="icon ion-android-arrow-back"></i><?php echo Utilities::getLabel( 'L_Back_To_Top_Writers' ); ?></a>
	  </div>
    </div>
  </div>
  
  <!--row end here-->
  
 
  <!--row end here-->
  
  <div class="row strip">
    <div class="fix-container">
	  <div class="complete">
		<h5><?php echo Utilities::getLabel( 'L_Completed_Orders_In' ); ?>:</h5>
			<?php foreach($disciplines as $key=>$disc) { ?>
				<a href="javascript:void(0);" class="dis-btn" onclick="loadReviews(<?php echo $arr_user['user_id'];?>,<?php echo $key;?>)"><?php echo $disc;?> </a>
				<!--a href="<?php #echo generateUrl('writer',seoUrl($arr_user['user_screen_name']),array(seoUrl($disc)));?>" class="dis-btn"><?php #echo $disc;?></a-->
			<?php }?>
	  </div>
      <div class="complete-order">
        <?php if(is_array($user_feedbacks['feedback']) && count($user_feedbacks['feedback'])>0){ ?>
		<div id="reviews_list">
		  <div class="row_12 border-bottom">
			  <div class="feedback"> <strong><?php echo Utilities::getLabel( 'L_Feedback' ); ?>:</strong>
				<ul class="fliter-by">
				  <li><a onclick="loadReviews(<?php echo $arr_user['user_id'];?>)"><?php echo Utilities::getLabel( 'L_Show_all' ); ?><i class="icon ion-chevron-down"></i></a>
					<div class="sub-menu">
					  <ul>
						<li><a onclick="loadReviewsByRatings(<?php echo $arr_user['user_id'];?>,5)"><?php echo Utilities::getLabel( 'L_5_Star_Rating' ); ?></a></li>
						<li><a onclick="loadReviewsByRatings(<?php echo $arr_user['user_id'];?>,4)"><?php echo Utilities::getLabel( 'L_4_Star_Rating' ); ?></a></li>
						<li><a onclick="loadReviewsByRatings(<?php echo $arr_user['user_id'];?>,3)"><?php echo Utilities::getLabel( 'L_3_Rating_or_below' ); ?></a></li>
					  </ul>
					</div>
				  </li>
				</ul>
			  </div>
			  <div class="page-order"><?php echo Utilities::getLabel( 'L_Page' ); ?> <?php echo $user_feedbacks['page'];?> <?php echo Utilities::getLabel( 'L_of' ); ?> <?php echo $user_feedbacks['pages'];?> <strong>(<?php echo $user_feedbacks['total_records'];?> <?php echo Utilities::getLabel( 'L_orders' ); ?>)</strong></div>
		  </div>
		  <?php foreach($user_feedbacks['feedback'] as $k=>$fbk) { ?>
			<div class="feedback-wrap">
			  <div class="row_12">
				<div class="cols_4">
				  <h2><?php echo ucwords($fbk['task_topic']);?></h2>
				  <h3><?php echo $fbk['paptype_name'];?>, <?php echo $fbk['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?>, <span><?php echo displayDate($fbk['posted_on'],true);?></span></h3>
				</div>
				<div class="cols_5"> <i class="icon ion-checkmark"></i>
				  <div class="rating">#<?php echo $fbk['task_ref_id']?>, <?php echo Utilities::getLabel( 'L_Completed' ); ?></div>
				</div>
			  </div>
			  <div class="text-wrapper">
				<div class="top-bar">
				  <h4><?php echo Utilities::getLabel( 'L_Customer_feedback' ); ?></h4>
					<figure class="star-rtng">
				  <?php if ($fbk['reviewer_rating']['average_rating'] > 0) { ?>
					<div class="ratingsWrap ratingSection"><span class="p<?php echo $fbk['reviewer_rating']['average_rating']*2; ?>"></span></div><span class="rating"><?php echo $fbk['reviewer_rating']['average_rating'];?></span>
				  <?php } else { ?>
					<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
				  <?php } ?>
					</figure>
				</div>
				<p><?php echo $fbk['comments'];?></p>
				<p><strong><?php echo $fbk['reviewee'];?></strong></p>
			  </div>
			  <span class="writers-feedback"><?php echo Utilities::getLabel( 'L_Writer_feedback' ); ?></span>
			  <div class="comt-box" style="display: none; overflow: hidden;">
				<div class="padding_20"> <?php echo ($fbk['review']['comments'] != '' ? $fbk['review']['comments']:'Not Reviewed Yet!');?></div>
			  </div>
			</div>
		  <?php }
			if(count($user_feedbacks['feedback']) == 0) { ?>
				<div class="no_result">
					<div class="no_result_icon">
						<?php
							$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
						?>
						<img src="<?php echo $logoUrl;?>">
					</div>
					<div class="no_result_text">
						<h5><?php echo Utilities::getLabel( 'L_No_Review_found' ); ?></h5>
					</div>
				</div>
		  <?php	}
		  ?>		  
		  <div class="pagination"><?php echo generatePagingStringHtml(intval($user_feedbacks['page']),$user_feedbacks['pages'],'search_reviews(xxpagexx,'.intval($arr_user['user_id']).')');?></div>
		</div>
		<?php } ?>
      </div>
    </div>
  </div>
</div>
<!--body end--> 