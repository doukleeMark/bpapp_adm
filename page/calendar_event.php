<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/select.class.lib");
	$selectClass = new SelectClass();

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
<link href="/css/calendar_event.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>Calendar</p>
				</li>
				<li>
					<a href="#" class="active">Calendar Event</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Calendar - <span class="semi-bold"> Event</span></h3>
			</div>
			<div class="row calendarEventGrid">
				<div class="col-md-4">
					<div class="row">
						<div class="col-xs-12">
							<div class="grid simple calendar_grid">
								<div class="grid-body no-border">
									<div id="datepicker"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4>EVENT <span class="semi-bold">LIST</span></h4>
									<span class="dateText"></span>
								</div>
								<div class="grid-body no-border eventList_grid">
									<ul id="eventList">
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>EVENT <span class="semi-bold">DETAIL</span></h4>
							<span class="actionText">NEW</span>
						</div>
						<div class="grid-body no-border eventDetail">
							<form id="form" method="post" enctype="multipart/form-data">	
								<input type="hidden" id="actionType" name="actionType" value="insert">
								<input type="hidden" id="idx" name="idx" value="">
								<input type="hidden" id="selectDate" name="selectDate" value="">
								<input type="hidden" id="fileIdx" name="fileIdx" value="">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="form-label">Group</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="cal_unit" id="cal_unit" class="select2 form-control">
													<?php
														$selectClass->printUnitOptions();
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="form-label">Brand</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="cal_brand" id="cal_brand" class="select2 form-control"></select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Event Title</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<input name="cal_title" id="cal_title" type="text"  class="form-control" value="">
											</div>
										</div>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label class="form-label">Event Content</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<textarea name="cal_content" id="cal_content" rows="10" style="width:100%"></textarea>
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
												<a href="#">
													<input type="text" class="form-control dataFileInput" value="" readonly>
												</a>
												<span class="input-group-btn">
													<span class="btn btn-danger btnDel" id="dataFileDel">DEL</span>
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="pull-left hidden" id="event_delete_grid">
										<button class="btn btn-danger btn-cons" id="event_delete" type="button">Delete</button>
									</div>
									<div class="pull-right">
										<button class="btn btn-primary btn-cons" id="event_submit" type="button">Submit</button>
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
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/calendar_event.js" type="text/javascript"></script>
</body>
</html>