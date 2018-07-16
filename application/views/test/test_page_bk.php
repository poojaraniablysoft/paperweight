<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

?>

<script type="text/javascript">
	var remaining_time = <?php echo $remaining_time; ?> * 1000;
	var quenum = <?php if(Test::lastUpdatedId()) {echo Test::lastUpdatedId();} else{echo '1';}?>;
	var totalque = <?php echo CONF_QUES_DISPLAY;?>;
</script>

<!-- body -->
<div id="body">

	<div class="sectionInner clearfix">

		<div class="fix-container">
			<?php echo Message::getHtml(); ?>
				<div class="sectionTitle">
					<div class="boxinfo"><p>This is a reduced functionality mode. You will get access to all service features once your application is approved</p></div>
					<h4>Register as a freelancer writer: Step 3 of 4</h4>
				</div>
				<ul class="stepListing">
					<li class="finishstep">
						<a><span class="counts">1</span><span class="textvalue">Submit Email</span><span class="spanarrow"></span></a>
					</li>
					<li class="finishstep">
						<a><span class="counts">2</span><span class="textvalue">Fill in Profile</span></a><span class="spanarrow"></span>
					</li>
					<li class="stepselect">
						<a><span class="counts">3</span><span class="textvalue">Pass Grammar Test</span><span class="spanarrow"></span></a>
					</li>
					<li>
						<a><span class="counts">4</span><span class="textvalue">Write Sample Essay</span><span class="spanarrow"></span></a>
					</li>
				</ul>
				
				<div class="borderArea" id="status">
					<?php if($status == 'Fail'){?>
						<div class="centerSection" id="status">
							<h3>Dear Applicant,</h3>
							<p>Unfortunately, the number of mistakes has exceeded the allowed maximum, which means your have failed the test. Your registration is now closed.</p>
						</div>	
					<?php } else {?>	
						<?php $i = 1;?>
						<div class="sectionTime">
							<div class="grid_1" >Question <span id="num"><?php echo $i;?></span> of  <?php echo CONF_QUES_DISPLAY;?></div>
							<div class="grid_2"><div class="timeLeft">Time Left: <span id='timer'></span></div></div>
						</div>
						
						<div id="loading" align="center"><img src="<?php echo CONF_WEBROOT_URL.'images/loadinfo.gif'; ?>" /></div>
						<div class="qWrap" id="queshow">
							
						</div>

					<?php }?>
				   
				</div>

		</div>

	</div>

</div>