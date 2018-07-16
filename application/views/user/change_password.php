<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Change_Password' ); ?></h2>
                <div class="formWrap">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
            <div class="rightPanel">
                <div class="sideWrap">
					<ul>
						<li><a href="<?php echo generateUrl('user', 'update_profile'); ?>"><?php echo Utilities::getLabel( 'L_Profile_Settings' ); ?></a></li>
						<li><?php echo Utilities::getLabel( 'L_Change_Password' ); ?></li>
					</ul>
				</div>
            </div>
        </div>
    </div>
</div>