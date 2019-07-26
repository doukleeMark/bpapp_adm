<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, svf_idx, svs_idx, svs_page, svs_select
	
	$result = (object)null;
	$result->result = "0";

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['svf_idx']) && isset($_REQUEST['svs_page']) && isset($_REQUEST['svs_select'])){

		$sql = "insert into survey_data(svd_idx, svd_page, svd_user, svd_select, svd_date) values(";
		$sql .= "{$_REQUEST['svf_idx']}, ";
		$sql .= "{$_REQUEST['svs_page']}, ";
		$sql .= "{$_REQUEST['ur_idx']}, ";
		$sql .= "{$_REQUEST['svs_select']}, ";
		$sql .= "sysdate()) ";
		$DB->Execute($sql);

		$result->result = "1";
	}
	
	echo json_encode($result);
?>