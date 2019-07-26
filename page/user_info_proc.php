<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
	
	include_once(CLASS_PATH . "/user.class.lib");
	$userClass = new UserClass();

	if($_POST['actionType']=="insert" || $_POST['actionType']=="update")
	{
		$obj = array(
					'idx'=>$_POST['idx'],
					'id'=>$_POST['userId'],
					'password'=>$_POST['userPw'],
					'name'=>$_POST['userName'],
					'team'=>$_POST['userTeam'],
					'unit'=>$_POST['userUnit'],
					'level'=>$_POST['userLevel'],
					'position'=>$_POST['userPosition'],
					'teamlevel'=>$_POST['userTeamlevel'],
					'low'=>$_POST['userGroupLow'],
					'high'=>$_POST['userGroupHigh'],
					'grouplevel'=>$_POST['userGroupLevel'],
					'choicelevel'=>$_POST['userChoiceLevel']);
	}

	if($_POST['actionType']=="insert") {
		$insert_urIdx = $userClass->userInsert($obj);
		$userClass-> deviceInsert($insert_urIdx);
	}else if($_POST['actionType']=="update") {
		$userClass->userUpdate($obj);		
	}
	
	echo "<script>location.href = '/?page=user_list';</script>";
	
?>