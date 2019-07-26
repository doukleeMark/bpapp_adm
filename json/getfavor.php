<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx
	
	if(isset($_REQUEST['ur_idx'])){

		$result = (object)null;

		// 파일 정보
		$result->file = (object)null;

		$sql = "select di.*, up.tmp_name from data_info di, upload_data up, favor_data fa where ";
		$sql .= "di.dt_file = up.idx AND di.idx = fa.fav_data AND fa.fav_user = {$_REQUEST['ur_idx']} ";
		$sql .= "order by di.idx desc ";
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
			$sql .= "fav_user = {$_REQUEST['ur_idx']} and fav_data = {$resFile[$i]['idx']}";
			$favFile = $DB->GetOne($sql);
			$temp->dt_favor = (isset($favFile['idx']))?'1':'0';
			
			$temp->dt_date = $resFile[$i]['dt_date'];
			$dataArr[] = $temp;
		}
		$result->file = $dataArr;
	}
	
	echo json_encode($result);
?>