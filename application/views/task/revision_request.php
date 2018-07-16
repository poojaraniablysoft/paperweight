<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<!--- popup wrapper Upload Additional Material-->
<div id="body" >
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper ">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Send_Request_to_Revise_Milestone_files' ); ?></h2>
                <div class="formWrap">
                   <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--- popup wrapper end here--> 

