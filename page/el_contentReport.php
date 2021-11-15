<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID'] || $_SESSION['USER_LEVEL'] < 9){
		echo "<script>location.href='/?page=login'</script>";
		return;
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
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/el_contentReport.css" rel="stylesheet" type="text/css"/>

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
					<a href="#" class="active">Report</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Report</h3>
			</div>
			<div class="row select-group">			
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Report</h4>
						</div>
						<div class="grid-body no-border">
							<div class="col-md-12" style="padding-bottom:50px;">
								<div style="display:inline-block;margin-bottom:5px;">
									<span>그룹</span>
									<select class='' name='filter-unit' style='width:100px;'></select>
								</div>
								<div style="display:inline-block;margin-left:10px;margin-bottom:5px;">
									<span>팀</span>
									<select class='' name='filter-team' style='width:150px;'></select>
								</div>
								<div style="display:inline-block;margin-left:10px;">
									<span>이름</span>
									<div style="display:inline-block;">
										<input name="filter-name" type="text"  class="form-control" placeholder="-" value="" style="min-height:35px;width:100px;">
									</div>
								</div>
								<button class="btn btn-success" id="btnExcel">Excel</button>
							</div>
						</div>
					</div>
				</div>
			</div>
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
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_contentReport.js?v=2111150001" type="text/javascript"></script>
</body>
</html>