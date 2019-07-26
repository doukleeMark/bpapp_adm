<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, unit
	
	include_once(CLASS_PATH . "/select.class.lib");
	$selectClass = new SelectClass();
	
	if($_POST['actionType'] == "list"){
		
		$res = $selectClass->printBrandOptions($_POST['unit']);
		
		echo $res;
	}
?>