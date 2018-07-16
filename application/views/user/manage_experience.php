<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel">
            	<h2 class="pagetitle"><?php echo $exp_id>0?Utilities::getLabel( 'L_Edit_Work_Experience' ):Utilities::getLabel( 'L_Add_Work_Experience' ); ?></h2>
                <div class="formWrap">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
            <div class="rightPanel"></div>
        </div>
    </div>
</div>