<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}

	include_once(CLASS_PATH . "/data.class.lib");
	include_once(CLASS_PATH . "/select.class.lib");
	$dataClass = new DataClass();
	$selectClass = new SelectClass();
	
	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$disabled = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$disabled = "disabled";
		$pagetitle = "Edit";
		$result = $dataClass->getDataInfo($_GET['idx']);
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
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/data_info.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>Data</p>
				</li>
				<li>
					<a href="#" class="active"><?=$pagetitle?> Data</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Data - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Upload Data <span class="semi-bold">Form</span></h4>
							<div class="tools">
								<a href="javascript:;" class="collapse"></a>
							</div>
						</div>
						<div class="grid-body no-border">
							<div class="hidden" id="folder_tree"></div>
							<form id="form" method="post" enctype="multipart/form-data">	
								<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
								<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
								<input type="hidden" id="folderIdx" name="folderIdx" value=<?=$result['dt_folders']?>>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="type-label">Data Type</label>
											<div class="radio radio-primary">
												<input id="radio_pdf" type="radio" name="dt_type" value="1" <?=($result['dt_type']=="1"|| !isset($result))?"checked":"";?> <?=$disabled?>>
												<label for="radio_pdf">PDF</label>
												<input id="radio_mp4" type="radio" name="dt_type" value="2" <?=($result['dt_type']=="2")?"checked":"";?> <?=$disabled?>>
												<label for="radio_mp4">MP4</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Folder</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="dt_folder" id="dt_folder" class="select2 form-control" multiple="multiple">													
												</select>
											</div>											
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="form-label">Data Title</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<input name="dt_title" id="dt_title" type="text"  class="form-control" value="<?=$result['dt_title']?>">
											</div>
										</div>	
									</div>
									<div class="col-md-6">
										<div class="form-group file-group">
											<label class="form-label">Data File</label>
											<div class="input-group findFile1 <?=($result['dt_file']>0)?'hidden':'';?>" >
												<input type="text" class="form-control" id="dropinput1" readonly>
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														<input type="file" id="dataFile" name="dataFile">찾아보기
													</span>
												</span>
											</div>
											
											<div class="input-group delInput1 <?=($result['dt_file']>0)?'':'hidden';?>" style="width:100% !important;">
												<a href="/page/downloadData.php?idx=<?=$result['dt_file']?>">
													<input type="text" class="form-control dataFileInput" value="<?=$result['real_name']?>" readonly>
												</a>
											</div>
											<span class="help">* 업로드 최대 용량은 300M 입니다. 용량이 클경우, 관리자에게 문의하세요.</span>
										</div>
									</div>
									
								</div>
								<div class="form-actions">
									<?php
										if(isset($result) && $result['dt_file'] != "0"){
									?>
									<div class="pull-left">
										<button class="btn btn-danger btn-cons" id="data_delete" type="button">Delete</button>
									</div>
									<?php
										}
									?>
									<div class="pull-right">
										<button class="btn btn-primary btn-cons" id="data_submit" type="button">Submit</button>										
									</div>
									<div class="pull-right">
										<div class="row-fluid">
											<div class="checkbox check-success">
												<input id="push_checkbox" name="push_send" type="checkbox" value="1">
												<label for="push_checkbox">Push Notification</label>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/plugins/easytree/jquery.easytree.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/data_info.js" type="text/javascript"></script> 
</body>
</html>