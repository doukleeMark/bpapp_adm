<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, bbs_idx, unit
	if(isset($_GET['ur_idx']) && isset($_GET['bbs_idx'])){

		$sql = "select * from bbs_data ";
		$sql .= "where idx={$_GET['bbs_idx']} ";
		$bbsRes = $DB->GetOne($sql);
		
		// hit 증가
		$hit = $bbsRes['bbs_hit'] + 1;
		$sql = "update bbs_data set bbs_hit = {$hit} where idx={$_GET['bbs_idx']}";
		$DB->Execute($sql);

		if($bbsRes['bbs_file'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$bbsRes['bbs_file']} ";
			$fileRes = $DB->GetOne($sql);
		}

		$sql = "select r.*, u.idx as uidx, u.ur_name, u.ur_id from bbs_reply r, user_data u ";
		$sql .= "where r.bbr_user = u.idx AND r.bbr_parent={$_GET['bbs_idx']} order by r.idx desc";
		$replyRes = $DB->GetAll($sql);

	}
	
	function url_auto_link($str = '', $popup = false)
	{
		if (empty($str)) {
			return false;
		}
		$target = $popup ? 'target="_blank"' : '';
		$str = str_replace(
			array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"),
			array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"),
			$str
		);
		$str = preg_replace(
			"/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i",
			"\\1<a href=\"javascript:open_web('\\2');\" {$target}>\\2</A>",
			$str
		);
		
		return $str;
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
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/app/css/bbs_view.css?v=1901021358" rel="stylesheet" type="text/css"/>
</head>
<body class="unit<?=$_GET['unit']?>">
	<input type="hidden" id="idx" name="idx" value=<?=$_GET['bbs_idx']?>>
	<input type="hidden" id="bbs_user" name="bbs_user" value=<?=$bbsRes['bbs_user']?>>
	<input type="hidden" id="bbs_mode" name="bbs_mode" value=<?=$bbsRes['bbs_mode']?>>
	<input type="hidden" id="bbr_user" name="bbr_user" value=<?=$_GET['ur_idx']?>>
	<input type="hidden" id="reply_idx" name="reply_idx">
	<div id="bbs_body">
		<?php
			if($bbsRes['bbs_file'] > 0 && (strtolower($fileRes['file_ext']) == 'png' || strtolower($fileRes['file_ext']) == 'jpg')){
		?>
		<img src="<?=$fileRes['tmp_name']?>">
		<?php
			}
		?>
		<?=str_replace("\n", "<br>", url_auto_link($bbsRes['bbs_content'], true))?>
	</div>
	<div class="bbs_reply">
		<div class="content">
			<textarea id="bbr_content" placeholder="댓글을 입력하세요." maxlength=300 cols="2"></textarea>
			<div class="bottom">
				<div class="input_length">0/300</div>
				<button type="button" class="reply_btn" id="reply_btn">등록</button>
			</div>
		</div>
	</div>
	<div class="bbs_reply_bg">
		<div class="bbs_reply_update">
			<div class="bbs_reply">
				<div class="content">
					<textarea id="bbr_content_update" placeholder="댓글을 입력하세요." maxlength=300 cols="2"></textarea>
					<div class="bottom">
						<div class="input_length">0/300</div>
						<button type="button" class="reply_btn" id="reply_btn_update">수정</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="reply_list">
	<?php
		for($i=0;$i<count($replyRes);$i++){
			if($replyRes[$i]['bbr_order'] == 8)$addClass = "C_position";
			else if($replyRes[$i]['bbr_order'] == 7)$addClass = "G_position";
			else if($replyRes[$i]['bbr_order'] == 6)$addClass = "U_position";
			else if($replyRes[$i]['bbr_order'] == 5)$addClass = "M_position";
			else if($replyRes[$i]['bbr_order'] == 4)$addClass = "T_position";
			else $addClass = "";
	?>
		<div class="reply_body <?=$addClass?>">
			<span class="reply_user"><b><?=$replyRes[$i]['ur_name']?></b> (<?=$replyRes[$i]['ur_id']?>)</span>
			<span class="reply_date"><?=$replyRes[$i]['bbr_dt_create']?></span>
			<?
				if($_GET['ur_idx'] == $replyRes[$i]['uidx']){
			?>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_update">수정</button>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_delete">삭제</button>
			<? } ?>
			<div class="reply_content"><?=url_auto_link($replyRes[$i]['bbr_content'], true)?></div>
		</div>
	<?php
		}
	?>
	</div>
<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="/app/js/jquery-ui.min.js"></script>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script src="/app/js/bbs_view.js?v=1901021358" type="text/javascript"></script>
<script type="text/javascript">
	function open_web(_url){
		if(isMobile.iOS()){
			document.location = "jscall://link|" + _url;
		}else if(isMobile.Android()){
			window.android.callAndroid("link|" + _url);
		}else {
			window.open(_url,'_blank');
		}
	}
</script>
</body>
</html>