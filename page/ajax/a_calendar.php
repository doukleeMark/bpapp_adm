<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, unit, idx, cal_unit, cal_brand, cal_title, cal_content, cal_date, fileIdx
	foreach ($_REQUEST as $key => $value) {$$key = $value;}

	include_once(CLASS_PATH . "/calendar.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$calendarClass = new CalendarClass();
	$uploadClass = new UploadClass();

	if($_POST['actionType'] == "getEventDate"){

		if(isset($_POST['unit']) && (int)$_POST['unit'] > 0) $u = " and c.cal_unit = {$_POST['unit']} ";
		
		$sql = "select DISTINCT c.cal_date as calDate from cal_data c, user_data u ";
		$sql .= "where c.cal_user = u.idx ".$u." and u.ur_level = 9";
		$adminRes = $DB->GetAll($sql);

		$sql = "select DISTINCT c.cal_date as calDate from cal_data c, user_data u ";
		$sql .= "where c.cal_user = u.idx ".$u." and u.ur_level != 9";
		$userRes = $DB->GetAll($sql);

		$result = (object)null;
		$result->admin = (object)null;
		$result->user = (object)null;
		
		$adminEvent = array();
		for($i=0;$i<count($adminRes);$i++){
			$adminEvent[] = $adminRes[$i]['calDate'];
		}
		$result->admin = $adminEvent;

		$userEvent = array();
		for($i=0;$i<count($userRes);$i++){
			$userEvent[] = $userRes[$i]['calDate'];
		}
		$result->user = $userEvent;

		echo json_encode($result);
	}else if($_POST['actionType'] == "getEventDetail"){
		$sql = "select * from cal_data ";
		$sql .= "where idx={$_POST['idx']}";
		$res = $DB->GetOne($sql);

		echo json_encode($res);
	}else if($_POST['actionType']=="insert" || $_POST['actionType']=="update"){

		$mb1 = 1048576; //1Byte-1MB
		$uploadno = 0;

		if(isset($_FILES['dataFile']) && $_FILES['dataFile']['size'] > 0){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
			if ($finfo1->size == 0) return;

			$uploadno = $uploadClass->calUpload($_FILES['dataFile']);
		}else if($_POST['fileIdx'] > 0){
			$uploadno = $_POST['fileIdx'];
		}

		$obj = array(
					'idx'=>$_POST['idx'],
					'cal_unit'=>$_POST['cal_unit'],
					'cal_brand'=>$_POST['cal_brand'],
					'cal_title'=>$_POST['cal_title'],
					'cal_content'=>$_POST['cal_content'],
					'cal_img'=>$uploadno,
					'cal_date'=>$_POST['cal_date']);

		if($_POST['actionType']=="insert") {
			$calendarClass->eventInsert($obj);
		}else if($_POST['actionType']=="update") {
			$calendarClass->eventUpdate($obj);		
		}
		
	}else if($_POST['actionType']=="delete"){
		$calendarClass->eventDelete($_POST['idx']);	
	}

?>