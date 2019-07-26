<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
	
	include_once(CLASS_PATH . "/board.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$boardClass = new BoardClass();
	$uploadClass = new UploadClass();

	$mb1 = 1048576; //1Byte-1MB
	$uploadno = 0;

	if($_POST['actionType']=="insert" || $_POST['actionType']=="update")
	{
		// 파일 존재할 경우
		if(isset($_FILES['dataFile']) && $_FILES['dataFile']['size'] > 0){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
			if ($finfo1->size == 0) return;

			$uploadno = $uploadClass->bbsUpload($_FILES['dataFile']);
		}else if($_POST['fileIdx'] > 0){
			$uploadno = $_POST['fileIdx'];
		}

		$obj = array(
					'idx'=>$_POST['idx'],
					'bbs_mode'=>$_POST['bbs_mode'],
					'bbs_unit'=>$_POST['bbs_unit'],
					'bbs_title'=>$_POST['bbs_title'],
					'bbs_content'=>$_POST['bbs_content'],
					'bbs_file'=>$uploadno);
	}

	if($_POST['actionType']=="insert") {
		$boardClass->bbsInsert($obj);
	}else if($_POST['actionType']=="update") {
		$boardClass->bbsUpdate($obj);		
	}
	echo "<script>location.href = '/?page=board_communityList';</script>";
	
?>