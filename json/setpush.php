<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : uuid, push
	
	$result = (object)null;
	$result->result = "0";
	
	if(isset($_REQUEST['uuid']) && isset($_REQUEST['push']) ){

		$sql = "update device_data set ";
		
		$sql .= "dvi_push = {$_REQUEST['push']} ";
		$sql .= "where dvi_uuid = '{$_REQUEST['uuid']}'";
		$DB->Execute($sql);

		$result->result = "1";
	}
	
	echo json_encode($result);
?>