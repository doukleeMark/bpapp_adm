<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	$sql = "select * from app_version";
	$res = $DB->GetOne($sql);
	
	$result = (object)null;
	$result->ver = $res['ver'];
	
	echo json_encode($result);
?>