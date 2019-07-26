<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/addon.class.lib");
	$addonClass = new AddonClass();
	
	if($_POST['actionType']=="insert" || $_POST['actionType']=="update") {
		$obj = array(
			'idx'=>$_POST['idx'],
			'qz_title'=>$_POST['qz_title'],
			'qz_item_1'=>$_POST['qz_item_1'],
			'qz_item_2'=>$_POST['qz_item_2'],
			'qz_item_3'=>$_POST['qz_item_3'],
			'qz_item_4'=>$_POST['qz_item_4'],
			'qz_correct'=>$_POST['qz_correct']);
	}

	if($_POST['actionType']=="insert") {
		
		$addonClass->quizInsert($obj);
		
		echo "success";

	}else if($_POST['actionType']=="update") {
		
		$addonClass->quizUpdate($obj);
		
		echo "success";
		
	}
	
?>