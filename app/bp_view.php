<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, bp_idx, unit, kn(카나브앱 접속시 댓글 버튼색 구분)
	
	if(isset($_GET['ur_idx']) && isset($_GET['bp_idx'])){

		$sql = "select * from bp_data ";
		$sql .= "where idx={$_GET['bp_idx']} ";
		$bpRes = $DB->GetOne($sql);
		
		// hit 증가
		$hit = $bpRes['bp_hit'] + 1;
		$sql = "update bp_data set bp_hit = {$hit} where idx={$_GET['bp_idx']}";
		$DB->Execute($sql);

		if($bpRes['bp_file'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$bpRes['bp_file']} ";
			$fileRes = $DB->GetOne($sql);
		}

		$sql = "select r.*, u.idx as uidx, u.ur_name, u.ur_id from bp_reply r, user_data u ";
		$sql .= "where r.bpr_user = u.idx AND r.bpr_parent={$_GET['bp_idx']} order by r.bpr_order desc, r.idx desc";
		$replyRes = $DB->GetAll($sql);

		$bp_unit = $bpRes['bp_unit'];

		// 카나브앱에서 접속 확인
		if(isset($_GET['kn']) && (int)$_GET['kn'] == 1) $knApp = "kn";
	}else if(isset($_GET['ur_idx']) && isset($_GET['unit'])){

		if((int)$_GET['unit'] < 1){
			echo "Unit Index Check..";
			return;	
		}

		$bp_unit = $_GET['unit'];
	}else{
		echo "POST Value Check..";
		return;
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
<link href="/assets/plugins/bootstrap4/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/app/css/bp_view.css?v=1901021403" rel="stylesheet" type="text/css"/>
</head>
<body class="unit<?=$bp_unit?> <?=$knApp?>">
	<input type="hidden" id="idx" name="idx" value=<?=$_GET['bp_idx']?>>
	<input type="hidden" id="bp_user" name="bp_user" value=<?=$bpRes['bp_user']?>>
	<input type="hidden" id="bp_brand" name="bp_brand" value=<?=$result['bp_brand']?>>
	<input type="hidden" id="bpr_user" name="bpr_user" value=<?=$_GET['ur_idx']?>>
	<input type="hidden" id="reply_idx" name="reply_idx">
	<div class="reply_list">
	<?php
		for($i=0;$i<count($replyRes);$i++){
			if($replyRes[$i]['bpr_order'] == 8)$addClass = "C_position";
			else if($replyRes[$i]['bpr_order'] == 7)$addClass = "G_position";
			else if($replyRes[$i]['bpr_order'] == 6)$addClass = "U_position";
			else if($replyRes[$i]['bpr_order'] == 5)$addClass = "M_position";
			else if($replyRes[$i]['bpr_order'] == 4)$addClass = "T_position";
			else $addClass = "";
			if($replyRes[$i]['bpr_order'] > 3){
	?>
		<div class="reply_body <?=$addClass?>">
			<span class="reply_user"><b><?=$replyRes[$i]['ur_name']?></b> (<?=$replyRes[$i]['ur_id']?>)</span>
			<span class="reply_date"><?=$replyRes[$i]['bpr_dt_create']?></span>
			<?php
				if($_GET['ur_idx'] == $replyRes[$i]['uidx']){
			?>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_update">수정</button>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_delete">삭제</button>
			<? } ?>
			<div class="reply_content"><?=url_auto_link($replyRes[$i]['bpr_content'], true)?></div>
		</div>
	<?php
			}
		}
	?>
	</div>
	<div id="bp_body">

        <?php if ($bpRes['bp_state'] == 3 && $bpRes['bp_deny_txt']) {?>

            <div class="alert alert-danger mb-10" role="alert">
                <h6><span class="badge badge-danger">전체공개 거부 사유</span></h6>
                <?=$bpRes['bp_deny_txt']?>
            </div>

        <?php } ?>

		<?php
			if($bpRes['bp_file'] > 0 && (strtolower($fileRes['file_ext']) == 'png' || strtolower($fileRes['file_ext']) == 'jpg')){
		?>
		<img src="<?=$fileRes['tmp_name']?>">
		<?php
			}
		?>
		<?=str_replace("\n", "<br>", url_auto_link($bpRes['bp_content'], true))?>
	</div>
	<div class="bp_reply">
		<div class="content">
			<textarea id="bpr_content" placeholder="댓글을 입력하세요." maxlength=300 cols="2"></textarea>
			<div class="bottom">
				<div class="input_length">0/300</div>
				<button type="button" class="reply_btn" id="reply_btn">등록</button>
			</div>
		</div>
	</div>
	<div class="bp_reply_bg">
		<div class="bp_reply_update">
			<div class="bp_reply">
				<div class="content">
					<textarea id="bpr_content_update" placeholder="댓글을 입력하세요." maxlength=300 cols="2"></textarea>
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
			if($replyRes[$i]['bpr_order'] < 4){
	?>
		<div class="reply_body <?=$addClass?>">
			<span class="reply_user"><b><?=$replyRes[$i]['ur_name']?></b> (<?=$replyRes[$i]['ur_id']?>)</span>
			<span class="reply_date"><?=$replyRes[$i]['bpr_dt_create']?></span>
			<?php
				if($_GET['ur_idx'] == $replyRes[$i]['uidx']){
			?>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_update">수정</button>
			<button type="button" reply_idx="<?=$replyRes[$i]['idx']?>" class="reply_delete">삭제</button>
			<?php
				}
			?>
			<div class="reply_content"><?=url_auto_link($replyRes[$i]['bpr_content'], true)?></div>
		</div>
	<?php
			}
		}
	?>
	</div>
    <div class="modal fade" id="denyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-light" id="exampleModalLabel">전체공유 <mark>거부</mark> 사유를 입력해주세요</h5>
                    <button type="button" class="close text-light " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <textarea class="form-control" id="deny-text" rows="4" style="font-size: 12pt"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                    <button id="btnDeny" type="button" class="btn btn-danger">전체공유 거부</button>
                </div>
            </div>
        </div>
    </div>
<script src="/assets/plugins/jquery-3.0.0.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap4/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="/app/js/jquery-ui.min.js"></script>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script src="/app/js/bp_view.js?v=1901021403" type="text/javascript"></script>
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