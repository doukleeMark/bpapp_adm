
<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	use EddTurtle\DirectUpload\Signature;

	require_once ROOT_PATH."/vendor/autoload.php";

	$upload = new Signature(AWS_ACCESS_KEY, AWS_SECRET, AWS_BUCKET, AWS_REGION, ['acl' => 'public-read']);

	include_once(CLASS_PATH . "/el.class.lib");
	include_once(CLASS_PATH . "/s3.class.lib");

	$elClass = new ELClass();
	$s3Class = new S3Class();

	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit";

		$contRes = $elClass->getContentInfo($_GET['idx']);
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>BP App Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
	
<link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/el_contentInfo.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>E-learning</p>
				</li>
				<li>
					<a href="#" class="active">Contents</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Content Info - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
							<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label">Title</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<input name="ct_title" type="text"  class="form-control" placeholder="" value="<?=$contRes['ct_title']?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Speaker</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<input name="ct_speaker" type="text"  class="form-control" placeholder="" value="<?=$contRes['ct_speaker']?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Description</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<textarea name="ct_desc" id="ct_desc" rows="3" style="width:100%;resize: none;"><?=$contRes['ct_desc']?></textarea>
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Tag</label>
										<div class="controls">
											<input name="ct_tag" id="ct_tag" type="text"  class="form-control" placeholder="Tag" value="<?=$contRes['ct_tag']?>">
										</div>
									</div>
									<?php
										if($actionType == 'update'){
									?>
									<div class="form-group">
										<label class="form-label">Rating</label>
										<p></p>
									</div>
									<?php
										}
									?>
								</div>
								
								<div class="col-md-6">
									<div class="form-group">
										<label class="type-label">Type</label>
										<div class="radio radio-primary">
											<input id="radio_v" type="radio" name="ct_type" value="V" <?=($contRes['ct_type']=="V"|| !isset($contRes))?"checked":"";?> <?=$disabled?>>
											<label for="radio_v">Video</label>
											<input id="radio_a" type="radio" name="ct_type" value="A" <?=($contRes['ct_type']=="A")?"checked":"";?> <?=$disabled?>>
											<label for="radio_a">Audio</label>
										</div>
									</div>
									<?php
										if($contRes['ct_s3_file']>0){
											$fileInfo = $s3Class->getS3Info($contRes['ct_s3_file']);
										}
									?>
									<div class="form-group file-group">
										<input type="hidden" class="file_idx" name="ct_s3_file" value=<?=$contRes['ct_s3_file']?>>
										<form action="<?=$upload->getFormUrl()?>" method="POST" enctype="multipart/form-data" id="content-upload">
										<?php echo $upload->getFormInputsAsHtml(); ?>
										<label class="form-label">Content</label>
										<div class="input-group fileInput <?=($contRes['ct_s3_file'] > 0)?'hidden':'';?>" >
											<input type="text" class="form-control dropinput" placeholder="마우스로 파일을 끌어오세요." readonly>
											<span class="input-group-btn">
												<span class="btn btn-default btn-file">
													<input type="file" name="file" accept="video/mp4,audio/mp3" >찾아보기
												</span>
											</span>
										</div>
										<div class="input-group fileDel <?=($contRes['ct_s3_file'] > 0)?'':'hidden';?>">
											<a href="<?=$fileInfo['s3_url']?>" class="link-filename" target="_blank"><?=$fileInfo['s3_real_name']?></a>
											<span class="input-group-btn">
												<span class="btn btn-warning btn-delfile">삭제</span>
											</span>
										</div>
										<div class="progress-bar-area"></div>
										<span class="help">* 업로드 최대 용량은 300M 입니다. 용량이 클경우, 관리자에게 문의하세요.</span>
										</form>
									</div>
									<?php
										if($contRes['ct_s3_thumb']>0){
											$fileInfo = $s3Class->getS3Info($contRes['ct_s3_thumb']);
										}
									?>
									<div class="form-group file-group">
										<input type="hidden" class="file_idx" name="ct_s3_thumb" value=<?=$contRes['ct_s3_thumb']?>>
										<form action="<?=$upload->getFormUrl()?>" method="POST" enctype="multipart/form-data" id="thumb-upload">
										<?php echo $upload->getFormInputsAsHtml(); ?>
										<label class="form-label">Thumbnail</label>
										<div class="input-group fileInput <?=($contRes['ct_s3_thumb'] > 0)?'hidden':'';?>" >
											<input type="text" class="form-control dropinput" placeholder="마우스로 파일을 끌어오세요." readonly>
											<span class="input-group-btn">
												<span class="btn btn-default btn-file">
													<input type="file" name="file" accept="image/x-png,image/jpeg" >찾아보기
												</span>
											</span>
										</div>
										<div class="input-group fileDel <?=($contRes['ct_s3_thumb'] > 0)?'':'hidden';?>">
											<a href="<?=$fileInfo['s3_url']?>" class="link-filename" target="_blank"><?=$fileInfo['s3_real_name']?></a>
											<span class="input-group-btn">
												<span class="btn btn-warning btn-delfile">삭제</span>
											</span>
										</div>
										<div class="progress-bar-area"></div>
										<span class="help">* 업로드 최대 용량은 1M 입니다. </span>
										</form>
									</div>
								</div>
							</div>
							<div class="actions">
								<div class="pull-right">
									<button class="btn btn-primary btn-cons" id="btn_submit" type="button">Submit</button>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_contentInfo.js" type="text/javascript"></script> 
</body>
</html>