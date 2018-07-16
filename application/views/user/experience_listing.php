<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo '<pre>' . print_r($arr_listing,true) . '</pre>';
?>

<div id="body">
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Manage_Experiences' ); ?></h2>
				<div class="gap"></div>
				<table width="100%" class="tablelisting">
					<tr>
						<th width="10%"><?php echo Utilities::getLabel( 'L_S_No' ); ?></th>
						<th width="*"><?php echo Utilities::getLabel( 'L_Company_Name' ); ?></th>
						<th width="24%"><?php echo Utilities::getLabel( 'L_Field_of_Work' ); ?></th>
						<th width="24%"><?php echo Utilities::getLabel( 'L_Position' ); ?></th>
						<th width="15%"><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
					</tr>
					<?php foreach ($arr_listing as $key=>$arr) { ?>
						<tr>
							<td><?php echo $key+1; ?></td>
							<td><?php echo $arr['uexp_company_name']; ?></td>
							<td><?php echo $arr['exfld_name']; ?></td>
							<td><?php echo $arr['uexp_position']; ?></td>
							<td>
								<a href="<?php echo generateUrl('user', 'manage_experience', array($arr['uexp_id'])); ?>"><?php echo Utilities::getLabel( 'L_Edit' ); ?></a>&nbsp;|
								<a href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Delete_This_Record_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('user', 'delete_experience', array($arr['uexp_id'])); ?>';}"><?php echo Utilities::getLabel( 'L_Delete' ); ?></a>
							</td>
						</tr>
					<?php } ?>
				</table>
				<div class="gap"></div>
				<a href="<?php echo generateUrl('user', 'manage_experience'); ?>" class="buttonRed clearfix fr"><?php echo Utilities::getLabel( 'L_Add_Experience' ); ?></a>                
            </div>
            <div class="rightPanel">
				<?php if (User::isUserLogged()) { ?>
					<div class="sideWrap">
						<ul>
							<li><a href="<?php echo generateUrl('user', 'update_profile'); ?>"><?php echo Utilities::getLabel( 'L_Profile_Settings' ); ?></a></li>
							<li><?php echo Utilities::getLabel( 'L_Manage_Experiences' ); ?></li>
						</ul>
					</div>
				<?php } ?>
            </div>
        </div>
    </div>
</div>