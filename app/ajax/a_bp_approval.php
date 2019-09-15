<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // POST : actionType, idx, actionNo

    if(isset($_POST['actionType'])) $actionType = $_POST['actionType'];
    if(isset($_POST['idx'])) $idx = $_POST['idx'];
    if(isset($_POST['actionNo'])) $actionNo = $_POST['actionNo'];
    if(isset($_POST['denyText'])) $denyText = $_POST['denyText'];
    if(isset($actionNo))$bp_state = $actionNo;
    if(isset($idx))$bp_idx = $idx;

    if($actionType=="approval_update") {

        // 구버전 CP 반려건에 대한 처리
        if((int)$bp_state == 3)	$bp_state = '0';

        $sql = "select bp_user, bp_brand, bp_title, bp_content, bp_file from bp_data where idx={$bp_idx}";
        $bpRes = $DB->GetOne($sql);

        if(isset($bpRes['bp_user'])){
            $sql = "select idx, ur_id, ur_point_bp from user_data where idx={$bpRes['bp_user']}";
            $userRes = $DB->GetOne($sql);
        }

        if((int)$bp_state >= 0 && (int)$bp_state <= 5 && isset($bpRes['bp_user']) && isset($userRes['idx']) ){
            $sql = "update bp_data set ";
            $sql .= "bp_state = {$bp_state}, ";
            $sql .= "bp_dt_update = sysdate() ";
            $sql .= "where idx = {$bp_idx}";
            $DB->Execute($sql);
        }

        logwrite('aaa');

        if((int)$bp_state == 0) {
            $sql = "update bp_data set ";
            $sql .= "bp_deny_txt = '{$denyText}' ";
            $sql .= "where idx = {$bp_idx}";

            logwrite($sql);

            $DB->Execute($sql);
        }

        echo json_encode("1");

    }
	
?>