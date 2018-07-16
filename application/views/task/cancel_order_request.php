<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Send_Request_to_Cancel_Order' ); ?></h2>
				<div class="gap"></div>
				<div id="error"></div>
                <div class="formWrap">
					<?php 
						$frm->fill($arr_task);
					?>
                    <?php echo $frm->getFormHtml(); ?>
					
                </div>  
            </div>
            <div class="rightPanel">
                <div class="sideWrap"></div>
            </div>               
        </div>
    </div>
</div>
