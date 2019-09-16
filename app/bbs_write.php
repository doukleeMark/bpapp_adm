
<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, bbs_type, bbs_idx, unit

	if(isset($_GET['ur_idx']) && isset($_GET['bbs_idx'])){
		
		$actionType = "bbs_edit";
		$btnName = "수 정";

		$sql = "select * from bbs_data ";
		$sql .= "where idx={$_GET['bbs_idx']} ";
		$bbsRes = $DB->GetOne($sql);

		if($bbsRes['bbs_file'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$bbsRes['bbs_file']} ";
			$fileRes = $DB->GetOne($sql);
		}

		$_GET['unit'] = $bbsRes['bbs_unit'];

	}else if(isset($_GET['ur_idx']) && isset($_GET['unit'])){

		$actionType = "bbs_write";
		$btnName = "등 록";

		if((int)$_GET['unit'] < 1){
			echo "Unit Index Check..";
			return;	
		}
		
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
<link href="/app/css/bbs_write.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<form id="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
	<input type="hidden" id="idx" name="idx" value=<?=$_GET['bbs_idx']?>>
	<input type="hidden" id="bbs_user" name="bbs_user" value=<?=$_GET['ur_idx']?>>
	<input type="hidden" id="bbs_unit" name="bbs_unit" value=<?=$_GET['unit']?>>
	<input type="hidden" id="bbs_file" name="bbs_file" value=<?=$bbsRes['bbs_file']?>>

	<div class="row unit<?=$_GET['unit']?>">
		<select id="bbs_mode" name="bbs_mode">
			<option value="">게시글 종류를 선택해주세요.</option>
			<?php
				if($bbs_type == "1"){
					$optionArr = array(1=>"칭찬하기", 2=>"자유글", 3=>"DC 현황");
				}else if($bbs_type == "2"){
					$optionArr = array(4=>"요청하기", 5=>"질문하기");
				}else if($bbs_type == "3"){
					$optionArr = array(1=>"칭찬하기", 2=>"자유글", 3=>"DC 현황", 4=>"요청하기", 5=>"질문하기");
				}

				foreach ($optionArr as $key => $value) {
					if($bbsRes['bbs_mode']==$key) $selectedChar = "selected";
					else $selectedChar = "";

					echo "<option value=\"".$key."\" " . $selectedChar . " >".$value."</option>";
				}
			?>
		</select>
		<input type="text" id="bbs_title" name="bbs_title" placeholder="제목을 입력하세요." value="<?=$bbsRes['bbs_title']?>">
		<span class="btn-file upload_grid <?=$bbsRes['bbs_file'] > 0?'hidden':''?>">
			<span class="fileName">이미지 업로드</span>
			<input type="file" id="bbsFile" name="bbsFile" accept='image/jpeg,image/gif,image/png'>
		</span>
		<span class="btn-file fileInfo_grid <?=$bbsRes['bbs_file'] > 0?'':'hidden'?>">
			<span class="fileName"><?=$fileRes['real_name']?><span class="del_btn">delete button</span></span>
		</span>
		<textarea id="bbs_content" name="bbs_content" class="common" placeholder="내용을 입력하세요."><?=$bbsRes['bbs_content']?></textarea>
		
		<div class="btn-grid">
			<input type="button" class="btn btn-cons" id="bbs_submit" value="등 록">
		</div>
	</div>
</form>
<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/plugins/smarteditor/js/HuskyEZCreator.js" type="text/javascript" charset="utf-8"></script>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script src="/app/js/bbs_write.js" type="text/javascript"></script>
</div>
</body>
</html>