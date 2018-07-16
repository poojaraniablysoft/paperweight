<?php echo Message::getHtml();?>
<?php foreach ($arr_reviews as $ele) { ?>
	<?php echo '<div class="popup_msg">'.$ele['rev_comments'].'</div>'; ?>
<?php } ?>
<?php if (count($arr_reviews) == 0) echo '<div class="popup_msg information">No Reviewed added yet.</div>'; ?>