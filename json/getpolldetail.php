<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, poll_idx
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['poll_idx'])){

		$result = (object)null;
		
		// Survey sub 정보
		$result->subs = (object)null;

		$sql = "select * from survey_sub where ";
		$sql .= "svs_parent = {$_REQUEST['poll_idx']} ";
		$sql .= "order by svs_page asc";
		$resSurveySub = $DB->GetAll($sql);
		
		$tempArr = array();
		for($i=0;$i<count($resSurveySub);$i++) {
			$temp = (object)null;
			$temp->svs_idx = $resSurveySub[$i]['idx'];
			$temp->svs_page = $resSurveySub[$i]['svs_page'];
			$temp->svs_question = urlencode($resSurveySub[$i]['svs_question']);
			$temp->svs_item_1 = urlencode($resSurveySub[$i]['svs_item_1']);
			$temp->svs_item_2 = urlencode($resSurveySub[$i]['svs_item_2']);
			$temp->svs_item_3 = urlencode($resSurveySub[$i]['svs_item_3']);
			$temp->svs_item_4 = urlencode($resSurveySub[$i]['svs_item_4']);
			$temp->svs_item_5 = urlencode($resSurveySub[$i]['svs_item_5']);
			
			$tempArr[] = $temp;
		}
		$result->subs = $tempArr;
	}
	
	echo json_encode($result);
?>