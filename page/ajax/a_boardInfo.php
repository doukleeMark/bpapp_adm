<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	// POST : actionType, idx, board_idxs, 

	include_once(CLASS_PATH . "/board.class.lib");
	include_once(CLASS_PATH . "/data.class.lib");
	
	$boardClass = new BoardClass();
	$dataClass = new DataClass();

	if($_POST['actionType']=="deleteBoardInfo"){

		$boardClass->deleteBoardIdx($_POST['idx']);
		echo "success";
	}else if($_POST['actionType']=="ChkDeleteBoardInfo"){		
		$list_no = explode(",", $_POST['board_idxs']);
		for($i=0;$i<count($list_no);$i++){
			$boardClass->deleteBoardIdx($list_no[$i]);
		}
	}else if($_POST['actionType']=="deleteBBSInfo"){
		$bbsRes = $boardClass->getBBSInfoOne($_POST['idx']);

		if(isset($bbsRes['idx'])){
			$boardClass->deleteBBRIdx($bbsRes['idx']);
			
			if($bbsRes['bbs_file'] > 0){
				// 첨부이미지 삭제
				$dataClass->deleteFile($bbsRes['bbs_file']);
			}
			
			$boardClass->deleteBBSIdx($bbsRes['idx']);

			echo "success";
		}
	}else if($_POST['actionType']=="ChkDeleteBBSInfo"){		
		$list_no = explode(",", $_POST['board_idxs']);
		for($i=0;$i<count($list_no);$i++){
			$bbsRes = $boardClass->getBBSInfoOne($list_no[$i]);
			if(isset($bbsRes['idx'])){
				$boardClass->deleteBBRIdx($bbsRes['idx']);
				if($bbsRes['bbs_file'] > 0){
					// 첨부이미지 삭제
					$dataClass->deleteFile($bbsRes['bbs_file']);
				}
				$boardClass->deleteBBSIdx($bbsRes['idx']);
			}
		}
	}
	
?>