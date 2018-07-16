<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>
<div id="body">
  <div class="row whiteBg strip">
    <div class="fix-container">
      <div class="blog-wrap"> 
        
        <!--left penal start here-->
        
        <div class="blog-left"><?php if($post_data || is_array($post_data)){?>
          <div class="post">
            <div class="post-title clearfix">
              <div class="date"> <?php echo dateFormat("d",$post_data['post_published']);?><span><?php echo dateFormat("M",$post_data['post_published']);?></span> </div>
              <h3><?php echo ucfirst($post_data['post_title']);?></h3>
              <ul class="cmt-box">
                <li> <i class="icon ion-eye"></i><?php echo Utilities::getLabel( 'L_Views' ); ?><span><?php echo $post_data['post_view_count'];?></span> </li>
                <li><i class="icon ion-chatbubble-working"></i><a href="#comments"><?php echo Utilities::getLabel( 'L_Comments' ); ?></a> <span><?php echo $comment_count;?></span></li>
				<?php if(isset($post_data['post_categories']) && is_array($post_data['post_categories']) && count($post_data['post_categories'])){	?>
				<li> <i class="icon ion-cube"></i>
					<?php 
						$post_category_counter = 1;
						foreach($post_data['post_categories'] as $cat){
							echo '<a href="'.generateUrl('blog', 'category', array($cat['category_seo_name']) ).'">'.$cat['category_title'].'</a>';
							if($post_category_counter != count($post_data['post_categories'])){
								echo ', ';
							}
							$post_category_counter++;
						}
						if(!empty($post_data['post_contibutor_name']) && $post_data['post_contibutor_name']!=''){ ?>	
				<li><i class="icon ion-person"></i><?php echo Utilities::getLabel( 'L_Author' ); ?><span><?php echo $post_data['post_contibutor_name'];?></span></li>	
				<?php }	?>
				</li>
				<?php } ?>
              </ul>
            </div>
			<?php 
				if(!empty($slider_images) && count($slider_images)>1)
				{
				?>
					<div id="slider" class="flexslider">
						<ul class="slides" id="post_slider">
							<?php foreach($slider_images as $slide_images1){?>
								<li>
									<img src="<?php echo generateUrl('image', 'post', array('large', $slide_images1['slide_images']));?>" />
								</li>
							<?php } ?>
						</ul>	
					</div>

					<div class="flexslider">
						<ul class="slides" id="post_carousel">
							<?php
							foreach($slider_images as $slide_images1){
								echo '<li><div class="slidethumb"><img src="'.generateUrl('image', 'post', array('thumb', $slide_images1['slide_images'])).'"></div></li>';
							}
							?>
						</ul>
					</div>
				<?php	
				}
				else{
					if(!empty($slider_images[0]['slide_images'])){
						echo '<figure class="post-image"><img src="'.generateUrl('image', 'post', array('large', $slider_images[0]['slide_images'])).'"></figure>';
					}
				}	
			?>
            <div class="post_content"><?php echo html_entity_decode($post_data['post_content'], ENT_QUOTES, 'UTF-8'); ?> </div>
            <div class="sharewrap">
				
				<div class="addthis_toolbox addthis_default_style" addthis:title="<?php echo $record['post_title']; ?>" addthis:url="<?php echo CONF_SITE_URL.generateUrl('blog', 'post', array($post_data['post_seo_name'])); ?>" >
					<a class="addthis_button_facebook_like" fb:like:layout="button_count" fb:like:share="true"></a>
					<a class="addthis_button_tweet" tw:via=""></a>
					<a class="addthis_button_linkedin_counter"></a>
					<a class="addthis_button_pinterest_pinit"></a>
					<a class="addthis_counter addthis_pill_style"></a>
				</div>
		
			</div>
          </div>
          <?php	if($post_data['post_comment_status'] !=0) {	?>
          <h4 class="comt-title" id="comments"><?php echo Utilities::getLabel( 'L_Comments' ); ?></h4>
          <div id="comment-post-list"></div>
		  <div class="comment-form" id="comment-form">
            <h6><?php echo Utilities::getLabel( 'L_Post_Your_Comment' ); ?></h6>
			<?php 
				$str = $frmComment->getFormHtml();
				echo $frmComment->getFormTag();
				echo $frmComment->getFieldHTML('comment_post_id');
				echo $frmComment->getFieldHTML('comment_user_id');
			?>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <?php	if(empty($loggedUserId)){ ?>
				  <tr>
					<td><?php echo Utilities::getLabel( 'L_Name' ); ?><span class="mandatory">*</span></td>
					<td><?php echo $frmComment->getFieldHTML('comment_author_name');?></td>
				  </tr>
				  <tr>
					<td><?php echo Utilities::getLabel( 'L_Email' ); ?><span class="mandatory">*</span></td>
					<td><?php echo $frmComment->getFieldHTML('comment_author_email');?></td>
				  </tr>
				  <?php	}?>	
				  <tr>
					<td><?php echo Utilities::getLabel( 'L_Comment' ); ?><span class="mandatory">*</span></td>
					<td><?php echo $frmComment->getFieldHTML('comment_content');?></td>
				  </tr>
				  <tr>
					<td><?php echo Utilities::getLabel( 'L_Verification' ); ?><span class="mandatory">*</span></td>
					<td><table class="capcha">
						<tr>
						  <td><?php echo $frmComment->getFieldHTML('security_code');?></td>
						  <td style="position:relative;"><?php echo $frmComment->getFieldHTML('captcha');?></i></td>
						</tr>
					  </table></td>
				  </tr>
				  <tr>
					<td colspan="2"><?php echo $frmComment->getFieldHTML('btn_submit');?></td>
				  </tr>
				</table>
			</form><?php echo $frmComment->getExternalJS(); ?>
          </div>
		  <?php }?>
        </div>
		<?php
			}
			else{
				echo Utilities::getLabel( 'L_No_data_found' );
			}
		?>
        <!--left penal end here-->
        <?php include 'rightpanelblog.php'; ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557198d76d84bd2f" async="async"></script>