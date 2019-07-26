<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	include_once(CLASS_PATH . "/quiz.class.lib");
	$quizClass = new QuizClass();

	if($_POST['actionType'] == "deletes"){
		$result = $quizClass->quizBankDeletes($_POST['idxs']);
		echo json_encode($result);
	}
	
?>