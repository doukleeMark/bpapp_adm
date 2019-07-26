<?php
	ini_set("display_errors", 1);

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
	
	include_once(CLASS_PATH . "/quiz.class.lib");
	$quizClass = new QuizClass();
	
	if($_POST['actionType']=="insert") {
		$post = array_replace([], $_POST, ['cq_writer' => $_SESSION['USER_NO']]);
		$quizClass->quizBankInsert($post);
	}else if($_POST['actionType']=="update") {
		$quizClass->quizBankUpdate($_POST);
	}
	echo "<script>location.href = '/?page=el_quizList';</script>";
	
?>