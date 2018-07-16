<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row  greyBg paddng strip">
		<div class="fix-container">
			<h2 class="page-title"><?php echo Utilities::getLabel( 'L_FAQ' ); ?></h2>
			<div class="contact-detali"></div>
			<div class="how-it-work">
				<div class="review-tab" id="horizontalTab">
					<div class="border-bottom">
						<ul class="resp-tabs-list">
							<?php 
							foreach(Applicationconstants::$arr_cats as $index => $title){
								$imgUrl = ($index == 1)?'../../../images/writer-icon.png':'../../../images/customer-icon.png';
							?>
								<li><a href="#tabs<?php echo $index; ?>" class="toggle-button"><span class="image"><img alt="img" src="<?php echo $imgUrl; ?>" /></span><?php echo $title; ?></a></li>
							<?php } ?>
						</ul>
					</div>
					<div class="resp-tabs-container grid_13">
						<?php 
							if($data_arr){
								foreach($data_arr as $index => $fetchedData){
								?>
								<div class="rivews-box tab_content" id="tabs<?php echo $index; ?>">
									<div class="faq">
										<div class="accordion">
											<h3><?php echo Utilities::getLabel( 'L_General_Questions' ); ?></h3>
											<?php 
												if($fetchedData){
													foreach($fetchedData as $data){
											?>
												<h4><?php echo $data['faq_title']; ?></h4>
												<p><?php echo html_entity_decode(nl2br($data['faq_description'])); ?></p>
											<?php 
													}
												}else{
													echo '<span class="center notfound-text">No faq found.</span>';
												}
											?>
										</div>
									</div>
								</div>
								<?php 
								}
							}
						?>
						</div>
				</div>
			</div>
		</div>	
	</div>
</div>