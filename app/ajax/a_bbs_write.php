<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$dataClass = new DataClass();
	$uploadClass = new UploadClass();
		
	if($_POST['actionType']=="bbs_write" || $_POST['actionType']=="bbs_edit") {
		$mb1 = 1048576; //1Byte-1MB
		if($_POST['bbs_file'] > 0)$uploadno = $_POST['bbs_file'];
		else $uploadno = 0;

		if(isset($_FILES['bbsFile'])){
			$finfo1 = $uploadClass->getfileinfo($_FILES['bbsFile']);
						
			if ($finfo1->size > ($mb1 * 50) || $finfo1->size == 0) {				
				echo "error,업로드 가능한 파일 용량이 아닙니다.";
				return;
			}			
		
			$uploadno = $uploadClass->bbsUpload($_FILES['bbsFile']);
		};

	}
	if($_POST['actionType']=="bbs_write") {

		$sql = "insert into bbs_data(bbs_user, bbs_unit, bbs_mode, bbs_title, bbs_content, bbs_file, bbs_dt_create) values(";
		$sql .= "'" . $_POST['bbs_user'] . "', ";
		$sql .= "'" . $_POST['bbs_unit'] . "', ";
		$sql .= "'" . $_POST['bbs_mode'] . "', ";
		$sql .= "'" . addslashes($_POST['bbs_title']) . "', ";
		$sql .= "'" . addslashes($_POST['bbs_content']) . "', ";
		$sql .= "'" . $uploadno . "', ";
		$sql .= "sysdate())";
		$DB->Execute($sql);
		$new_idx = $DB->Insert_ID();

		if($_POST['bbs_mode']==4) // 요청
			exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php bbs1,{$new_idx} > /dev/null &");
		else if($_POST['bbs_mode']==5) // 질문
			exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php bbs2,{$new_idx} > /dev/null &");

		echo json_encode("1");

	}else if($_POST['actionType']=="bbs_edit") {

		$sql = "update bbs_data set ";
		$sql .= "bbs_mode ='" . $_POST['bbs_mode'] . "', ";
		$sql .= "bbs_title='" . addslashes($_POST['bbs_title']) . "', ";
		$sql .= "bbs_content='" . addslashes($_POST['bbs_content']) . "', ";
		$sql .= "bbs_file='" . $uploadno . "' ";
		$sql .= "where idx=" . $_POST['bbsIdx'];
		$DB->Execute($sql);
		
		echo json_encode("1");

	}else if($_POST['actionType']=="bbs_deleteFile"){
		
		$dataClass->deleteFile($_POST['fileIdx']);

		$sql = "update bbs_data set ";
		$sql .= "bbs_file=0 ";
		$sql .= "where idx=" . $_POST['bbsIdx'];
		$DB->Execute($sql);
		echo json_encode("1");
	}
	
?>