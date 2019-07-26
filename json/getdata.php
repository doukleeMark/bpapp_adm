<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, fd_idx, unit
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['fd_idx']) && isset($_REQUEST['unit'])){
		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$resUser = $DB->GetOne($sql);

		if((int)$_REQUEST['fd_idx'] == 0)$parentIdx = $_REQUEST['unit'];
		else $parentIdx = $_REQUEST['fd_idx'];

		$result = (object)null;
		
		// 폴더 정보
		$result->folder = (object)null;
		
		$sql = "select * from folder_data where fd_parent = {$parentIdx} order by idx";
		$resFolder = $DB->GetAll($sql);
		$folderArr = array();
		for($i=0;$i<count($resFolder);$i++) {

			$temp = (object)null;
			$temp->fd_idx = $resFolder[$i]['idx'];
			$temp->fd_parent = $resFolder[$i]['fd_parent'];
			$temp->fd_title = urlencode($resFolder[$i]['fd_name']);
			$temp->fd_display = $resFolder[$i]['fd_display'];

			$sql = "select dt_date from data_info where ";
			$sql .= "dt_folders like '%X{$resFolder[$i]['idx']},%' ";
			$sql .= "order by idx desc limit 1";
			$lastRes = $DB->GetOne($sql);

			// 데이터 업로드시간 기준 하루까지 상태표시
			if(isset($lastRes['dt_date'])){
				$diff = strtotime(date("Y-m-d")) - strtotime(substr($lastRes['dt_date'], 0, 10));
				$years = floor($diff / (365*60*60*24)); 
				$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
				$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
				$daycount = $years*365+$months*30+$days;
				$temp->fd_new = ($daycount > 1 )?'0':'1';
			}else $temp->fd_new = '0';
			
			$folderArr[] = $temp;
		}
		$result->folder = $folderArr;

		// 파일 정보
		$result->file = (object)null;

		$sql = "select di.*, up.tmp_name from data_info di, upload_data up where ";
		$sql .= "di.dt_file = up.idx ";
		$sql .= "AND di.dt_folders like '%X{$parentIdx},%' ";
		$sql .= "order by idx desc ";
		$resFile = $DB->GetAll($sql);
		$dataArr = array();
		for($i=0;$i<count($resFile);$i++) {
			$temp = (object)null;
			$temp->dt_idx = $resFile[$i]['idx'];
			$temp->dt_type = $resFile[$i]['dt_type'];
			$temp->dt_folder = $resFile[$i]['dt_folder'];
			$temp->dt_title = urlencode($resFile[$i]['dt_title']);
			$temp->dt_file = urlencode(SHOST.$resFile[$i]['tmp_name']);

			// 데이터 업로드시간 기준 하루까지 상태표시
			$diff = strtotime(date("Y-m-d")) - strtotime(substr($resFile[$i]['dt_date'], 0, 10));
			$years = floor($diff / (365*60*60*24)); 
			$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
			$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
			$daycount = $years*365+$months*30+$days;
			$temp->dt_new = ($daycount > 1 )?'0':'1';

			// 즐겨찾기 여부 확인
			$sql = "select idx from favor_data where ";
			$sql .= "fav_user = {$resUser['idx']} and fav_data = {$resFile[$i]['idx']}";
			$favFile = $DB->GetOne($sql);
			$temp->dt_favor = (isset($favFile['idx']))?'1':'0';

			$temp->dt_date = $resFile[$i]['dt_date'];
			$dataArr[] = $temp;
		}
		$result->file = $dataArr;
	}
	
	echo json_encode($result);
?>