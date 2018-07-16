<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

global $order_status;

//echo "<pre>".print_r($reviews,true)."</pre>";
//echo "<pre>".print_r($feedreceived,true)."</pre>";
?>

<script type="text/javascript">
	var filter = "<?php echo $filter; ?>";
</script>
<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
	<div class="order_page">
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ) . '</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>
<div id="reviews_history"> 
<?php 
$i=1;
foreach($feedreceived['feedback'] as $val) { ?>
<div class="white-box review-page clearfix <?php echo ($i>1) ? 'padding_30':''?>">
	<h2>
		<a href="<?php echo generateUrl('task', 'order_process', array($val['task_id'])); ?>">#<?php echo $val['task_ref_id'];?></a>
		&nbsp;
		<span><?php echo $val['task_topic'];?></span>
		<span class="lightxt">(<?php echo $val['paptype_name'];?> - <?php echo $val['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?>)</span>
	</h2>
	<div class="border-box">
		<div class="sectionphoto">
			<figure class="photo"><img src="<?php echo generateUrl('user', 'photo', array($val['rev_reviewer_id']));?>" alt="<?php echo $val['reviewee']; ?>"></figure>
			<?php if ($val['user_rating'] > 0) { ?>
				<div class="ratingsWrap"><span class="p<?php echo ($val['user_rating']*2); ?>"></span></div>
			<?php } else { ?>
				<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
			<?php } ?>
		</div>
		<div class="writer-review">
			<?php if ($val['feed_rating']['average_rating'] > 0) { ?>
				<div class="ratingsWrap"><span class="p<?php echo ($val['feed_rating']['average_rating']*2); ?>"></span></div>
			<?php } else { ?>
				<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
			<?php } ?>
			<h3><?php if(User::isWriter()){ echo Utilities::getLabel( 'L_Customer_Feedback' ); } else { echo Utilities::getLabel( 'L_Writer_Feedback' ); }?></h3>
			<p><?php echo nl2br($val['comments']); ?></p>
		</div>
	</div>
</div>
<?php $i++; } 
if(User::isWriter())
{
	$user_type = User::isWriter();
}
else{
	$user_type =0;
}
 ?>
</div>
<div class="pagination" ><?php echo generatePagingStringHtml($feedreceived['page'],$feedreceived['pages'],"getFeedback(xxpagexx,$user_type);"); ?></div>
<?php 
if(count($feedreceived['feedback']) < 1 ){ ?>
<div class="white-box review-page">
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
</div>
<?php }