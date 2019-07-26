<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/code.class.lib");
    $codeClass = new codeClass();

    $codePD = $codeClass->getByGroupList("PD");
    $codeDI = $codeClass->getByGroupList("DI");

    // Ordering
    $sOrder = "ORDER BY cc.cc_order";

    $sql = "SELECT 
            ct.idx, 
            ct.ct_title, 
            ct.ct_speaker, 
            ct.ct_tag, 
            ct.ct_code_pd,
            ct.ct_code_di, 
            cc.cc_order  
        FROM 
            contents ct, 
            course_contents cc 
		WHERE ct.ct_delete = 0 AND ct.ct_open = 1 AND ct.idx = cc.cc_ct_id AND cc.cc_co_id = " . $_POST['course_idx'] . " ";
    $sql .= $sOrder;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "select count(ct.idx) as cnt from contents ct, course_contents cc ";
    $sql .= "where ct.idx = cc.cc_ct_id and cc.cc_co_id = " . $_POST['course_idx'] . " ";
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'cc_order', 'ct_title', 'ct_speaker', 'ct_code_pd', 'ct_code_di', 'ct_tag');

    $output = array(
        "draw" => $draw,
        "recordsTotal" => $cnt,
        "recordsFiltered" => $cnt,
        "data" => array(),
    );

    $count = 0;
    while ($aRow = mysqli_fetch_array($res)) {
        $count++;
        $row = array();

        for ($i = 0; $i < count($aColumns); $i++) {

            if ($aColumns[$i] == "no") {
                $row['no'] = $count;
            } else if ($aColumns[$i] == 'ct_code_pd') {

                $temp = $aRow[$aColumns[$i]];
                if(strlen($aRow[$aColumns[$i]])){
                    $temp = str_replace('X', '', $temp);
                    $temp = substr($temp, 0, -1);
                    $arr = explode(',', $temp);
                    $str = '';
                    for ($j=0; $j < count($codePD); $j++) { 
                        if($codePD[$j]['idx'] == $arr[0]){
                            $str = $codePD[$j]['code_name'];
                            break;
                        }
                    }
                    if(count($arr) > 1){
                        $str = $str . " 외 " . (count($arr) - 1) . "개";
                    }
                }

                $row[$aColumns[$i]] = $str;
            } else if ($aColumns[$i] == 'ct_code_di') {

                $temp = $aRow[$aColumns[$i]];
                if(strlen($aRow[$aColumns[$i]])){
                    $temp = str_replace('X', '', $temp);
                    $temp = substr($temp, 0, -1);
                    $arr = explode(',', $temp);
                    $str = '';
                    for ($j=0; $j < count($codeDI); $j++) { 
                        if($codeDI[$j]['idx'] == $arr[0]){
                            $str = $codeDI[$j]['code_name'];
                            break;
                        }
                    }
                    if(count($arr) > 1){
                        $str = $str . " 외 " . (count($arr) - 1) . "개";
                    }
                }

                $row[$aColumns[$i]] = $str;
            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>