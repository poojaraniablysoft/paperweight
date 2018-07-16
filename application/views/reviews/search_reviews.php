<?php// echo '<pre>'.print_r($user_reviews['page'],true).'</pre>';exit;?>
<div class="row_12 border-bottom">
  <div class="feedback"> <strong><?php echo Utilities::getLabel( 'L_Feedback' ); ?>:</strong>
	<ul class="fliter-by">
	  <li><a onclick="loadReviews(<?php echo $arr_user;?>)"><?php echo Utilities::getLabel( 'L_Show_all' ); ?><i class="icon ion-chevron-down"></i></a>
		<div class="sub-menu">
		  <ul>
			<li><a onclick="loadReviewsByRatings(<?php echo $arr_user;?>,5)"><?php echo Utilities::getLabel( 'L_5_Star_Rating' ); ?></a></li>
			<li><a onclick="loadReviewsByRatings(<?php echo $arr_user;?>,4)"><?php echo Utilities::getLabel( 'L_4_Star_Rating' ); ?></a></li>
			<li><a onclick="loadReviewsByRatings(<?php echo $arr_user;?>,3)"><?php echo Utilities::getLabel( 'L_3_Rating_or_below' ); ?></a></li>
		  </ul>
		</div>
	  </li>
	</ul>
  </div>
  <div class="page-order"><?php echo Utilities::getLabel( 'L_Page' ); ?> <?php echo $user_reviews['page'];?> <?php echo Utilities::getLabel( 'L_of' ); ?> <?php echo $user_reviews['pages'];?> <strong>(<?php echo $user_reviews['total_records'];?> <?php echo Utilities::getLabel( 'L_orders' ); ?>)</strong></div>
</div>
<?php foreach($user_reviews['feedback'] as $k=>$fbk) { ?>
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
	  <a class="writers-feedback"><?php echo Utilities::getLabel( 'L_Writer_feedback' ); ?></a>
	  <div class="comt-box" style="display: none; overflow: hidden;">
		<div class="padding_20"> <?php echo ($fbk['review']['comments'] != '' ? $fbk['review']['comments']:'Not Reviewed Yet!');?></div>
	  </div>
	</div>
<?php } 
if(count($user_reviews['feedback']) == 0) {?>
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
<?php }
?>
<div class="pagination">
<?php echo generatePagingStringHtml(intval($user_reviews['page']),$user_reviews['pages'],'search_reviews(xxpagexx,'.intval($arr_user).')');?>
</div>