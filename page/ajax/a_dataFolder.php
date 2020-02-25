<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, targetFolder, folderName, displayOrder

	include_once(CLASS_PATH . "/data.class.lib");
	$dataClass = new DataClass();

	if($_POST['actionType'] == "insert"){

		$sql = "select fd_unit, fd_depth from folder_data where idx = {$_POST['targetFolder']}";
		$parentInfo = $DB->GetOne($sql);
		
		$sql = "insert into folder_data(fd_unit, fd_name, fd_parent, fd_depth, fd_display) values(";
		$sql .= "'" . $parentInfo['fd_unit'] . "', ";
		$sql .= "'" . $_POST['folderName'] . "', ";
		$sql .= "'" . $_POST['targetFolder'] . "', ";
		$sql .= "'" . ((int)$parentInfo['fd_depth']+1) . "', ";
		$sql .= "'" . $_POST['displayOrder'] . "')";
		
		$DB->Execute($sql);
		//$idx = mysql_insert_id();
		return;

	}else if($_POST['actionType'] == "update"){

		$sql = "update folder_data set ";
		$sql .= "fd_name='" . $_POST['folderName'] . "', ";		
		$sql .= "fd_display='" . $_POST['displayOrder'] . "' ";		
		$sql .= "where idx={$_POST['targetFolder']}";
		
		$DB->Execute($sql);
		return;

	}else if($_POST['actionType']=="delete") {

		return $dataClass->deleteFolder($_POST['targetFolder']);
	}

?>