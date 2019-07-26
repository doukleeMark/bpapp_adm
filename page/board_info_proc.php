<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
	
	include_once(CLASS_PATH . "/board.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$boardClass = new BoardClass();
	$uploadClass = new UploadClass();

	$mb1 = 1048576; //1Byte-1MB
	$uploadno = 0;

	if($_POST['actionType']=="insert" || $_POST['actionType']=="update"){
		// 파일 존재할 경우
		if(isset($_FILES['dataFile']) && $_FILES['dataFile']['size'] > 0){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
			if ($finfo1->size == 0) return;

			$uploadno = $uploadClass->bbsUpload($_FILES['dataFile']);
		}else if($fileIdx > 0){
			$uploadno = $fileIdx;
		}

		$obj = array(
					'idx'=>$_POST['idx'],
					'bod_units'=>$_POST['bod_units'],
					'bod_type'=>$_POST['bod_type'],
					'bod_title'=>$_POST['bod_title'],
					'bod_content'=>$_POST['bod_content'],
					'bod_file'=>$uploadno);
	}

	if($_POST['actionType']=="insert") {
		$_POST['idx'] = $boardClass->boardInsert($obj);
	}else if($_POST['actionType']=="update") {
		$boardClass->boardUpdate($obj);		
	}

	// 푸쉬
	if($_POST['actionType']=="insert" || $_POST['actionType']=="update"){
		if($_POST['push_send'] == '1' && $_POST['bod_type'] == '1'){
			exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php notice,{$_POST['idx']} > /dev/null &");
		}
	}

	echo "<script>location.href = '/?page=board_list';</script>";
	
?>