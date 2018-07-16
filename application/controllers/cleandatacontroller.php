<?php
class CleandataController extends LegitController{
	
	public function default_action(){
		
		$tablesToTruncate = array("tbl_bids","tbl_blog_contributions","tbl_blog_meta_data","tbl_blog_post","tbl_blog_post_categories","tbl_blog_post_category_relation","tbl_blog_post_comments","tbl_blog_post_images","tbl_milestones","tbl_money_earned","tbl_ratings","tbl_reserve_money","tbl_reviews","tbl_sample_essay","tbl_tasks","tbl_task_invitees","tbl_task_updates","tbl_test_progress","tbl_transactions","tbl_users","tbl_user_auth_tokens","tbl_user_citation_styles","tbl_user_education","tbl_user_email_verification_codes","tbl_user_experience","tbl_user_languages","tbl_user_password_reset_requests","tbl_user_profile","tbl_user_subscriptions","tbl_user_test","tbl_wallet_transactions","tbl_withdrawal_requests");
		
		$successMsg = "";
		$errorMsg = "";
		
		$db = Syspage::getdb();
		foreach( $tablesToTruncate as $table ){
			if( $db->query("truncate  $table") ){
				$successMsg .= "<p>Cleaned table: ".$table."</p>";
			}else{
				$errorMsg .= "<p>Failed to clean table: ".$table."<br>Error :".$db->getError()."</p>";
			}
		}
		
		echo $successMsg;
		
		if( $errorMsg != ''){
			echo $errorMsg;
		}
		
	}
}