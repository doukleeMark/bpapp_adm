<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	if($_POST['actionType']=="reply_insert") {

		// POST : actionType, bpr_parent, bp_user, bpr_user, bpr_content

		$sql = "select idx, ur_point_bp, ur_position from user_data where idx={$_POST['bpr_user']} ";
		$userRes = $DB->GetOne($sql);

		$sql = "insert into bp_reply(bpr_user, bpr_parent, bpr_order, bpr_content, bpr_dt_create) values(";
		$sql .= "'" . $_POST['bpr_user'] . "', ";
		$sql .= "'" . $_POST['bpr_parent'] . "', ";
		$sql .= "'" . $userRes['ur_position'] . "', ";
		$sql .= "'" . addslashes($_POST['bpr_content']) . "', ";
		$sql .= "sysdate())";
		$DB->Execute($sql);

		if($_POST['bp_user'] != $_POST['bpr_user']){

			// 푸시
			exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php bp_reply,{$_POST['bpr_parent']} > /dev/null &");
			
		}
		
		echo json_encode("1");

	}else if($_POST['actionType']=="reply_update"){

		$sql = "update bp_reply set ";
		$sql .= "bpr_content = '" . addslashes($_POST['bpr_content_update']) . "', ";
		$sql .= "bpr_dt_create = sysdate() ";
		$sql .= "where idx={$_POST['reply_idx']} ";
		$DB->Execute($sql);

		echo json_encode("1");

	}else if($_POST['actionType']=="reply_delete"){
		
		$sql = "delete from bp_reply where idx={$_POST['replyIdx']}";
		$DB->Execute($sql);
		echo json_encode("1");
	}
	
?>