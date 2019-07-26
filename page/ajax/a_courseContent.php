<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/el.class.lib");

    if($_POST['actionType']=="changeOrder") {
        //  courseIdx, ccIdx[], order[]

        $elClass = new ELClass();
        
        for ($i=0; $i <count($_POST['ccIdx']) ; $i++) { 
            $elClass->courseContentUpdateOrder($_POST['courseIdx'], $_POST['ccIdx'][$i], $_POST['order'][$i]);
        }
        

    }else if($_POST['actionType']=="addContent") {
        // courseIdx, idxs

        $elClass = new ELClass();
        $arr = explode(',', $_POST['idxs']);

        foreach($arr as $i){
            $elClass->courseContentInsert($_POST['courseIdx'], $i);
        }
    }else if($_POST['actionType']=="delContent") {
        // courseIdx, idxs

        $elClass = new ELClass();
        $arr = explode(',', $_POST['idxs']);

        foreach($arr as $i){
            $elClass->courseContentDelete($_POST['courseIdx'], $i);
        }
    }
?>