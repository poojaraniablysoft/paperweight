<div class="categories-wrap">
	<div class="searchForm">
		<?php echo $frmSearchForm->getFormTag();
			echo $frmSearchForm->getFieldHtml('search');
			echo $frmSearchForm->getFieldHtml('btn_submit');
			echo $frmSearchForm->getFieldHtml('page');
		?>    
		</form>
	</div>

	<div class="contribute"> 
		<a class="grydtn-btn" href="<?php echo generateUrl('blog','contribution');?>"><?php echo Utilities::getLabel( 'L_Contribute' ); ?></a>
	</div>
 
	<div class="category_widget">
		<h3><?php echo Utilities::getLabel( 'L_Recent_Posts' ); ?></h3>
		<?php if(isset($recent_post) && $recent_post != '') { ?>
			<ul>
				<?php
					foreach($recent_post as $records){
				?>
					<li> <a href="<?php echo generateUrl('blog', 'post', array($records['post_seo_name']));?>"><?php echo ucfirst(myTruncateCharacters($records['post_title'], 30));?></a>
					  <span><?php echo displayDate($records['post_published']);?> </span>
					  <span><a href="<?php echo generateUrl('blog', 'post', array($records['post_seo_name']));?>#comment-form"><?php echo $records['comment_count'];?> <?php echo Utilities::getLabel( 'L_Comments' ); ?></a></span>
					</li>
				<?php
					}
				?>    
			</ul>
		<?php }else { ?>
			<div class="no_result"><div class="no_result_icon"><img src="<?php echo CONF_WEBROOT_URL;?>images/small-logo.png"></div><div class="no_result_text"><h5><?php echo Utilities::getLabel( 'L_No_Blog_Post_found' ); ?></h5></p></div></div>
		<?php }?>
	</div>
	<?php if($archives || is_array($archives)){?>
	<div class="category_widget">
		<h3><?php echo Utilities::getLabel( 'L_Archives' ); ?></h3>
		<ul>
			<?php
				foreach($archives as $archives1) {
					$month = dateFormat("m", $archives1['created_month']);
					$year = dateFormat("Y", $archives1['created_month']);
					echo '<li><a href="'.generateUrl('blog', 'archives', array($year, $month)).'">'.$archives1['created_month'].'</a> </li>';
				}
			?>
		</ul>
	</div>   
	<?php
		}	
	if($categories || is_array($categories)){ ?>
	<div class="category_widget">
		<h3><?php echo Utilities::getLabel( 'L_Categories' ); ?></h3>
		<ul>
			<?php
				foreach($categories as $categories1) {
					if($categories1['category_id'] == 6 && $categories1['count_post'] == 0){/*if Uncategorized has 0 post then it will not show */
					}else{
						echo '<li><a href="'.generateUrl('blog', 'category', array($categories1['category_seo_name'])).'">'.ucfirst($categories1['category_title']).'('.$categories1['count_post'].') </a>';
						if(isset($categories1['children']))
						{
							echo '<ul>';
								foreach($categories1['children'] as $children)
								{
									echo '<li><a href="'. generateUrl('blog', 'category', array($children['category_seo_name'])).'">'.ucfirst($children['category_title']).' ('.$children['count_post'].')</a> </li>';
								}
							echo '</ul>';	
						}
						echo '</li>';
					}	
				}
			?>	
		</ul>			 
	</div> 
	<?php }?>
</div> 