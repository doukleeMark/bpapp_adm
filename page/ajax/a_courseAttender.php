<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/el.class.lib");
    include_once(CLASS_PATH . "/user.class.lib");

    if($_POST['actionType']=="addAttender") {
        // courseIdx, f_unit, f_team, f_position, f_name

        $elClass = new ELClass();
        $userClass = new UserClass();

        $obj = Array(
            unit => $_POST['f_unit'],
            team => $_POST['f_team'],
			position => $_POST['f_position'],
			name => $_POST['f_name']
        );

        $user_list = $userClass->getUserListByFilter($obj);

        foreach($user_list as $u){
            if(isset($u['idx'])){
                $elClass->courseAttenderInsert($_POST['courseIdx'], $u);
            }
        }
    }else if($_POST['actionType']=="delAttender") {
        // courseIdx, idxs

        $elClass = new ELClass();
        $arr = explode(',', $_POST['idxs']);

        foreach($arr as $i){
            $elClass->courseAttenderDelete($_POST['courseIdx'], $i);
        }
    }
?>