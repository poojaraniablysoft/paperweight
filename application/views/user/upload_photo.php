<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<div id="body" >
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper ">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Upload_Profile_Image' ); ?></h2>
                <div class="formWrap">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
        </div>
    </div>
</div>