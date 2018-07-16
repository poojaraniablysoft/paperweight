<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('configurations'); ?>"<?php if( strtolower( $selectedNav ) == 'configurations' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_GENERAL_SETTINGS' ); ?></a></li>							
		<li><a href="<?php echo generateUrl('labels'); ?>"<?php if( strtolower( $selectedNav ) == 'labels' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_LABELS_MANAGEMENT' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('countries'); ?>"<?php if( strtolower( $selectedNav ) == 'countries' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_COUNTRIES' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('work'); ?>"<?php if( strtolower( $selectedNav ) == 'work' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_WORK_FIELD' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('services'); ?>"<?php if( strtolower( $selectedNav ) == 'services' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_SERVICES_FIELD' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('disciplines'); ?>"<?php if( strtolower( $selectedNav ) == 'disciplines' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_DISCIPLINES' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('paper'); ?>"<?php if( strtolower( $selectedNav ) == 'paper' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_PAPER_TYPE' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('academic'); ?>"<?php if( strtolower( $selectedNav ) == 'academic' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_ACADEMIC_DEGREES' ); ?></a></li>
		
		<li><a href="<?php echo generateUrl('citation'); ?>"<?php if( strtolower( $selectedNav ) == 'citation' ) echo ' class="selected"'; ?>><?php echo Utilities::getLabel( 'L_MANAGE_CITATION_STYLE' ); ?></a></li>
		
	</ul>
</section>
<!--leftPanel end here--> 