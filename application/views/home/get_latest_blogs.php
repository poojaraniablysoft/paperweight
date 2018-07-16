<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
	if(!$records || !is_array($records)){ echo 'No Record Found!!'; }else{
?>
<div class="row darkgry paddng">
    <div class="fix-container">
      <div class="recent-blog">
        <h2 class="page-title"><?php echo Utilities::getLabel( 'L_Recent_Blog_Posts' ); ?></h2>
        <p><?php echo Utilities::getLabel( 'L_Recent_Blog_Posts_Desc' ); ?></p>
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
						echo myTruncateCharacters($records1['post_short_description'], 100);
					}
					else{
						echo myTruncateCharacters($records1['post_content'], 100);
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
<?php } ?>