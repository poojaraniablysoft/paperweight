<?php echo $frm->getFormtag(); ?>
	<table class="orderForm-tbl" cellpadding="0" cellspacing="0">
		<thead>
		  <tr>
			<td colspan="3"><h2><?php echo Utilities::getLabel( 'L_Place_New_Order' ); ?></h2></td>
			<td colspan="2"><span><?php echo Utilities::getLabel( 'H_Free_Fast_&_Safe' ); ?></span></td>
		  </tr>
		</thead>
		<tbody>
		  <tr>
			<?php if(!User::isUserLogged() || (User::isUserLogged() && User::isWriter())) {?>
			<td><?php echo $frm->getFieldHTML('user_email'); ?></td>
			<?php }else { ?>
			<td><?php echo $frm->getFieldHTML('task_topic'); ?></td>
			<?php }?>
			<td><?php echo $frm->getFieldHTML('task_due_date');?></td>
			<td><?php echo $frm->getFieldHTML('task_paptype_id');?></td>
			<td><?php echo $frm->getFieldHTML('task_pages');?>
				<span class="task_pages"><img class="error-icn" src="images/error-icn.png"/><div class="tootltip"><p><?php echo sprintf( Utilities::getLabel( 'L_words_pages' ), CONF_WORDS_PER_PAGE ); ?></p></div></span></td>
			<td><?php echo $frm->getFieldHTML('btn_submit');?></td>
		  </tr>
		</tbody>
	</table>
</form>
<?php echo $frm->getExternalJS(); ?>
