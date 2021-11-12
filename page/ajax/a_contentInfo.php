<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/el.class.lib");
	include_once(CLASS_PATH . "/s3.class.lib");

	if($_POST['actionType']=="insert") {

        $elClass = new ELClass();
        $s3Class = new S3Class();
         
        // 작성자 정보 추가
        $obj = array(
            'ct_writer' => $_SESSION['USER_NO'],
            'ct_code_pd' => $_POST['ct_code_pd'],
            'ct_code_di' => $_POST['ct_code_di'],
            'ct_code_gd' => $_POST['ct_code_gd'],
            'ct_code_lv' => $_POST['ct_code_lv'],
            'ct_code_ss' => $_POST['ct_code_ss'],
            'ct_title' => $_POST['ct_title'],
            'ct_desc' => $_POST['ct_desc'],
            'ct_speaker' => $_POST['ct_speaker'],
            'ct_tag' => '',
            'ct_s3_file' => (int)$_POST['ct_s3_file'],
            'ct_s3_thumb' => (int)$_POST['ct_s3_thumb'],
            'ct_type' => $_POST['ct_type']
        );

        $content_idx = $elClass->contentInsert($obj);

        // s3파일 전송 및 파일정보 업데이트
        $s3Class->s3Upload($_POST['ct_s3_file'], 'contents' . "/" . $content_idx);
        $s3Class->s3Upload($_POST['ct_s3_thumb'], 'contents' . "/" . $content_idx);

        // 파일삭제
        $deleteIdxs = explode(",", $_POST['d_file']);
        for ($i=0; $i < count($deleteIdxs); $i++) { 
            $s3Class->s3Delete($deleteIdxs[$i]);
        }

        echo json_encode($content_idx);

	}else if($_POST['actionType']=="update") {

        $elClass = new ELClass();
	    $s3Class = new S3Class();

        // 필수로 마지막 배열은 idx 
        $obj = array(
            'ct_code_pd' => $_POST['ct_code_pd'],
            'ct_code_di' => $_POST['ct_code_di'],
            'ct_code_gd' => $_POST['ct_code_gd'],
            'ct_code_lv' => $_POST['ct_code_lv'],
            'ct_code_ss' => $_POST['ct_code_ss'],
            'ct_title' => $_POST['ct_title'],
            'ct_speaker' => $_POST['ct_speaker'],
            'ct_desc' => $_POST['ct_desc'],
            'ct_type' => $_POST['ct_type'],
            'ct_s3_file' => (int)$_POST['ct_s3_file'],
            'ct_s3_thumb' => (int)$_POST['ct_s3_thumb'],
            'ct_test_count' => (int)$_POST['ct_test_count'],
            'idx' => (int)$_POST['idx']
        );

        $elClass->contentUpdate($obj);

        // s3파일 전송 및 파일정보 업데이트
        $s3Class->s3Upload($_POST['ct_s3_file'], 'contents' . "/" . $_POST['idx']);
        $s3Class->s3Upload($_POST['ct_s3_thumb'], 'contents' . "/" . $_POST['idx']);

        // 파일삭제
        $deleteIdxs = explode(",", $_POST['d_file']);
        for ($i=0; $i < count($deleteIdxs); $i++) { 
            $s3Class->s3Delete($deleteIdxs[$i]);
        }

        // 깜짝퀴즈 저장
        for ($i=0; $i < count($_POST['quizIdx']); $i++) { 
            
            $rangeArr = explode(",", $_POST['range'][$i]);

            $quizObj = array(
                'cs_question' => $_POST['cs_question'][$i],
                'cs_item_1' => $_POST['cs_item_1'][$i],
                'cs_item_2' => $_POST['cs_item_2'][$i],
                'cs_item_3' => $_POST['cs_item_3'][$i],
                'cs_item_4' => $_POST['cs_item_4'][$i],
                'cs_answer' => (int)$_POST['cs_answer'][$i],
                'cs_on' => (int)$_POST['cs_on'][$i],
                'cs_start_sec' => (int)$rangeArr[0],
                'cs_end_sec' => (int)$rangeArr[1],
                'cs_ct_id' => (int)$_POST['idx'],
                'idx' => (int)$_POST['quizIdx'][$i]
            );

            if(!((int)$_POST['quizIdx'][$i] > 0)){
                $elClass->supriseQuizInsert($quizObj);
            }else{
                $elClass->supriseQuizUpdate($quizObj);
            }
        }

        // 깜짝퀴즈 삭제
        $del_quizs = explode(",", $_POST['del_quizs']);
        for ($i=0; $i < count($del_quizs); $i++) { 
            $elClass->supriseQuizDelete($del_quizs[$i]);
        }
		
	}else if($_POST['actionType']=="deletes"){

        $elClass = new ELClass();
        $s3Class = new S3Class();

        $arr = explode(',', $_POST['idxs']);

        // 컨텐츠삭제 가능확인
        foreach($arr as $i){
            if($elClass->usedContent($i)){
                echo json_encode(false);
                exit();
            }
        }

        foreach($arr as $i){

            $content = $elClass->getContentInfo($i);

            if($content['ct_s3_file'] > 0){
                $s3Class->s3Delete($content['ct_s3_file']);
            }
            if($content['ct_s3_thumb'] > 0){
                $s3Class->s3Delete($content['ct_s3_thumb']);
			}
			
			/*
			// 컨텐츠와 테스트 연결정보 삭제
			$elClass->contentTestDeleteWithContentID($i);
            // 컨텐츠 테스트결과 정보 삭제
            $elClass->contentTestResultDeleteWithContentID($i);
            // 컨텐츠 플레이 정보 삭제
			$elClass->contentPlayedDeleteWithContentID($i);
			// 컨텐츠 댓글 정보 삭제
			$elClass->contentMsgDeleteWithContentID($i);
			// 컨텐츠 즐겨찾기 정보 삭제
            $elClass->contentFavorDeleteWithContentID($i);
            // 컨텐츠 평가 정보 삭제
            $elClass->contentRatingDeleteWithContentID($i);
            // 컨텐츠 깜짝퀴즈 삭제
            $elClass->contentSupriseDeleteWithContentID($i);
            // 컨텐츠 삭제 
			$elClass->contentDelete($i);
			*/
			// 필수로 마지막 배열은 idx 
			$obj = array(
				'ct_s3_file' => 0,
				'ct_s3_thumb' => 0,
				'ct_delete' => 1,
				'idx' => (int)$i
			);
	
			$elClass->contentUpdate($obj);

        }
        
        echo json_encode(true);
        
	}else if($_POST['actionType']=='deleteFile'){
        
        // POST : idx, file_idx, target(input name)
        
        if((int)$_POST['idx'] > 0){
            $obj = array(
                $_POST['target'] => 0,
                'idx' => (int)$_POST['idx']
            );
    
            $elClass = new ELClass();
            $elClass->contentUpdate($obj);
        }

		$s3Class = new S3Class();
		$s3Class->s3Delete($_POST['file_idx']);
		
	}else if($_POST['actionType']=='open'){

		// POST : idx
		if($_SESSION['USER_LEVEL'] >= 9){
			$obj = array(
				'ct_open' => 1,
				'idx' => (int)$_POST['idx']
			);
	
			$elClass = new ELClass();
			$elClass->contentUpdate($obj);
			echo json_encode(true);
		}else{
			echo json_encode(false);
		}
		
	}else if($_POST['actionType']=='reset'){

		// POST : ur_idx, co_idx

		$ur_idx = (int)$_POST['ur_idx'];
		$co_idx = (int)$_POST['co_idx'];

		if($_SESSION['USER_LEVEL'] >= 9 && $ur_idx > 0 && $co_idx > 0){

			$elClass = new ELClass();
			$elClass->courseContentReset($co_idx, $ur_idx);

			echo json_encode(true);
		}else{
			echo json_encode(false);
		}
		
	}else if($_POST['actionType']=='allReset'){

		// POST : co_idx
		
		$co_idx = (int)$_POST['co_idx'];

		if($_SESSION['USER_LEVEL'] >= 9 && $co_idx > 0){

			$elClass = new ELClass();

			$sql = "SELECT cu_ur_id FROM course_user WHERE cu_co_id = {$co_idx} "; 
			$urList = $DB->GetAll($sql);

			for ($i=0; $i < count($urList); $i++) {
				$elClass->courseContentReset($co_idx, $urList[$i]['cu_ur_id']);
			}

			echo json_encode(true);
		}else{
			echo json_encode(false);
		}
	}
	
?>