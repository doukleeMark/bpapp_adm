<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, idx, dt_folders, dt_title, dt_type, push_send, data_idxs

	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/upload.class.lib");

	$dataClass = new DataClass();
	$uploadClass = new UploadClass();

	if($_POST['actionType']=="insert") {
		$mb1 = 1048576; //1Byte-1MB
		$uploadno = 0;
		$fileType = array("","pdf", "mp4");

		if(isset($_FILES['dataFile'])){
			$finfo1 = $uploadClass->getfileinfo($_FILES['dataFile']);
			
			if (!($finfo1->ext == $fileType[$_POST['dt_type']] || $finfo1->ext == strtoupper($fileType[$_POST['dt_type']]))) {
				echo "error,올바른 파일 형식이 아닙니다.";
				return;
			}
			
			if ($finfo1->size > ($mb1 * 300) || $finfo1->size == 0) {				
				echo "error,업로드 가능한 파일 용량이 아닙니다.";
				return;
			}

			$uploadno = $uploadClass->dataUpload($_FILES['dataFile']);
		};

		$obj = array(
			'dt_type'=>$_POST['dt_type'],
			'dt_folders'=>$_POST['dt_folders'],
			'dt_title'=>addslashes($_POST['dt_title']),
			'dt_file'=>$uploadno);
		
		$_POST['idx'] = $dataClass->insertData($obj);

		echo "success";

	}else if($_POST['actionType']=="update") {
		
		$obj = array(
			'idx'=>$_POST['idx'],
			'dt_folders'=>$_POST['dt_folders'],
			'dt_title'=>addslashes($_POST['dt_title']));
		
		$dataClass->updateData($obj);
		
		echo "success";
	}else if($_POST['actionType']=="deleteDataInfo"){

		$dataClass->deleteDataIdx($_POST['idx']);
		echo "success";
	}else if($_POST['actionType']=="ChkDeleteDataInfo"){		
		$list_no = explode(",", $_POST['data_idxs']);
		for($i=0;$i<count($list_no);$i++){
			$dataClass->deleteDataIdx($list_no[$i]);
		}
	}

	// 푸쉬
	if(($_POST['actionType']=="insert" || $_POST['actionType']=="update") && $_POST['push_send'] == '1'){
		exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php data,{$_POST['idx']} > /dev/null &");
	}

	
?>