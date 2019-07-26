<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, cal_idx, unit

	include_once(CLASS_PATH . "/select.class.lib");

	$selectClass = new SelectClass();
	
	if(isset($_GET['ur_idx']) && isset($_GET['cal_idx'])){
		
		$actionType = "cal_edit";
		$btnName = "수 정";

		$sql = "select * from cal_data ";
		$sql .= "where idx={$_GET['cal_idx']} ";
		$calRes = $DB->GetOne($sql);

		if($calRes['cal_img'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$calRes['cal_img']} ";
			$fileRes = $DB->GetOne($sql);
		}
		$_GET['unit'] = $calRes['cal_unit'];
	}else if(isset($_GET['ur_idx']) && isset($_GET['unit'])){

		$actionType = "cal_write";
		$btnName = "등 록";
		
	}else{
		echo "POST Value Check..";
		return;
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta id="viewport" name="viewport" content="minimum-scale=1, maximum-scale=1, user-scalable=yes, initial-scale=1, width=device-width">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="format-detection" content="telephone=no" />
<link href="/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="/app/css/cal_write.css?v=1901021407" rel="stylesheet" type="text/css"/>
</head>

<body>
<form id="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
	<input type="hidden" id="idx" name="idx" value=<?=$_GET['cal_idx']?>>
	<input type="hidden" id="cal_user" name="cal_user" value=<?=$_GET['ur_idx']?>>
	<input type="hidden" id="cal_unit" name="cal_unit" value=<?=$_GET['unit']?>>
	<input type="hidden" id="cal_img" name="cal_img" value=<?=$calRes['cal_img']?>>
	
	<div class="row unit<?=$_GET['unit']?>">
		<input type="text" id="cal_date" name="cal_date" placeholder="일정 날짜를 선택하세요." value="<?=$calRes['cal_date']?>" readonly="readonly">
		<input type="text" id="cal_time" name="cal_time" placeholder="일정 시간을 선택하세요." value="<?=$calRes['cal_time']?>" readonly="readonly">
		<select id="cal_brand" name="cal_brand" class="<?=($actionType == 'cal_write')?'placeholder_color':''?>">
			<option value="">브랜드를 선택해주세요.</option>
			<?php
				$selectClass->printBrandOptionsSelect($_GET['unit'], $calRes['cal_brand']);
			?>
		</select>
		<input type="text" id="cal_title" name="cal_title" placeholder="제목을 입력하세요." value="<?=$calRes['cal_title']?>">
		<span class="btn-file upload_grid <?=$calRes['cal_img'] > 0?'hidden':''?>">
			<span class="fileName">이미지 업로드</span>
			<input type="file" id="calFile" name="calFile" accept='image/jpeg,image/gif,image/png'>
		</span>
		<span class="btn-file fileInfo_grid <?=$calRes['cal_img'] > 0?'':'hidden'?>">
			<span class="fileName"><?=$fileRes['real_name']?><span class="del_btn">delete button</span></span>
		</span>
		<textarea id="cal_content" name="cal_content" class="common" placeholder="내용을 입력하세요."><?=$calRes['cal_content']?></textarea>
		
		<div class="btn-grid">
			<input type="button" class="btn btn-cons" id="cal_submit" value="<?=$btnName?>">
		</div>
	</div>
</form>
<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script src="/app/js/cal_write.js?v=1901021407" type="text/javascript"></script>
</div>
</body>
</html>