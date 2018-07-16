<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
	</div>
		<!--body end here-->
	 
	</div>
	<!--wrapper end here-->
	  
		<!--footer start here-->
		<footer id="footer">
				 <p class="left">Note: All the times are according to server time. Current server time is <?php echo addTimezone(date("l M d, Y, H:i"), CONF_TIMEZONE);?></p>
				 <p class="right"><a href="<?php echo generateUrl('home');?>"><?php echo CONF_WEBSITE_NAME;?></a></p>
		</footer>
		<!--footer end here-->

	<script src="<?php echo CONF_WEBROOT_URL;?>js/cbpHorizontalMenu.js"></script>
	<script>
		cbpHorizontalMenu.init();
	</script>
</body>
</html>