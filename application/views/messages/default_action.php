<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

//echo '<pre>' . print_r($arr_last_conversation,true) . '</pre>';
//echo '<pre>' . print_r($arr_users,true) . '</pre>';
?>

<script type="text/javascript">
	var bid_id = <?php echo $bid_id ?>;
	var logoUrl = "<?php echo generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',)); ?>";
</script>

<h1 class="mob-show"><?php echo Utilities::getLabel( 'L_Inbox' ); ?></h1>

<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
	<div class="order_page">
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">Click here to submit essay!</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>
<div class="writer-inbox" id="writer-inbox">

  <ul class="roundtabs clearfix">
	  <li class="currenttab" id="all"><a href="javascript:all_msg('all',1);"><?php echo Utilities::getLabel( 'L_All' ); ?></a></li>
	  <li id="read"><a href="javascript:all_msg('read',1);"><?php echo Utilities::getLabel( 'L_Read' ); ?></a></li>
	  <li id="Unread"><a href="javascript:all_msg('unread',1);"><?php echo Utilities::getLabel( 'L_Unread' ); ?></a></li>
	  <li id="starred"><a href="javascript:all_msg('starred',1);"><?php echo Utilities::getLabel( 'L_Starred' ); ?></a></li>
  </ul>
<div id="history">
  <div class="white-box clearfix">
	<div class="msgcontainer" >
	
		<?php foreach ($arr_users['messages'] as $ele) {   ?> 
			<div  class="border-box <?php if($ele['unread_messages'] >= 1) { echo  Utilities::getLabel( 'L_unread' ); } else { echo Utilities::getLabel( 'L_read' ); };?>">
				
				<ul class="choiceTabs">
				  <li><a href="javascript:void(0);" onclick="markStarred($(this)); return false;" data-conversation-id="<?php echo $ele['bid_id']; ?>"><i class="<?php echo ($ele['bid_is_starred']==1 ) ? 'starred':'';?> ion-star"></i></a></li>
				  <li><a href="#"><i class="icon ion-record"></i></a></li>
				</ul>
				<div class="cursor" onclick="return getConversationHistory($(this));" data-bid-id="<?php echo $ele['bid_id']; ?>">
					<div class="user_msg"><?php echo strlen($ele['user_screen_name']) > 14 ? substr($ele['user_screen_name'],0,14) . '...' : $ele['user_screen_name']; ?></div>
					<div class="msg_text">
						<h5><?php echo '#' . $ele['task_ref_id']; ?></h5>
						<p><span><?php echo Utilities::getLabel( 'L_Updated' ); ?>:</span> <?php echo datesToInterval($ele['last_updated_on']); ?> </p>
					</div>
					<div class="last-msg">
						<?php echo $ele['tskmsg_msg'];?>
					</div>
				</div>
			</div>
		<?php }		?>  
		
		
		<?php if(count($arr_users['messages']) == 0){ ?>
			<div class="no_result">
				<div class="no_result_icon">
					<?php
						$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));					
					?>
					<img src="<?php echo $logoUrl;?>">
				</div>
				<div class="no_result_text">
					<h5><?php echo Utilities::getLabel( 'L_No_Message_found' ); ?></h5>
				</div>
			</div>
		<?php }?>
	</div>
	
  </div>
  
</div>
<div class="pagination" ><?php echo generatePagingStringHtml($arr_users['page'],$arr_users['pages'],"all_msg('all',xxpagexx);"); ?></div>
</div>


<div id="msg_section" style="display:none;">
	<div class="sectionMsgs user-list">
      <div class="white-box clearfix">
        <div  class="conversation_history">
          <div class="topCol blue-bg"> <a href="<?php echo generateUrl('messages');?>" class="white-btn"><i class="icon ion-chevron-left"></i><?php echo Utilities::getLabel( 'L_Back' ); ?></a>
			 
          </div>
          <div class="scrollWrap" id="conversation_history">
            <h2 class="centerTitle"><span class="aOM">Monday, August 11, 2014</span> <span class="aZc">12:09 PM</span><span><a href="#" class="chatStarred live"><i class="fa fa-star"></i></a></span> </h2>
            <?php if (!empty($arr_last_conversation['bid']) || !empty($arr_last_conversation['conversation'])) { ?>
				<?php foreach ($arr_last_conversation['conversation'] as $ele) { ?>
					<div class="userList">
						<div class="photo"><img src="<?php echo generateUrl('user', 'photo', array($ele['tskmsg_user_id'],50,50)); ?>" alt=""></div>
						<span class="timetxt"><?php echo datesToInterval($ele['tskmsg_date']); ?></span>
						<h5><a><?php echo $ele['sender']; ?></a></h5>
						<p><?php echo $ele['tskmsg_msg']; ?></p>
					</div>	
				<?php } ?>
			<?php } else { ?>
				<span><?php echo Utilities::getLabel( 'L_No_conversations_to_show_here' ); ?></span>
			<?php } ?>
            
          </div>
        </div>
      </div>
      <div class="white-box clearfix padding_30">
	  <?php if (!empty($arr_last_conversation['bid'])) { ?>
		<div class="postSection">
			<form name="frmMessage" id="frmMessage">
				<input type="hidden" name="bid_id" id="bid_id" value="<?php echo $arr_last_conversation['bid']['bid_id']; ?>">
				<textarea name="message" id="message" rows="" cols="" placeholder="<?php echo Utilities::getLabel( 'L_Enter_your_message_here' ); ?>"></textarea>
				<input type="button" value="Send" name="btn_send" id="btn_send" onclick="return submitFrm();" class="theme-btn">
			</form>
		</div>
	  <?php } ?>
      </div>
    </div>
</div>