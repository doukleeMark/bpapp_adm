<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx
	
	if(isset($_REQUEST['ur_idx'])){

		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$userRes = $DB->GetOne($sql);

		$sql = "select ur_id, ur_name, ur_point_bp, ur_team from ";
		$sql .= "user_data where ur_hidden = 0 AND ur_level < 3 order by ur_point_bp desc";
		$rankUserRes = $DB->GetAll($sql);

		$sql = "select ur_team, round(avg(ur_point_bp)) as point from user_data ";
		$sql .= "where ur_team!='' AND ur_hidden = 0 AND ur_level < 3 group by ur_team order by point desc";
		$rankTeamRes = $DB->GetAll($sql);
		
		// 등급 퍼센트 가져오기
		$sql = "select ptr_code, ptr_point from point_role ";
		$sql .= "where ptr_type=3";
		$gradeRoleRes = $DB->GetAll($sql);

		// DB에 없을 경우 기본 퍼센트 
		$platinum_per = 3;
		$royal_per = 7;
		$gold_per = 40;
		$silver_per = 25;
		// $bronze_per 나머지

		for($i=0;$i<count($gradeRoleRes);$i++) {
			if($gradeRoleRes[$i]['ptr_code'] == "grade_platinum")
				$platinum_per = $gradeRoleRes[$i]['ptr_point'];
			else if($gradeRoleRes[$i]['ptr_code'] == "grade_royal")
				$royal_per = $gradeRoleRes[$i]['ptr_point'];
			else if($gradeRoleRes[$i]['ptr_code'] == "grade_gold")
				$gold_per = $gradeRoleRes[$i]['ptr_point'];
			else if($gradeRoleRes[$i]['ptr_code'] == "grade_silver")
				$silver_per = $gradeRoleRes[$i]['ptr_point'];
		}

		// 등급 인원
		$total_user = count($rankUserRes);
		$platinum_user = floor($total_user * ($platinum_per/100));
		$royal_user = floor($total_user * ($royal_per/100));
		$gold_user = floor($total_user * ($gold_per/100));
		$silver_user = floor($total_user * ($silver_per/100));
		$bronze_user = $total_user - ($platinum_user+$royal_user+$gold_user+$silver_user);

		// 마이개인 순위
		$my_user_rank = '-';
		$my_user_grade = '1';
		// 개인 전체 순위
		$rank_userArr = array();
		$userRank = 1;
		$addRank = 1;
		for($i=0;$i<count($rankUserRes);$i++) {
			
			if($i != 0){
				if($rankUserRes[$i]['ur_point_bp'] == $rankUserRes[$i-1]['ur_point_bp']){
					$addRank = $addRank + 1;
				}else{
					$userRank = $userRank + $addRank;
					$addRank = 1;
				}
			}

			$temp = (object)null;
			$temp->ur_rank = $userRank;

			if($platinum_user >= $userRank && $rankUserRes[$i]['ur_point_bp'] > 0){
				$userGrade = "5";
			}else if(($platinum_user+$royal_user) >= $userRank && $rankUserRes[$i]['ur_point_bp'] > 0){
				$userGrade = "4";
			}else if(($platinum_user+$royal_user+$gold_user) >= $userRank && $rankUserRes[$i]['ur_point_bp'] > 0){
				$userGrade = "3";
			}else if(($platinum_user+$royal_user+$gold_user+$silver_user) >= $userRank && $rankUserRes[$i]['ur_point_bp'] > 0){
				$userGrade = "2";
			}else{
				$userGrade = "1";
			}
			
			$temp->ur_grade = $userGrade;
			$temp->ur_name = urlencode($rankUserRes[$i]['ur_name']);
			$temp->ur_team = urlencode($rankUserRes[$i]['ur_team']);
			$temp->ur_point = $rankUserRes[$i]['ur_point_bp'];
			
			$rank_userArr[] = $temp;

			if($userRes['ur_id'] == $rankUserRes[$i]['ur_id']){
				$my_user_rank = $userRank;
				$my_user_grade = $userGrade;
			}
		}

		// 마이팀 순위
		$my_team_rank = '';
		$my_team_point = '';

		// 팀별 순위 
		$rank_teamArr = array();
		$teamRank = 1;
		$addRank = 1;
		for($i=0;$i<count($rankTeamRes);$i++) {

			if($i != 0){
				if($rankTeamRes[$i]['point'] == $rankTeamRes[$i-1]['point']){
					$addRank = $addRank + 1;
				}else{
					$teamRank = $teamRank + $addRank;
					$addRank = 1;
				}
			}

			$temp = (object)null;
			$temp->rank = (string)$teamRank;
			$temp->team = urlencode($rankTeamRes[$i]['ur_team']);
			$temp->point = $rankTeamRes[$i]['point'];
			
			$rank_teamArr[] = $temp;

			if($userRes['ur_team'] == $rankTeamRes[$i]['ur_team']){
				$my_team_rank = (string)$teamRank;
				$my_team_point = $rankTeamRes[$i]['point'];
			}
		}

		$result = (object)null;

		// myLikeCnt
		$sql = "select count(lk_bbs) as cnt from like_data where lk_user='{$_REQUEST['ur_idx']}'";
		$myLikeBBS = $DB->GetOne($sql);

		$sql = "select count(bl_bp) as cnt from bp_like where bl_user='{$_REQUEST['ur_idx']}'";
		$myLikeBP = $DB->GetOne($sql);

		// myWriteCnt
		$sql = "select count(idx) as cnt from bbs_data where bbs_user='{$_REQUEST['ur_idx']}'";
		$myWriteBBS = $DB->GetOne($sql);

		$sql = "select count(idx) as cnt from bp_data where bp_user='{$_REQUEST['ur_idx']}' and bp_hidden=0";
		$myWriteBP = $DB->GetOne($sql);
		
		// mypage
		$result->mypage = (object)null;

		$result->mypage->my_grade = $my_user_grade;
		$result->mypage->my_point = $userRes['ur_point_bp'];

		$result->mypage->my_user_rank = $my_user_rank;
		$result->mypage->my_team_rank = $my_team_rank;
		$result->mypage->my_team_name = $userRes['ur_team'];
		$result->mypage->my_team_point = $my_team_point;

		$result->mypage->my_like_cnt = (int)$myLikeBBS['cnt']+(int)$myLikeBP['cnt'];
		$result->mypage->my_write_cnt = (int)$myWriteBBS['cnt']+(int)$myWriteBP['cnt'];

		$result->rank_user = (object)null;
		$result->rank_user = $rank_userArr;

		$result->rank_team = (object)null;
		$result->rank_team = $rank_teamArr;

	}
	
	echo json_encode($result);
?>