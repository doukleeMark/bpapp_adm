<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
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
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/user_point.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>User</p>
				</li>
				<li>
					<a href="#" class="active">Point List</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Point - <span class="semi-bold">List</span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4><span class="semi-bold">Send Point</span></h4>
						</div>
						<div class="grid-body no-border sendPoint">
							<div class="form-inline">
								<div class="form-group col-md-3">
									<label class="form-label">Target Email</label>
									<div class="controls">
										<input type="text" class="form-control" id="targetEmail" placeholder="insert email">
									</div>
								</div>
								<div class="form-group col-md-5">
									<label class="form-label">Event Name</label>
									<div class="controls">
										<input type="text" class="form-control" id="eventName" placeholder="insert point event tilte">
									</div>
								</div>
								<div class="form-group col-md-2">
									<label class="form-label">Point</label>
									<div class="controls">
										<input type="text" class="form-control" id="eventPoint" placeholder="0">
									</div>
								</div>
								<div class="form-group col-md-2 sendBtnGroup">
									<button type="button" class="btn btn-primary btn-cons form-control" id="pointSendBtn">SEND</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">						

						</div>
						<div class="grid-body no-border">
							<table class="table table-hover table-condensed" id="listTable">
								<thead>
									<tr>
										<th style="width:1%" >
											<div class="checkbox check-default">
												<input id="checkbox0" type="checkbox" value="0" class="checkall"/>
												<label for="checkbox0"></label>
											</div>
										</th>
										<th style="width:1%"><nobr>NO.</nobr></th>
										<th style="width:8%"><nobr>EMAIL</nobr></th>
										<th style="width:8%"><nobr>NAME</nobr></th>
										<th style="width:5%"><nobr>UNIT</nobr></th>
										<th style="width:5%"><nobr>LEVEL</nobr></th>
										<th style="width:10%"><nobr>EVENT</nobr></th>
										<th style="width:5%"><nobr>POINT</nobr></th>
										<th style="width:5%"><nobr>TOTAL POINT</nobr></th>
										<th style="width:5%"><nobr>DATE</nobr></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<div id="add_table-action" class="hidden">
								<div class="table-action">
									<div class="pull-left">
										<div class="btn-group">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Action<span class="caret"></span></button>
											<ul class="dropdown-menu">
												<li><a href="/excel/pointList_excel.php">Export Excel</a></li>
											</ul>
										</div>
									</div>
									<div class="pull-right"></div>
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
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script> 
<script type="text/javascript" src="/plugins/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/plugins/dataTables/dataTables.bootstrap.min.js"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/user_point.js" type="text/javascript"></script> 
</body>
</html>