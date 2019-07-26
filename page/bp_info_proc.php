<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
	
	include_once(CLASS_PATH . "/bp.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$bpClass = new BPClass();
	$uploadClass = new UploadClass();

	$mb1 = 1048576; //1Byte-1MB
	$uploadno = 0;

	if($_POST['actionType']=="update")
	{
		// 파일 존재할 경우
		if(isset($_FILES['dataFile']) && $_FILES['dataFile']['size'] > 0){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
			if ($finfo1->size == 0) return;

			$uploadno = $uploadClass->bpUpload($_FILES['dataFile']);
		}else if($_POST['fileIdx'] > 0){
			$uploadno = $_POST['fileIdx'];
		}

		$obj = array(
					'idx'=>$_POST['idx'],
					'bp_unit'=>$_POST['bp_unit'],
					'bp_brand'=>$_POST['bp_brand'],
					'bp_title'=>$_POST['bp_title'],
					'bp_content'=>$_POST['bp_content'],
					'bp_new_fu'=>(int)$_POST['bp_new_fu'],
					'bp_file'=>$uploadno);
	}

	if($_POST['actionType']=="update") {
		$bpClass->bpUpdate($obj);		
	}
	
	echo "<script>location.href = '/?page=bp_list';</script>";
	
?>