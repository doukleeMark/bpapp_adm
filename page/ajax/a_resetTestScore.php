<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    $co_idx = $_POST['co_idx'];
    $ur_idx = $_POST['ur_idx'];
    $q_no = $_POST['q_no'];

    $sql = "select * from course_contents where cc_co_id={$co_idx} and cc_order={$q_no}";
    $ct_id = $DB->GetOne($sql)['cc_ct_id'];

    $sql = "delete from contents_test_result where ctr_ct_id={$ct_id} and ctr_ur_id={$ur_idx}";
    $DB->Execute($sql);

    $arr = array();
    $arr['co_idx'] = $co_idx;
    $arr['ur_idx'] = $ur_idx;
    $arr['q_no'] = $q_no;
    $arr['rr'] = $sql;

    $output = array( "data" => array() );
    $output['data'][] = $arr;

	echo json_encode($output);
?>