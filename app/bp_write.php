<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, bp_idx, unit, type

	include_once(CLASS_PATH . "/select.class.lib");
	$selectClass = new SelectClass();

	if(isset($_GET['ur_idx']) && isset($_GET['bp_idx'])){
		
		$actionType = "bp_edit";
		$btnName = "수 정";

		$sql = "select * from bp_data ";
		$sql .= "where idx={$_GET['bp_idx']} ";
		$bpRes = $DB->GetOne($sql);

		if($bpRes['bp_file'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$bpRes['bp_file']} ";
			$fileRes = $DB->GetOne($sql);
		}

		$bp_unit = $bpRes['bp_unit'];
		$bp_brand = $bpRes['bp_brand'];
		$bp_user = $bpRes['bp_user'];
		$bp_new_fu = $bpRes['bp_new_fu'];

	}else if(isset($_GET['ur_idx']) && isset($_GET['unit'])){

		$actionType = "bp_write";
		$btnName = "등 록";

		if((int)$_GET['unit'] < 1){
			echo "Unit Index Check..";
			return;	
		}

		$bp_user = $_GET['ur_idx'];
		$bp_unit = $_GET['unit'];
		
		if ($_GET['type'] == '6')$bp_new_fu = 1;
		else $bp_new_fu = 0;
	}else{
		echo "POST Value Check..";
		return;
	}

	// CP 확인 및 team F/U 작성을 위해 영업팀장 확인
	$sql = "select ur_position from user_data ";
	$sql .= "where idx={$_GET['ur_idx']} ";
	$positionRes = $DB->GetOne($sql);

	$approval_txt = "승인요청";

	if (isset($_GET['fu_idx'])) {
        $fu_idx = $_GET['fu_idx'];

        $sql = "select * from bp_data ";
        $sql .= "where idx={$fu_idx} ";
        $fu_res = $DB->GetOne($sql);

        $fu_title = htmlspecialchars($fu_res['bp_title']);
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
<link href="/app/css/bp_write.css?v=1901021412" rel="stylesheet" type="text/css"/>
</head>

<body>
<form id="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
	<input type="hidden" id="idx" name="idx" value=<?=$_GET['bp_idx']?>>
	<input type="hidden" id="bp_updater" name="bp_updater" value=<?=$_GET['ur_idx']?>>
	<input type="hidden" id="bp_user" name="bp_user" value=<?=$bp_user?>>
	<input type="hidden" id="bp_unit" name="bp_unit" value=<?=$bp_unit?>>
	<input type="hidden" id="bp_file" name="bp_file" value=<?=$bpRes['bp_file']?>>
	<input type="hidden" id="bp_new_fu" name="bp_new_fu" value=<?=$bp_new_fu?>>
    <input type="hidden" id="bp_fu_idx" name="bp_fu_idx" value=<?=$fu_idx?>>
    <input type="hidden" id="bp_fu_txt" name="bp_fu_txt" value=<?=$fu_title?>>

	<div class="row unit<?=$bp_unit?>">
		<select id="bp_brand" name="bp_brand" class="<?=($actionType == 'bp_write')?'placeholder_color':''?>">
			<option value="">브랜드를 선택해주세요.</option>
			<?php
				$selectClass->printBrandOptionsSelect($bp_unit, $bp_brand);
			?>
		</select>

        <?php if ($fu_idx) {?>
            <input type="text" id="bp_title" name="bp_title" maxlength="200" placeholder="제목을 입력하세요." value="<?=$fu_title?>">
        <?php } else { ?>
            <input type="text" id="bp_title" name="bp_title" maxlength="200" placeholder="제목을 입력하세요." value="<?=$bpRes['bp_title']?>">
        <?php } ?>

		<span class="btn-file upload_grid <?=$bpRes['bp_file'] > 0?'hidden':''?>">
			<span class="fileName">이미지 업로드</span>
			<input type="file" id="dataFile" name="dataFile" accept='image/jpeg,image/gif,image/png'>
		</span>
		<span class="btn-file fileInfo_grid <?=$bpRes['bp_file'] > 0?'':'hidden'?>">
			<span class="fileName"><?=$fileRes['real_name']?><span class="del_btn">delete button</span></span>
		</span>
		<textarea id="bp_content" name="bp_content" class="common" placeholder="내용을 입력하세요."><?=$bpRes['bp_content']?></textarea>
		<?php
			if((int)$positionRes['ur_position'] != 2){
		?>
		<div class="check-grid">
			<input type="checkbox" id="bp_approval" value="1">
			<label for="bp_approval"><?=$approval_txt?></label>
		</div>
		<?php
			}
		?>
		<div class="btn-grid">
			<input type="button" class="btn btn-cons" id="bp_submit" value="<?=$btnName?>">
		</div>
	</div>
</form>
<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script src="/app/js/bp_write.js?v=1901071940" type="text/javascript"></script>
</div>
</body>
</html>