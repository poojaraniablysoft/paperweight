<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Extend_Deadline' ); ?></h2>
				<div class="gap"></div>
				<div id="error"></div>
                <div class="formWrap">
					<?php 
						$task_due_date = date('M d,Y H',strtotime($arr_task['task_due_date']));
						$arr_task['task_due_date'] = date('M d,Y',strtotime($arr_task['task_due_date']));
						$task_time = explode($arr_task['task_due_date'],$task_due_date);
						$arr_task['task_time']=$task_time['1'];
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
<script type="text/javascript">
/* 	
$(document).ready(function(){
	var currentDate = new Date();
	var day = currentDate.getDate();
      var month = currentDate.getMonth() + 1;
      var year = currentDate.getFullYear();
 var my_date = month+"-"+day+"-"+year;
	$("#task_due_date").datetimepicker({
		format: 'M d, Y H:i',
		minDate: my_date
	});
}); */
</script>