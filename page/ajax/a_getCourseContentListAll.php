<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/code.class.lib");
    $codeClass = new codeClass();

    $codePD = $codeClass->getByGroupList("PD");
    $codeDI = $codeClass->getByGroupList("DI");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('idx', 'ct_code_di', 'ct_code_pd', 'ct_title', 'ct_speaker', 'ct_open_type');

    if (isset($_POST['order'])) {
        $sOrder = "ORDER BY ";
        $sOrder .= $orderColumns[intval($_POST['order'][0]['column'])] . " " . $DB->RealEscapeString($_POST['order'][0]['dir']) . " ";
    }

//    $orderColumns = array('ct.idx', 'ct.ct_title', 'ct.ct_speaker', 'ct.ct_open_type');
    $filterColumns = array('ct.ct_title', 'ct.ct_speaker');

    
    $sWhere = "";
    if ($_POST['search']['value'] != "") {
        $sWhere = "AND (";

        if (strpos($_POST['search']['value'], " ")) {
            $words = explode(" ", $_POST['search']['value']);

            for ($i = 0; $i < count($words); $i++) {
                $sWhere .= " (";
                for ($j = 0; $j < count($filterColumns); $j++) {
                    $sWhere .= $filterColumns[$j] . " LIKE '%" . $DB->RealEscapeString($words[$i]) . "%' OR ";
                }
                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ") AND";
            }
        } else {

            for ($i = 0; $i < count($filterColumns); $i++) {
                $sWhere .= $filterColumns[$i] . " LIKE '%" . $DB->RealEscapeString($_POST['search']['value']) . "%' OR ";
            }
        }

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ') ';
    }

    $add_sql = "AND NOT EXISTS (SELECT * FROM course_contents cc WHERE ct.idx = cc.cc_ct_id AND cc.cc_co_id = " . $_POST['course_idx'] . ") ";

    if((int)$_POST['code_di'] > 0){
        $add_sql .= "AND ct_code_di like '%X" . $_POST['code_di'] . ",%' ";
    }
    if((int)$_POST['code_pd'] > 0){
        $add_sql .= "AND ct_code_pd like '%X" . $_POST['code_pd'] . ",%' ";
    }

    $sql = "SELECT 
        ct.idx, 
        ct.ct_open_type,
        ct.ct_title, 
        ct.ct_speaker, 
        ct.ct_code_pd,
        ct.ct_code_di, 
        ct.ct_tag 
    FROM 
        contents ct 
    WHERE
        ct.ct_delete = 0 ";
    $sql .= $add_sql . $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "select count(ct.idx) as cnt from contents ct ";
    $sql .= "where ct.ct_delete = 0 AND ct.ct_open = 1 ";
    $sql .= $add_sql . $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'ct_code_di', 'ct_code_pd', 'ct_title', 'ct_speaker', 'ct_open_type');

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
            } else if ($aColumns[$i] == 'ct_code_di') {

                $str = '';
                $temp = $aRow[$aColumns[$i]];
                if(strlen($aRow[$aColumns[$i]])){
                    $temp = str_replace('X', '', $temp);
                    $temp = substr($temp, 0, -1);
                    $arr = explode(',', $temp);
                    
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
            } else if ($aColumns[$i] == 'ct_code_pd') {
                $str = '';
                $temp = $aRow[$aColumns[$i]];
                if(strlen($aRow[$aColumns[$i]])){
                    $temp = str_replace('X', '', $temp);
                    $temp = substr($temp, 0, -1);
                    $arr = explode(',', $temp);
                    
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
            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>