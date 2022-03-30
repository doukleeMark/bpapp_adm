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
<link href="/plugins/easytree/ui.easytree.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/data_folder.css" rel="stylesheet" type="text/css"/>

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
					<a href="#" class="active">Folder</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Data - <span class="semi-bold">Folder</span></h3>
			</div>			
			<div class="row">
				<div class="col-md-6">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4><span class="semi-bold">Folder Tree</span></h4>
						</div>
						<div class="grid-body no-border tree">
							<div id="folder_tree"></div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4><span class="semi-bold">Folder Info</span></h4>
						</div>
						<div class="grid-body no-border">
						<form id="form">
							<div class="form-group">
								<label class="form-label">Target Folder</label>
								<select name="targetFolder" id="targetFolder" class="select2 form-control">
									
								</select>
							</div>							
							<div class="form-group">
								<label class="form-label">Folder name</label>
								<div class="input-with-icon right">  
									<i class=""></i>
									<input type="text" name="folderName" id="folderName" class="form-control">
								</div>
								<span class="help">&nbsp;</span>
							</div>
							<div class="form-group">
								<label class="form-label">Display Order</label>
								<input type="text" name="displayOrder" id="displayOrder" class="form-control">
							</div>
							<div class="form-actions">
								<div class="pull-left">
									<button type="button" class="btn btn-danger" id="folder_delete">Delete</button>
								</div>
								<div class="pull-right">
									<button type="button" class="btn btn-primary" id="folder_edit">Edit</button>
									<button type="button" class="btn btn-success" id="folder_add">Add</button>
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
<script src="/js/data_folder.js" type="text/javascript"></script> 
</body>
</html>