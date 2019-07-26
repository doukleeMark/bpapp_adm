<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, descTxt, group[], arrival[], target[]

	if($_POST['actionType'] == "missionSetting"){
		
		for($i=0;$i<count($_POST['group']);$i++){
			$sql = "update mission_data set ";
			$sql .= "md_target={$_POST['target'][$i]}, ";
			$sql .= "md_arrival={$_POST['arrival'][$i]} ";
			$sql .= "where md_group='{$_POST['group'][$i]}'";
			$DB->Execute($sql);
		}

		echo json_encode('1');
		
	}else if($_POST['actionType'] == "missionDesc"){
		
			$sql = "update mission_str set ";
			$sql .= "descTxt='".addslashes($_POST['descTxt'])."' ";
			$sql .= "where idx='1'";
			$DB->Execute($sql);

		echo json_encode('1');	
	}
?>