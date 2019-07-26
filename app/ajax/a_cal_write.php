<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$dataClass = new DataClass();
	$uploadClass = new UploadClass();
		
	if($_POST['actionType']=="cal_write" || $_POST['actionType']=="cal_edit") {
		$mb1 = 1048576; //1Byte-1MB
		if($_POST['cal_img'] > 0)$uploadno = $_POST['cal_img'];
		else $uploadno = 0;

		if(isset($_FILES['calFile'])){
			$finfo1 = $uploadClass->getfileinfo($_FILES['calFile']);
						
			if ($finfo1->size > ($mb1 * 50) || $finfo1->size == 0) {				
				echo "error,업로드 가능한 파일 용량이 아닙니다.";
				return;
			}			
		
			$uploadno = $uploadClass->calUpload($_FILES['calFile']);
		};

	}
	if($_POST['actionType']=="cal_write") {

		// 구버전 대응 코드
		if(isset($_POST['cal_time'])){
			$sql = "insert into cal_data(cal_user, cal_unit, cal_brand, cal_title, cal_content, cal_img, cal_date, cal_time, cal_dt_create) values(";
			$sql .= "'" . $_POST['cal_user'] . "', ";
			$sql .= "'" . $_POST['cal_unit'] . "', ";
			$sql .= "'" . $_POST['cal_brand'] . "', ";
			$sql .= "'" . addslashes($_POST['cal_title']) . "', ";
			$sql .= "'" . addslashes($_POST['cal_content']) . "', ";
			$sql .= "'" . $uploadno . "', ";
			$sql .= "'" . $_POST['cal_date'] . "', ";
			$sql .= "'" . $_POST['cal_time'] . "', ";
			$sql .= "sysdate())";
		}else{
			$sql = "insert into cal_data(cal_user, cal_unit, cal_brand, cal_title, cal_content, cal_img, cal_date, cal_dt_create) values(";
			$sql .= "'" . $_POST['cal_user'] . "', ";
			$sql .= "'" . $_POST['cal_unit'] . "', ";
			$sql .= "'" . $_POST['cal_brand'] . "', ";
			$sql .= "'" . addslashes($_POST['cal_title']) . "', ";
			$sql .= "'" . addslashes($_POST['cal_content']) . "', ";
			$sql .= "'" . $uploadno . "', ";
			$sql .= "'" . $_POST['cal_date'] . "', ";
			$sql .= "sysdate())";
		}

		$DB->Execute($sql);
		$new_idx = $DB->Insert_ID();

		echo json_encode("1");

	}else if($_POST['actionType']=="cal_edit") {

		$sql = "update cal_data set ";
		$sql .= "cal_brand ='" . $_POST['cal_brand'] . "', ";
		$sql .= "cal_title='" . addslashes($_POST['cal_title']) . "', ";
		$sql .= "cal_content='" . addslashes($_POST['cal_content']) . "', ";
		$sql .= "cal_img='" . $uploadno . "', ";
		if(isset($_POST['cal_time']))$sql .= "cal_time='" . $_POST['cal_time'] . "', ";
		$sql .= "cal_date='" . $_POST['cal_date'] . "' ";
		$sql .= "where idx=" . $_POST['calIdx'];
		$DB->Execute($sql);
		
		echo json_encode("1");

	}else if($_POST['actionType']=="cal_img_delete"){
		
		$dataClass->deleteFile($_POST['fileIdx']);

		$sql = "update cal_data set ";
		$sql .= "cal_img=0 ";
		$sql .= "where idx=" . $_POST['calIdx'];
		$DB->Execute($sql);
		echo json_encode("1");
	}
	
?>