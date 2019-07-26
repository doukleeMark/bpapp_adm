<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	if($_POST['actionType']=="reply_insert") {

		$sql = "select ur_position from user_data where idx={$_POST['bbr_user']} ";
		$userRes = $DB->GetOne($sql);

		$sql = "insert into bbs_reply(bbr_user, bbr_parent, bbr_order, bbr_content, bbr_dt_create) values(";
		$sql .= "'" . $_POST['bbr_user'] . "', ";
		$sql .= "'" . $_POST['bbr_parent'] . "', ";
		$sql .= "'" . $userRes['ur_position'] . "', ";
		$sql .= "'" . addslashes($_POST['bbr_content']) . "', ";
		$sql .= "sysdate())";
		$DB->Execute($sql);

		if($_POST['bbs_user'] != $_POST['bbr_user']){

			// 푸시
			exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php reply,{$_POST['bbr_parent']} > /dev/null &");
			
		}
		
		echo json_encode("1");

	}else if($_POST['actionType']=="reply_update"){

		$sql = "update bbs_reply set ";
		$sql .= "bbr_content = '" . addslashes($_POST['bbr_content_update']) . "', ";
		$sql .= "bbr_dt_create = sysdate() ";
		$sql .= "where idx={$_POST['reply_idx']} ";
		$DB->Execute($sql);

		echo json_encode("1");

	}else if($_POST['actionType']=="reply_delete"){
		
		$sql = "delete from bbs_reply where idx={$_POST['replyIdx']}";
		$DB->Execute($sql);
		echo json_encode("1");
	}
	
?>