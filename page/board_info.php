<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/board.class.lib");
	include_once(CLASS_PATH . "/select.class.lib");
	$boardClass = new BoardClass();
	$selectClass = new SelectClass();
	
	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit";
		$result = $boardClass->getBoardInfoOne($_GET['idx']);
		
		if($result['bod_file'] > 0){
			$fileInfo = $boardClass->getFileInfo($result['bod_file']);
		}
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
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/board_info.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>Board</p>
				</li>
				<li>
					<a href="#" class="active">Board <?=$pagetitle?></a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Board - <span class="semi-bold"> <?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<form action="/page/board_info_proc.php" id="board_info_form" method="post" enctype="multipart/form-data">
								<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
								<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
								<input type="hidden" id="fileIdx" name="fileIdx" value=<?=$result['bod_file']?>>
								<input type="hidden" name="bod_type" value="1">
								<input type="hidden" name="bod_units" id="bod_units" value="">
								<div class="row margin_grid">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Group</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="bod_unit" id="bod_unit" class="select2 form-control" multiple="multiple">
													<?php
														$selectClass->printUnitsOptions($result['bod_units']);
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Title</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<input name="bod_title" id="bod_title" type="text"  class="form-control" value="<?=$result['bod_title']?>">
											</div>
										</div>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Content</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<textarea name="bod_content" id="bod_content" rows="10" style="width:100%"><?=$result['bod_content']?></textarea>
											</div>
										</div>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group file-group">
											<label class="form-label">File</label>
											<div class="input-group findFile1">
												<input type="text" class="form-control" id="dropinput1" readonly>
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														<input type="file" id="dataFile" name="dataFile">찾아보기
													</span>
												</span>
											</div>
											<div class="input-group delInput1 hidden">
												<a href="/page/downloadData.php?idx=<?=$result['bod_file']?>">
													<input type="text" class="form-control dataFileInput" value="<?=$fileInfo['real_name']?>" readonly>
												</a>
												<span class="input-group-btn">
													<span class="btn btn-danger btnDel" id="dataFileDel">DEL</span>
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<?
										if(isset($result)){
									?>
									<div class="pull-left">
										<button class="btn btn-danger btn-cons" id="board_delete" type="button">Delete</button>
									</div>
									<? } ?>
									<div class="pull-right">
										<button class="btn btn-primary btn-cons" id="board_submit" type="button">Submit</button>										
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
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script src="/plugins/smarteditor/js/HuskyEZCreator.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/board_info.js" type="text/javascript"></script>
</body>
</html>