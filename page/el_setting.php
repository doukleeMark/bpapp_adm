<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/el.class.lib");
	$elClass = new ELClass();
	
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
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/DataTables-1.10.18/css/dataTables.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/Select-1.2.6/css/select.bootstrap4.css"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/el_setting.css" rel="stylesheet" type="text/css"/>

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
					<a href="#" class="active">Setting</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Type - <span class="semi-bold">Setting</span></h3>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4><span class="semi-bold">제품</span></h4>
						</div>
						<div class="grid-body no-border product">
							<input type="hidden" name="idx" value="0">
                            <div class="form-inline">
								<div class="col-xs-8">
									<input type="text" class="form-control" name="type" placeholder="Product type">
								</div>
								<div class="col-xs-4 add-group">
									<button type="button" class="btn btn-primary btnAddType">추가</button>
								</div>
								<div class="modify-group hidden">
									<div class="col-xs-2">
										<button type="button" class="btn btn-warning btnUpdateType">수정</button>
									</div>
									<div class="col-xs-2" >
										<button type="button" class="btn btn-danger btnDeleteType">삭제</button>
									</div>
								</div>
							</div>
							<table class="table table-bordered" c="table-striped " id="productTable" style="width:100%">
								<thead>
									<tr>
										<th style="width:100%">제품</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4><span class="semi-bold">질환</span></h4>
						</div>
						<div class="grid-body no-border diseases">
							<input type="hidden" name="idx" value="0">
                            <div class="form-inline">
								<div class="col-xs-8">
									<input type="text" class="form-control" name="type" placeholder="Diseases type">
								</div>
								<div class="col-xs-4 add-group">
									<button type="button" class="btn btn-primary btnAddType">추가</button>
								</div>
								<div class="modify-group hidden">
									<div class="col-xs-2">
										<button type="button" class="btn btn-warning btnUpdateType">수정</button>
									</div>
									<div class="col-xs-2" >
										<button type="button" class="btn btn-danger btnDeleteType">삭제</button>
									</div>
								</div>
							</div>
							<table class="table table-bordered" c="table-striped " id="diseasesTable" style="width:100%">
								<thead>
									<tr>
										<th style="width:100%">질환</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
                <div class="col-md-4">
                    <div class="grid simple">
                        <div class="grid-title no-border">
                            <h4><span class="semi-bold">Sales Skill</span></h4>
                        </div>
                        <div class="grid-body no-border salesSkill">
                            <input type="hidden" name="idx" value="0">
                            <div class="form-inline">
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" name="type" placeholder="Skill type">
                                </div>
                                <div class="col-xs-4 add-group">
                                    <button type="button" class="btn btn-primary btnAddType">추가</button>
                                </div>
                                <div class="modify-group hidden">
                                    <div class="col-xs-2">
                                        <button type="button" class="btn btn-warning btnUpdateType">수정</button>
                                    </div>
                                    <div class="col-xs-2" >
                                        <button type="button" class="btn btn-danger btnDeleteType">삭제</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered" c="table-striped " id="salesSkillTable" style="width:100%">
                                <thead>
                                <tr>
                                    <th style="width:100%">열 제목</th>
                                </tr>
                                </thead>
                            </table>
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
<script src="assets/js/core.js" type="text/javascript"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Select-1.2.6/js/dataTables.select.js"></script>
<script type="text/javascript" src="/plugins/dataTables/dataTables.bootstrap.min.js"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_setting.js?v=2111150001" type="text/javascript"></script>
</body>
</html>