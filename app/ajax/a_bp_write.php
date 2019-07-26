<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$dataClass = new DataClass();
	$uploadClass = new UploadClass();

	if($_POST['actionType']=="bp_write" || $_POST['actionType']=="bp_edit") {
		$mb1 = 1048576; //1Byte-1MB
		if($_POST['bp_file'] > 0)$uploadno = $_POST['bp_file'];
		else $uploadno = 0;

		if(isset($_FILES['dataFile'])){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
						
			if ($finfo1->size > ($mb1 * 50) || $finfo1->size == 0) {				
				echo "error,업로드 가능한 파일 용량이 아닙니다.";
				return;
			}			
		
			$uploadno = $uploadClass->bpUpload($_FILES['dataFile']);
		};

		$sql = "select ur_group_level from user_data ";
		$sql .= "where idx={$_POST['bp_user']} ";
		$userRes = $DB->GetOne($sql);

	}
	if($_POST['actionType']=="bp_write") {

		if((int)$_POST['bp_approval'] == 1){
			if((int)$userRes['ur_group_level'] == 1){
				$bp_state = 4;
			}else{
				$bp_state = 2;
			}
		} else $bp_state = 0;

		$sql = "insert into bp_data(bp_user, bp_unit, bp_brand, bp_title, bp_content, bp_new_fu, bp_file, bp_state, bp_date, bp_dt_update) values(";
		$sql .= "'" . $_POST['bp_user'] . "', ";
		$sql .= "'" . $_POST['bp_unit'] . "', ";
		$sql .= "'" . $_POST['bp_brand'] . "', ";
		$sql .= "'" . addslashes($_POST['bp_title']) . "', ";
		$sql .= "'" . addslashes($_POST['bp_content']) . "', ";
		$sql .= "'" . $_POST['bp_new_fu'] . "', ";
		$sql .= "'" . $uploadno . "', ";
		$sql .= "'" . $bp_state . "', ";
		$sql .= "sysdate(), sysdate())";
		$DB->Execute($sql);
		$bp_idx = $DB->Insert_ID();

		if((int)$bp_state > 0)echo json_encode("open");
		else echo json_encode("private");
		
	}else if($_POST['actionType']=="bp_edit") {

		$bp_state = $_POST['bp_state'];
		
		if((int)$_POST['bp_approval'] == 1 && ((int)$_POST['bp_user'] == (int)$_POST['bp_updater'])){
			if((int)$userRes['ur_group_level'] == 1){
				$bp_state = 4;
			}else{
				$bp_state = 2;
			}
		}

		$sql = "update bp_data set ";
		$sql .= "bp_brand ='" . $_POST['bp_brand'] . "', ";
		$sql .= "bp_title ='" . addslashes($_POST['bp_title']) . "', ";
		$sql .= "bp_content ='" . addslashes($_POST['bp_content']) . "', ";
		$sql .= "bp_new_fu = " . $_POST['bp_new_fu'] . ", ";
		if((int)$_POST['bp_approval'] == 1 && ((int)$_POST['bp_user'] == (int)$_POST['bp_updater']))$sql .= "bp_state = " . $bp_state . ", ";
		$sql .= "bp_file = '" . $uploadno . "', ";
		$sql .= "bp_dt_update = sysdate() ";
		$sql .= "where idx=" . $_POST['bp_idx'];
		$DB->Execute($sql);

		$bp_idx = $_POST['bp_idx'];
		
		if((int)$bp_state > 0)echo json_encode("open");
		else echo json_encode("private");

	}else if($_POST['actionType']=="bp_deleteFile"){
		
		$dataClass->deleteFile($_POST['fileIdx']);

		$sql = "update bp_data set ";
		$sql .= "bp_file=0 ";
		$sql .= "where idx=" . $_POST['bpIdx'];
		$DB->Execute($sql);
		echo json_encode("1");
	}

?>