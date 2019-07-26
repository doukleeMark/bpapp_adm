<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, svf_title, svf_visible, 
	// idx, svs_question, svs_item_1~5, push_send, chkIdxs

	include_once(CLASS_PATH . "/survey.class.lib");
	
	$surveyClass = new SurveyClass();
	
	if($_POST['actionType']=="insert") {

		$obj = array(
			'svf_title'=>$_POST['svf_title'],
			'svf_visible'=>$_POST['svf_visible']);
		
		$insertIdx = $surveyClass->insertSurveyInfo($obj);

		echo $insertIdx;

	}else if($_POST['actionType']=="update") {

		$obj = array(
			'idx'=>$_POST['idx'],
			'svf_title'=>$_POST['svf_title'],
			'svf_visible'=>$_POST['svf_visible']);

		$surveyClass->updateSurveyInfo($obj);
		
		$svs_question = json_decode($_POST['svs_question']);
		$svs_item_1 = json_decode($_POST['svs_item_1']);
		$svs_item_2 = json_decode($_POST['svs_item_2']);
		$svs_item_3 = json_decode($_POST['svs_item_3']);
		$svs_item_4 = json_decode($_POST['svs_item_4']);
		$svs_item_5 = json_decode($_POST['svs_item_5']);
		
		$surveyClass->deleteSurveySubAll($_POST['idx']);

		for($i=0;$i<count($svs_question);$i++){
			
			$subObj = array(
				'svs_parent'=>$_POST['idx'],
				'svs_page'=>($i+1),
				'svs_question'=>$svs_question[$i],
				'svs_item_1'=>$svs_item_1[$i],
				'svs_item_2'=>$svs_item_2[$i],
				'svs_item_3'=>$svs_item_3[$i],
				'svs_item_4'=>$svs_item_4[$i],
				'svs_item_5'=>$svs_item_5[$i]);

			$surveyClass->insertSurveySub($subObj);
		}

		echo "success";
	}else if($_POST['actionType']=="delete"){		
		$list_no = explode(",", $_POST['chkIdxs']);
		for($i=0;$i<count($list_no);$i++){
			$surveyClass->deleteSurveyIdx($list_no[$i]);
		}
	}
	
?>