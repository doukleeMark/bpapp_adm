<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx
	
	if(isset($_REQUEST['ur_idx'])){

		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$resUser = $DB->GetOne($sql);

		$result = (object)null;
		
		// Survey 정보
		$result->survey = (object)null;

		$sql = "select * from survey_info where ";
		$sql .= "svf_unit = 7 ";
		$sql .= "AND svf_visible = '1' ";
		$sql .= "order by idx desc";
		$resSurvey = $DB->GetAll($sql);
		
		$tempArr = array();
		for($i=0;$i<count($resSurvey);$i++) {
			$temp = (object)null;
			$temp->svf_idx = $resSurvey[$i]['idx'];
			$temp->svf_title = urlencode($resSurvey[$i]['svf_title']);
			$temp->svf_date = $resSurvey[$i]['svf_date'];
			$complete = '0';			
			$temp->svf_complete = $complete;
			$tempArr[] = $temp;
		}
		$result->survey = $tempArr;
	}
	
	echo json_encode($result);
?>