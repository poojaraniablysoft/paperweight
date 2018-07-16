<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
	$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
	
	if(!$records || !is_array($records)){ echo '<div class="no_result"><div class="no_result_icon"><img src="'.$logoUrl.'"></div><div class="no_result_text"><h5>' . Utilities::getLabel( 'L_No_Blog_Post_found' ) . '</h5></p></div></div>'; }else{
		echo createHiddenFormFromPost('frmPaging', '', array(), array());
	foreach($records as $records1){
?>
	<div class="post">
		<div class="post-title clearfix">
			 <div class="date">	<?php echo dateFormat("d",$records1['post_published']);?><span><?php echo dateFormat("M",$records1['post_published']);?></span> </div>
			 <h3><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>"><?php echo ucfirst(myTruncateCharacters($records1['post_title'], 50));?></a></h3>
			 <ul class="cmt-box">
				<li> <i class="icon ion-eye"></i><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>"><?php echo Utilities::getLabel( 'L_Views' ); ?></a><span><?php echo $records1['post_view_count'];?></span></li>
				<li><i class="icon ion-chatbubble-working"></i><a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>#comments"><?php echo Utilities::getLabel( 'L_Comments' ); ?></a> <span><?php echo $records1['comment_count'];?></span></li>	
				<?php if(isset($records1['post_categories']) && is_array($records1['post_categories']) && count($records1['post_categories'])){ ?>
					<li> <i class="icon ion-cube"></i>
						<?php 
							$post_category_counter = 1;
							foreach($records1['post_categories'] as $cat){
								echo '<a href="'.generateUrl('blog', 'category', array($cat['category_seo_name']) ).'">'.$cat['category_title'].'</a>';
								if($post_category_counter != count($records1['post_categories'])){
									echo ', ';
								}
								$post_category_counter++;
							} ?>
					</li>
				<?php } if(!empty($records1['post_contibutor_name']) && $records1['post_contibutor_name']!=''){ ?>	
				<li><i class="icon ion-person"></i><?php echo Utilities::getLabel( 'L_Author' ); ?><span><?php echo $records1['post_contibutor_name'];?></span></li>	
				<?php }	?>
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
					$post_content=html_entity_decode($records1['post_content']);										echo myTruncateCharacters($post_content, 200);
				}
			?>  <a href="<?php echo generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>" class="readmore">[<?php echo Utilities::getLabel( 'L_read_more' ); ?>]</a>
		</div>
		<div class="sharewrap">
			
			<div class="addthis_toolbox addthis_default_style" addthis:title="<?php echo $records1['post_title']; ?>" addthis:url="<?php echo CONF_SITE_URL.generateUrl('blog', 'post', array($records1['post_seo_name'])); ?>" >
				<a class="addthis_button_facebook_like" fb:like:layout="button_count" fb:like:share="true"></a>
				<a class="addthis_button_tweet" tw:via=""></a>
				<a class="addthis_button_linkedin_counter"></a>
				<a class="addthis_button_pinterest_pinit"></a>
				<a class="addthis_counter addthis_pill_style"></a>
			</div>
		
		</div>  
	 </div> 
<?php
	}
/* if ($pages > 1){ ?>
	<ul class="pagination">
		<?php
			echo getPageString('<li><a href="javascript:void(0)" onclick="listPages(xxpagexx);">xxpagexx</a></li>', $pages, $page,'<li><a class="pageselect" href="javascript:void(0)">xxpagexx</a></li>', '<li>...</li>');
		?>
	</ul>
<?php
	} */
	
}
if ($pages > 1){
?>
<div class="pagination">
<ul>
<?php
	echo getPageString('<li><a href="javascript:void(0)" onclick="listPages(xxpagexx);">xxpagexx</a></li>', $pages, $page,'<li><a class="pageselect" href="javascript:void(0)">xxpagexx</a></li>', '<li>...</li>');
?>
</ul></div>
<?php } ?>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5026620141dbd841" async="async"></script>