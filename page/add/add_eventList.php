<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : date

	if(!isset($_POST['date']))return;

	$sql = "select c.*, u.ur_name from cal_data c, user_data u ";
	$sql .= "where c.cal_user = u.idx AND c.cal_date like '{$_POST['date']}%'";
	$eventList = $DB->GetAll($sql);

	for($i=0;$i<count($eventList);$i++){
?>
	<li cal_idx="<?=$eventList[$i]['idx']?>" class="noselect">
		<b><?=$eventList[$i]['cal_title']?></b><p class="writer">작성자 : <span><?=$eventList[$i]['ur_name']?></span></p>
		<? if($eventList[$i]['cal_mail'] > 0){ ?>
		<span class="sendMail">M</span>
		<? } ?>
	</li>
<?php
	}
	if(count($eventList) == 0){
?>
	<li class="blank">
		등록된 이벤트가 없습니다.
	</li>
<?php
	}
?>