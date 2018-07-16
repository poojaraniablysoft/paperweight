<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="writer_wraper">
	<?php foreach ($arr_writers['data'] as $key=>$ele) { ?>				
		<div class="writer-list">
			<div class="imgbox"><a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><img src="<?php echo generateUrl('user', 'photo', array($ele['user_id'],100,100)); ?>" alt="<?php echo $ele['user_screen_name']; ?>"></a></div>
			<div class="right-penal">   <h2><a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><?php echo $ele['user_screen_name']; ?></a></h2>
				<div class="language"><?php 
					$html = $html_drop = '';
					
					foreach($ele['user_languages'] as $k=>$v) {
						if($k < 2){
							$html .= ($v['lang_name'] != '')?'<a class="btn no-cursor">'.$v['lang_name'].'</a>':'';
						}else {
							$html_drop .= '<li><a>'.$v['lang_name'].'</a></li>';
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
					<div class="ratingsWrap ratingSection"><span class="p<?php echo $ele['user_rating']*2; ?>"></span></div>
				<?php } else { ?>
					<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
			<div class="txt-box">
				<div class="txt-content">
					<p><?php echo (strlen($ele['user_more_cv_data']) > 300) ? substr($ele['user_more_cv_data'],0,299).'...':$ele['user_more_cv_data'];?>   </p>
				</div>
				<div class="grydtn-btn"><span><?php echo $ele['orders_in_progress']; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_In_Progress' ); ?></p></div>
				<div class="grydtn-btn"><span><?php echo $ele['orders_completed']; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_Completed' ); ?></p></div>
				<div class="clearfix"></div> 
			</div>
			<ul class="request-writer clearfix">
				<li><a class="initialism" onclick="chooseWriter(<?php echo $ele['user_id']; ?>)" href="javascript:void(0);"><?php echo Utilities::getLabel( 'L_Request_Writer' ); ?></a></li>
				<li><a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><?php echo Utilities::getLabel( 'L_View_Details' ); ?></a></li>
			</ul>
		</div>
	<?php }
		if(count($arr_writers['data']) < 1) {
		?> 
		<div class="grid_1">
			<div class="no_result">
				<div class="no_result_icon">
					<?php
						$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
					?>
					<img src="<?php echo $logoUrl;?>">
				</div>
				<div class="no_result_text">
				<h5><?php echo Utilities::getLabel( 'L_No_Writer_Found_Heading' ); ?></h5>
				<p><?php echo Utilities::getLabel( 'L_Try_to_search_with_other_filters' ); ?>. <a href="javascript:void(0);" onclick="getTopUsers(1)"><?php echo Utilities::getLabel( 'L_Back' ); ?></a> </p>
				</div>
			</div>
		</div>
		<?php
		}
	?>
	<div class="clearfix"></div>  
</div>
<div class="pagination">
	<?php echo generatePagingStringHtml($arr_writers['page'],$arr_writers['pages'],'search_writers(xxpagexx,\''.$filter.'\','.$filter_value.');');?>
</div>