<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Register_as_a_writer' ); ?></h2>
				<div class="gap"></div>
				<h4><?php echo Utilities::getLabel( 'L_Step_2' ); ?>: <?php echo Utilities::getLabel( 'L_Fill_in_your_profile' ); ?></h4>
                <div class="formWrap">
                    <?php echo $frm->getFormHtml(); ?>
                </div>  
            </div>
            <div class="rightPanel">
                <div class="sideWrap"></div>
            </div>               
        </div>
    </div>
</div>