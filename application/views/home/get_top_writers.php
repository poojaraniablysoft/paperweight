<div class="row whiteBg paddng strip">
    <div class="fix-container">
      <div class="our-wrtiter">
        <h2 class="page-title"><?php echo Utilities::getLabel( 'L_Our_writers' ); ?></h2>
        <p><?php echo Utilities::getLabel( 'L_Our_writers_Description' ); ?></p>
        <ul class="wrtiter-list">
         <?php foreach ($arr_writers['data'] as $key=>$ele) { ?> 
		  <li>
            <div class="profile">
              <div class="img-sectn">  <img src="<?php echo generateUrl('user', 'photo', array($ele['user_id'],100,100)); ?>"> </div>
              <div class="txt-detail">
                <h2><?php echo $ele['user_screen_name']; ?></h2> 
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
            
            <div class="botm-sectn">              
              <a href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>"><?php echo Utilities::getLabel( 'L_View_Details' ); ?></a>
            </div>
          </li>
		  
          <?php } ?>
        </ul>
        <div class="wrap-wrtiter-list"> <a href="<?php echo generateUrl('user','top_writers');?>" class="theme-btn"><?php echo Utilities::getLabel( 'L_View_All' ); ?></a> </div>
      </div>
    </div>
</div>