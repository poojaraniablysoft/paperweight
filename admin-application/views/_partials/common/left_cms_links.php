<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('contentpages');?>"<?php if(isset($leftLink)){if($leftLink == 'home') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_Manage_Content_Pages' ); ?></a></li>
		<li><a href="<?php echo generateUrl('cmsblock');?>"<?php if(isset($leftLink)){if($leftLink == 'cmsblock') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_Manage_Page_Blocks' ); ?></a></li>
		<li><a href="<?php echo generateUrl('emailtemplate');?>"<?php if(isset($leftLink)){if($leftLink == 'emailtemplate') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_EMAIL_TEMPLATE' ); ?></a></li>
		<li><a href="<?php echo generateUrl('contentpages','service_tag');?>"<?php if(isset($leftLink)){if($leftLink == 'service_tag') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_Manage_Infotips' ); ?></a></li>
		<li><a href="<?php echo generateUrl('faq');?>"<?php if(isset($leftLink)){if($leftLink == 'faq') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_Manage_FAQ\'s' ); ?></a></li>
		<li><a href="<?php echo generateUrl('howitworks');?>"<?php if(isset($leftLink)){if($leftLink == 'howitworks') echo ' class="selected"'; } ?>><?php echo Utilities::getLabel( 'L_Manage_How_It_Works_Steps' ); ?></a></li>
	</ul>
</section>
<!--leftPanel end here--> 