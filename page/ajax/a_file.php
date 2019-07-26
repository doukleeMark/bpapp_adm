<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, fileIdx, idx
	
	include_once(CLASS_PATH . "/board.class.lib");
	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/calendar.class.lib");
	include_once(CLASS_PATH . "/bp.class.lib");

	$boardClass = new BoardClass();
	$dataClass = new DataClass();
	$calendarClass = new CalendarClass();
	$bpClass = new BPClass();
	
	if($_POST['actionType'] == "fileInfo"){
		if($_POST['fileIdx'] > 0){
			$fileInfo = $boardClass->getFileInfo($_POST['fileIdx']);
		}
		
		echo json_encode($fileInfo);
	}else if($_POST['actionType'] == "deleteFile"){
		$obj = array(
			'idx'=>$_POST['idx'],
			'bod_file'=>'0');
		$boardClass->boardUpdateFile($obj);
		
		if($_POST['fileIdx'] > 0)
			$dataClass->deleteFile($_POST['fileIdx']);

		echo json_encode('1');
	}else if($_POST['actionType'] == "bpDeleteFile"){
		$obj = array(
			'idx'=>$_POST['idx'],
			'bp_file'=>'0');
		$bpClass->bpUpdateFile($obj);
		
		if($_POST['fileIdx'] > 0)
			$dataClass->deleteFile($_POST['fileIdx']);

		echo json_encode('1');
	}else if($_POST['actionType'] == "cal_deleteFile"){
		$obj = array(
			'idx'=>$_POST['idx'],
			'cal_img'=>'0');
		$calendarClass->eventUpdateFile($obj);
		
		if($_POST['fileIdx'] > 0)
			$dataClass->deleteFile($_POST['fileIdx']);

		echo json_encode('1');
	}
?>