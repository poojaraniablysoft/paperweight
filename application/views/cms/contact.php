<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
//echo "<pre>".print_r($content,true)."</pre>";
?>
<div id="body">
  <div class="row whiteBg paddng strip">
    <div class="fix-container">
      <h2 class="page-title"><?php echo $content['cmspage_title'];?></h2>
      <div class="contact-detali">
		<?php echo html_entity_decode($content['cmspage_content']);?>
	  </div>
      
      <div class="location">
		<div class="siteForm">
		<?php
			echo Message::getHtml(); 
			echo $frm->getFormHtml();
		?> 
		</div>
		<div  class="contact-address">
		  <h3><?php echo Utilities::getLabel( 'L_Contact_Address' ); ?></h3>
		  <div class="address">
			<p><i class="icon ion-ios-location-outline"></i> <?php echo CONF_CONTACT_ADDRESS;?></p>			  
			<p><i class="icon ion-iphone"></i> <?php echo CONF_CONTACT_PHONE;?></p>			  
			<p><i class="icon ion-ios-email-outline"></i> <a href="mailto:<?php echo CONF_CONTACT_EMAIL_TO;?>"><?php echo CONF_CONTACT_EMAIL_TO;?></a></p>		  
		  </div>      
		  <div class="map">
			<?php echo CONF_CONTACT_LOCATION;?>
		  </div>      
		</div>
      </div>
      
    </div>
  </div>
</div>
