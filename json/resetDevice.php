<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : uuid

	/* UUID reset */
	if(isset($_REQUEST['uuid'])){
		$sql = "update device_data set ";
		$sql .= "dvi_uuid=NULL, dvi_os=NULL, dvi_model=NULL, dvi_token=NULL, dvi_version=NULL, dvi_push=31, dvi_dt_add=NULL, dvi_dt_last=NULL ";
		$sql .= "where dvi_uuid='{$_REQUEST['uuid']}'";
		$res = $DB->Execute($sql);
		
		$result = (object)null;
		$result->result = ($res)?"1":"0";

		echo json_encode($result);
	}
?>
