<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>
<div id="body">
  <div class="row whiteBg strip">
    <div class="fix-container">
     <div class="blog-wrap">
		<!--left penal start here-->
		<div class="blog-left">
		<?php
			$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
			
			if(!$records || !is_array($records)){ 
					echo '<div class="no_result"><div class="no_result_icon"><img src="'.$logoUrl.'"></div><div class="no_result_text"><h5>' . Utilities::getLabel( 'L_No_Blog_Post_found_with_search_criteria' ) . '</h5><p>' . Utilities::getLabel( 'L_Try_to_search_with_other_Keyword' ) . ' <a href="'.generateUrl('blog', '', array()).'">Back To Blog</a> </p></div></div>'; 
			}else{
				echo createHiddenFormFromPost('frmPaging', '', array(), array());
				foreach($records as $records1){
			?>
				<div class="post">
					<div class="post-title clearfix">
						 <div class="date">	<?php echo dateFormat("d",$records1['post_published']);?><span><?php echo dateFormat("M",$records1['post_published']);?></span> </div>
						 <h3><?php echo ucfirst(myTruncateCharacters($records1['post_title'], 50));?></h3>
						 <ul class="cmt-box">
							<li> <i class="icon ion-eye"></i><?php echo Utilities::getLabel( 'L_Views' ); ?><span><?php echo $records1['post_view_count'];?></span></li>
							<li><i class="icon ion-chatbubble-working"></i><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>#comment-form"><?php echo Utilities::getLabel( 'L_Comments' ); ?></a> <span><?php echo $records1['comment_count'];?></span></li>				 
						</ul>     
					</div>
					<?php if(!empty($records1['post_image_file_name'])){ ?>
							<figure class="post-image"><img src="<?php echo generateUrl('image', 'post', array('large', $records1['post_image_file_name'])); ?>" alt="img"></figure>
					<?php }	?>
					<div class="post_content">
						<?php 
							if(!empty($records1['post_short_description'])){
								echo $records1['post_short_description'];
							}
							else{
								echo myTruncateCharacters($records1['post_content'], 200);
							}
						?>  <a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>" class="readmore">[<?php echo Utilities::getLabel( 'L_read_more' ); ?>]</a>
					</div>
					<div class="sharewrap">
						<div class="fb-like" data-href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
					</div>     
				</div> 
			<?php
				}
			}
			?>
		</div>     
		<!--left penal end here-->
     
	<?php 	if ($pages > 1){?>
		<div class="pagination">
			<ul>
			<?php
				echo getPageString('<li><a href="javascript:void(0)" onclick="listPages(xxpagexx);">xxpagexx</a></li>', $pages, $page,'<li><a class="pageselect" href="javascript:void(0)">xxpagexx</a></li>', '<li>...</li>');
			?>
			</ul>
		</div>	<?php } ?>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5026620141dbd841"></script>
		<?php include 'rightpanelblog.php'; ?>
	  </div>
    </div>
  </div>
</div>