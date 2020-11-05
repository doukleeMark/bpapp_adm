<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID'] || $_SESSION['USER_LEVEL'] < 9){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
    // 201103 추가 - 이도욱
	include_once(CLASS_PATH . "/el.class.lib");
	$elClass = new ELClass();
    $courseRes = $elClass->getCourseThreadInfo($_GET['idx']);
    //
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
<link href="/css/el_courseResult.css" rel="stylesheet" type="text/css"/>
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
					<a href="#" class="active">Course</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Course - <span class="semi-bold">Result</span></h3>
			</div>
			<!--// 201103 추가 - 이도욱-->
			<div class="row">
                <div class="col-xs-12" >
                    <div class="grid simple">
                        <div class="grid-title no-border"></div>
                        <div class="grid-body no-border">
                            <input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
                            <table class="table table-hover table-condensed" id="courseInfoTable">
                                <thead>
                                    <tr role="row">
                                        <th style="width:10%"><nobr>과정명</nobr></th>
                                        <td style="width:90%" colspan="3"><nobr><?=$courseRes['co_title']?></nobr></th>
                                    </tr>
                                    <tr role="row" >
                                        <th style="width:10%"><nobr>총인원</nobr></th>
                                        <td style="width:40%"><nobr><?=$courseRes['cnt']?></nobr></th>
                                        <th style="width:10%"><nobr>기간(상태)</nobr></th>
                                        <td style="width:40%">
                                        <nobr>
                                            <?=$courseRes['co_start']?>~<?=$courseRes['co_end']?>
                                            (
                                                 <?
                                                    if($courseRes['co_status'] == -2){
                                                        echo "<span class='label bg-cons bg-dark'>DONE</span>";
                                                    } else if ($courseRes['co_status'] == -1){
                                                        echo "<span class='label bg-cons bg-blue'>INFINITE</span>";
                                                    } else if ($courseRes['co_status'] == 0){
                                                        echo "<span class='label bg-cons bg-red'>D-day</span>";
                                                    } else {
                                                        echo "<span class='label bg-cons bg-red'>D- ".$courseRes['co_status']." </span>";
                                                    }
                                                 ?>
                                            )
                                            </nobr>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
							<table class="table table-hover table-condensed" id="listTable">
								<thead>
									<tr>
										<th style="width:1%"><nobr>번호</nobr></th>
										<th style="width:10%"><nobr>그룹명</nobr></th>
										<th style="width:10%"><nobr>팀명</nobr></th>
										<th style="width:10%"><nobr>이메일</nobr></th>
										<th style="width:10%"><nobr>이름</nobr></th>
										<th style="width:5%"><nobr>이수여부</nobr></th>
										<th style="width:5%"><nobr>점수</nobr></th>
										<th style="width:5%"><nobr>테스트</nobr></th>
										<th style="width:5%"><nobr></nobr></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>						
					</div>
					<div class="pull-left">
						<button class="btn btn-primary" id="btn-excel" type="button">Excel Download</button>
					</div>
					<div class="pull-right">
						<button class="btn btn-danger" id="btn-all-reset" type="button">전체 초기화</button>
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

<script src="js/plugins/jquery.alphanum.js" type="text/javascript"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_courseResult.js?v=20110501" type="text/javascript"></script>
</body>
</html>