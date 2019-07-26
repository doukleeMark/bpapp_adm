<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/quiz.class.lib");
    $quizClass = new quizClass();

    // actionType 
    $output = array();
    if($_POST['actionType']=="add_update") {

        $idx = $_POST['idx'];
        $obj = array(
            'idx' => $idx,
            'cq_ct_id' => $_POST['contentIdx'],
            'cq_writer' => $_SESSION['USER_NO'],
            'cq_question' => $_POST['cq_question'],
            'cq_item_1' => $_POST['cq_item_1'],
            'cq_item_2' => $_POST['cq_item_2'],
            'cq_item_3' => $_POST['cq_item_3'],
            'cq_item_4' => $_POST['cq_item_4'],
            'cq_answer' => $_POST['cq_answer']
        );
        if($idx){
            $quizClass->testUpdate($obj);
        }else{
            $quizClass->testInsert($obj);
        }
        $output['result'] = 'success';
        echo json_encode($output);
		return;
        
    }else if($_POST['actionType']=="delTest") {
        $quizClass->testDeletes($_POST['idxs']);

        $output['result'] = 'success';
        echo json_encode($output);
		return;
    }
?>