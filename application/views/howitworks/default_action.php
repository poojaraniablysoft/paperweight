<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row  greyBg paddng strip">
		<div class="fix-container">
			<h2 class="page-title"><?php echo Utilities::getLabel( 'L_How_It_Works' ); ?></h2>
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
									<?php 
										if($fetchedData){
											foreach($fetchedData as $in => $data){
									?>
										<div class="our-works">
											<div class="grid-1">
												<h3><span class="step"><?php echo ++$in; ?></span><?php echo $data['step_title']; ?></h3>
												<p><?php echo html_entity_decode(nl2br($data['step_description'])); ?></p>
											</div>
											<?php if($data['step_image']){ ?>
												<div class="grid-2">
													<div class="image">
														<img alt="<?php echo $data['step_title']; ?>" src="<?php echo generateUrl('image', 'step', array( 'thumb', $data['step_image'] )); ?>" /> 
														<div class="zoomin">
															<a data-fancybox-group="gallery<?php echo $index; ?>" rel="group" href="<?php echo generateUrl('image', 'step', array( 'large', $data['step_image'] )); ?>" class="fancybox" ><img alt="<?php echo $data['step_title']; ?>" src="../../../images/zoom-in.png" />
															</a>
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									<?php 
											}
										}else{
											echo '<span class="center notfound-text">' . Utilities::getLabel( 'L_No_step_found' ) . '</span>';
										}
									?>
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