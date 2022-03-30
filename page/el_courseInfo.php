<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID'] || $_SESSION['USER_LEVEL'] < 9){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}

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

		$rating = 0;
		$playSec = 0;

		$courseRes = $elClass->getCourseInfo($_GET['idx']);
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
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="/plugins/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/DataTables-1.10.18/css/dataTables.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/Buttons-1.5.4/css/buttons.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/RowReorder-1.2.4/css/rowReorder.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/Select-1.2.6/css/select.bootstrap4.css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/el_courseInfo.css" rel="stylesheet" type="text/css"/>

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
				<h3>Course Info - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Course <span class="semi-bold">Information</span></h4>
						</div>
						<div class="grid-body no-border">
							<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
							<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label">Title</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<input name="co_title" type="text"  class="form-control" placeholder="" value="<?=$courseRes['co_title']?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Description</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<textarea name="co_desc" id="co_desc" rows="3" style="width:100%;resize: none;"><?=$courseRes['co_desc']?></textarea>
										</div>
									</div>
									<div class="form-group">
										<label class="type-label">Date</label>
										<div class="row-fluid">
											<div class="radio radio-primary">
												<input id="radio_1" type="radio" name="co_status" value="1" <?=($courseRes['co_status']=="1"|| !isset($courseRes))?"checked":"";?>>
												<label for="radio_1">INFINITE</label>
											</div>
										</div>
										<div class="row-fluid">
											<div class="radio radio-primary date_radio">
												<input id="radio_2" type="radio" name="co_status" value="2" <?=($courseRes['co_status']=="2")?"checked":"";?>>
												<label for="radio_2">&nbsp;</label>
												<div class="inline">
													<div class="input-append success">
														<input type="text" name="co_dt_start" class="form-control datepicker" value="<?=$courseRes['co_dt_start']?>" <?=($courseRes['co_status']=="1"|| !isset($courseRes))?"disabled":"";?>>
														<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
													</div>

													<div class="input-append success">
														<input type="text" name="co_dt_end" class="form-control datepicker" value="<?=$courseRes['co_dt_end']?>" <?=($courseRes['co_status']=="1"|| !isset($courseRes))?"disabled":"";?>>
														<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
													</div>
												</div>
											</div>
										</div>
									</div>
                                    <?php if ($_GET['idx']) {?>
                                    <div class="form-group">
                                        <label class="type-label">Push Notification</label>
                                        <div class="row-fluid">
                                            <div class="radio radio-primary">
                                                <input id="push_type_1" type="radio" name="radio_push" value="1" checked>
                                                <label for="push_type_1">For Attender</label>
                                                <input id="push_type_2" type="radio" name="radio_push" value="2">
                                                <label for="push_type_2">For All</label>
                                            </div>
                                        </div>
                                        <div class="row-fluid m-b-10">
                                            <input type="text" id="push_title" class="form-control" placeholder="알림 타이틀을 입력하세요">
                                        </div>
                                        <div class="row-fluid m-b-10">
                                            <input type="text" id="push_body" class="form-control" placeholder="알림 설명을 입력하세요">
                                        </div>
                                        <button class="btn btn-warning btn-pushsend">Send Notification</button>
                                    </div>
                                    <?php }?>
								</div>
								
								<div class="col-md-6">
									<?php
										if($courseRes['co_s3_thumb']>0){
											$fileInfo = $s3Class->getS3Info($courseRes['co_s3_thumb']);
										}
									?>
									<div class="form-group file-group">
										<form action="/page/ajax/a_fileUpload.php" method="POST" enctype="multipart/form-data" id="thumb-upload" class="file-upload">
											<input type="hidden" class="file_idx" name="co_s3_thumb" value=<?=$courseRes['co_s3_thumb']?>>
											<input type="hidden" class="file_size" value='1'>
											<label class="form-label">Thumbnail</label>
											<div class="input-group fileInput <?=($courseRes['co_s3_thumb'] > 0)?'hidden':'';?>" >
												<input type="text" class="form-control dropinput" placeholder="마우스로 파일을 끌어오세요." readonly>
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														<input type="file" name="file" accept="image/x-png,image/jpeg" >찾아보기
													</span>
												</span>
											</div>
											<div class="fileDel <?=($courseRes['co_s3_thumb'] > 0)?'':'hidden';?>" >
												<img src="<?=$fileInfo['s3_url']?>" alt="thumbnail" style="width:80px;" />
												<div class="input-group  ">
													<a href="<?=$fileInfo['s3_url']?>" class="link-filename" target="_blank"><?=$fileInfo['s3_real_name']?></a>
													<span class="input-group-btn">
														<span class="btn btn-warning btn-delfile">삭제</span>
													</span>
												</div>
											</div>
											<div class="progress-bar-area"></div>
											<span class="help">* 업로드 최대 용량은 1M 입니다. </span>
										</form>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<div class="pull-right">
									<button class="btn btn-primary btn-cons" id="btnSubmit" type="button">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				if($actionType == 'update'){
			?>
			<div class="row select-group">			
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Contents <span class="semi-bold">Select</span></h4>
						</div>
						<div class="grid-body no-border">
							<div class="col-md-12">	
								<table class="table table-bordered" c="table-striped " id="includedContentTable" style="width:100%">
									<thead>
										<tr>
											<th></th>
											<th style="width:1%">순서</th>
											<th style="width:20%">질환</th>
											<th style="width:20%">제품</th>
											<th style="width:49%">제목</th>
											<th style="width:10%">강의자</th>
                                            <th style="width:1%">공개범위</th>
										</tr>
									</thead>
								</table>
							</div>
							<div class="col-md-12">
								<table class="table table-bordered" id="notIncludedContentTable" style="width:100%">
									<thead>
										<tr>
											<th></th>
											<th style="width:20%">질환</th>
											<th style="width:20%">제품</th>
											<th style="width:50%">제목</th>
											<th style="width:10%">강의자</th>
                                            <th style="width:1%">공개범위</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row select-group">			
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Attender : <span class="semi-bold count">0</span> person</h4>
						</div>
						<div class="grid-body no-border">
							<div class="col-md-12">
								<table class="table table-bordered" id="leftAttenderSelect" style="width:100%">
									<thead>
										<tr>
											<th></th>
											<th style="width:9%">그룹</th>
											<th style="width:20%">팀</th>
											<th style="width:10%">이름</th>
											<th style="width:30%">직책</th>
											<th style="width:30%">이메일</th>
										</tr>
									</thead>
								</table>
							</div>
							<div class="col-md-12" style="padding:50px 0;">
								<div style="display:inline-block;margin-bottom:5px;">
									<span>그룹</span>
									<select class='' name='filter-unit' style='width:100px;'></select>
								</div>
								<div style="display:inline-block;margin-left:10px;margin-bottom:5px;">
									<span>팀</span>
									<select class='' name='filter-team' style='width:150px;'></select>
								</div>
								<div style="display:inline-block;margin-left:10px;">
									<span>직책</span>
									<select class='' name='filter-position' style='width:150px;'>
									<?php
										$res = array(
											array("value"=>"", "text"=>"-"),
											array("value"=>"0", "text"=>"RSM"),
											array("value"=>"1", "text"=>"MR"),
											array("value"=>"2", "text"=>"CP"),
											array("value"=>"3", "text"=>"PM"),
											array("value"=>"4", "text"=>"영업팀장"),
											array("value"=>"5", "text"=>"MKT팀장"),
											array("value"=>"6", "text"=>"그룹장"),
											array("value"=>"7", "text"=>"본부장"),
											array("value"=>"8", "text"=>"부문장")
										);
										
										for($i=0; $i<count($res); $i++) {
											echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
										}
									?>
									</select>
								</div>
								<div style="display:inline-block;margin-left:10px;">
									<span>이름</span>
									<div style="display:inline-block;">
										<input name="filter-name" type="text"  class="form-control" placeholder="-" value="" style="min-height:35px;width:100px;">
									</div>
								</div>
								<button class="btn btn-success" id="btnAddAttender">Add</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				}
			?>
		</div>
	</div>
</div>

<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/plugins/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="/plugins/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Buttons-1.5.4/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Buttons-1.5.4/js/buttons.bootstrap4.js"></script>
<script type="text/javascript" src="/plugins/dataTables/RowReorder-1.2.4/js/dataTables.rowReorder.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Select-1.2.6/js/dataTables.select.js"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_courseInfo.js?v=2111150001" type="text/javascript"></script>
</body>
</html>