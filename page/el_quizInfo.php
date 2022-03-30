<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/quiz.class.lib");
	$quizClass = new QuizClass();
	
	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit";

		$quizRes = $quizClass->getQuizBankInfo($_GET['idx']);
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
<link href="/css/el_quizInfo.css" rel="stylesheet" type="text/css"/>

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
					<a href="#" class="active">Quiz Bank</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Quiz Bank - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
						<form id="form" action="/page/el_quizInfo_proc.php" method="post" autocomplete="off">	
								<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
								<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<label class="form-label">Search Tag</label>
											<div class="controls">
												<input name="cq_tag" id="cq_tag" type="text"  class="form-control" placeholder="Tag" value="<?=$quizRes['cq_tag']?>">
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="form-group">
											<label class="form-label">Question</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<textarea name="cq_question" id="cq_question" rows="3" style="width:100%"><?=$quizRes['cq_question']?></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12">
										<div class="controls items">
											<?php
												for($i=1 ; $i <= 4; $i++){
													echo "<div class='answer-group'>";
													echo "<label>$i.</label>";
													echo "<input name='cq_item_$i' type='text'  class='form-control' placeholder='' value='".$quizRes['cq_item_'.$i]."' >";
													echo "</div>";
												}
											?>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-xs-6">
										<div class="form-group">
											<label class="form-label">Answer</label>
											<select name="cq_answer" id="cq_answer" class="select2 form-control">
													<?php
														for($i=1; $i<=4; $i++) {
															if((int)$quizRes['cq_answer']==$i) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$i."\" " . $selectedChar . " >".$i."번</option>";
														}
													?>
											</select>
										</div>
									</div>
								</div>

								<div class="actions">
									<div class="pull-right">
										<button class="btn btn-primary btn-cons" id="btn_submit" type="button">Submit</button>
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
<script src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_quizInfo.js" type="text/javascript"></script> 
</body>
</html>