<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	// POST : actionType, idx, idxs

	include_once(CLASS_PATH . "/bp.class.lib");
	
	$bpClass = new BPClass();
	
	if($_POST['actionType']=="bpDelete"){

		$bpClass->bpDelete($_POST['idx']);
		echo "success";
	}else if($_POST['actionType']=="chk_bpDelete"){
		$list_no = explode(",", $_POST['idxs']);
		for($i=0;$i<count($list_no);$i++){
			$bpClass->bpDelete($list_no[$i]);
		}
	}
	
?>