<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
	if($records || is_array($records)){
		echo createHiddenFormFromPost('frmPaging', '', array(), array());
		
?>
		<?php
			//echo "<pre>".print_r($records,true)."</pre>";exit;
			foreach($records as $records1){
		?>
		<div class="comment-list">
			<?php
				if(!empty($records1['user_id'])){
					echo '<figure class="author_image"><img src="'.generateUrl('user', 'photo', array($records1['user_id'], 100,100)).'" alt="img"> </figure>';
				}else{
					echo '<figure class="author_image"><img src="'.generateUrl('image', 'user', array('thumb', 'comment_no_image.png')).'" alt="img"> </figure>';
				}
			?>
			<div class="cmt-content">
              <h5><?php echo ucfirst($records1['comment_author_name']);?></h5>
              <p><?php echo nl2br($records1['comment_content']);?></p>
			  <p><?php echo displayDate($records1['comment_date_time']);?></p>
            </div>
		</div>
		<?php	} ?>
			
		<?php if ($pages > 1){ ?>
			<div class="pagination"><ul>
				<?php
					echo getPageString('<li><a href="javascript:void(0)" onclick="listPages(xxpagexx);">xxpagexx</a></li>', $pages, $page,'<li><a class="pageselect" href="javascript:void(0)">xxpagexx</a></li>', '<li>...</li>');
				?>
			</ul></div>
		<?php
			}
	}
?>