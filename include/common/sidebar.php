<div class="page-sidebar" id="main-menu">
	<div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
		<div class="user-info-wrapper">
			<!--<div class="profile-wrapper">
				<img src="/images/logo/logo3.png"  alt="" data-src="/images/logo/logo3.png" data-src-retina="/images/logo/logo3.png" width="69" height="69" />
			</div>-->
			<div class="user-info">
				<div class="greeting">Welcome</div>
				<div class="username"> <span class="semi-bold"><?=$_SESSION['USER_NAME']?></span> 님</div>
				<div class="status">Status<a href="#">
					<div class="status-icon green"></div>
					Online</a></div>
			</div>
		</div>
		<?php
			$depth = explode("_", $_GET['page']);
		?>
		<ul id="navi">
			<li class="<?=($depth[0] == 'user')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-male"></i> <span class="title">User</span> <span class="arrow <?=($depth[0] == 'user')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'user_list')?'active':'';?>"> <a href="?page=user_list"><span class="title">User List</span></a> </li>
					<?php
						if($_SESSION['USER_LEVEL'] == 10){
					?>
					<li class="<?=($_GET['page'] == 'user_point')?'active':'';?>"> <a href="?page=user_point"><span class="title">Point List</span></a> </li>
					<li class="<?=($_GET['page'] == 'user_pointSetting')?'active':'';?>"> <a href="?page=user_pointSetting"><span class="title">Point Setting</span></a> </li>
					<?php
						}
					?>
				</ul>
			</li>
			<!--<li class="<?=($depth[0] == 'bp')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-star"></i> <span class="title">Best Practice</span> <span class="arrow <?=($depth[0] == 'bp')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'bp_list')?'active':'';?>"> <a href="?page=bp_list"><span class="title">Best Practice</span></a> </li>
				</ul>
			</li>
			<li class="<?=($depth[0] == 'data')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-archive"></i> <span class="title">Data</span> <span class="arrow <?=($depth[0] == 'data')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'data_folder')?'active':'';?>"> <a href="?page=data_folder"><span class="title">Folder</span></a> </li>
					<li class="<?=($_GET['page'] == 'data_list')?'active':'';?>"> <a href="?page=data_list"><span class="title">Data List</span></a> </li>
				</ul>
			</li>
			<li class="<?=($depth[0] == 'board')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-comment-o"></i> <span class="title">Board</span> <span class="arrow <?=($depth[0] == 'board')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'board_list')?'active':'';?>"> <a href="?page=board_list"><span class="title">Notice</span></a> </li>
					<li class="<?=($_GET['page'] == 'board_communityList')?'active':'';?>"> <a href="?page=board_communityList"><span class="title">Community</span></a> </li>
					<li class="<?=($_GET['page'] == 'board_questionList')?'active':'';?>"> <a href="?page=board_questionList"><span class="title">Question</span></a> </li>
				</ul>
			</li>
			<li class="<?=($_GET['page'] == 'calendar_event')?'active':'';?>"> <a href="?page=calendar_event"> <i class="fa fa-calendar"></i>  <span class="title">Calendar</span></a></li>
			<li class="<?=($depth[0] == 'addon')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-plus-circle"></i> <span class="title">Addon</span> <span class="arrow <?=($depth[0] == 'addon')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'addon_mission')?'active':'';?>"> <a href="?page=addon_mission"><span class="title">Mission</span></a> </li>
				</ul>
			</li>
			<li class="<?=($depth[0] == 'survey')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-check-circle-o"></i> <span class="title">Quick Poll</span> <span class="arrow <?=($depth[0] == 'survey')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'survey_list')?'active':'';?>"> <a href="?page=survey_list"><span class="title">Quick Poll List</span></a> </li>
					<li class="<?=($_GET['page'] == 'survey_result')?'active':'';?>"> <a href="?page=survey_result"><span class="title">Quick Poll Result</span></a> </li>
				</ul>
			</li>-->
			<li class="<?=($depth[0] == 'report')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-bar-chart-o"></i> <span class="title">Report</span> <span class="arrow <?=($depth[0] == 'report')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<li class="<?=($_GET['page'] == 'report_login')?'active':'';?>"> <a href="?page=report_login"><span class="title">Login Report</span></a> </li>
					<!--<li class="<?=($_GET['page'] == 'report_market')?'active':'';?>"> <a href="?page=report_market"><span class="title">Market Report</span></a> </li>
					<li class="<?=($_GET['page'] == 'report_data')?'active':'';?>"> <a href="?page=report_data"><span class="title">Data Report</span></a> </li>-->
				</ul>
			</li>
			<?php
				if($_SESSION['USER_LEVEL'] >= 3){
			?>
			<li class="<?=($depth[0] == 'el')?'active open':'';?>"> <a href="javascript:;"> <i class="fa fa-desktop"></i> <span class="title">E-learning</span> <span class="arrow <?=($depth[0] == 'el')?'open':'';?>"></span> </a>
				<ul class="sub-menu">
					<?php
						if($_SESSION['USER_LEVEL'] >= 3){
					?>
					<li class="<?=($_GET['page'] == 'el_setting')?'active':'';?>"> <a href="?page=el_setting"><span class="title">Type Setting</span></a> </li>
					<?php
						}
						if($_SESSION['USER_LEVEL'] >= 3){
					?>
					<li class="<?=($_GET['page'] == 'el_contentList')?'active':'';?>"> <a href="?page=el_contentList"><span class="title">Contents</span></a> </li>
					<?php
						}
						if($_SESSION['USER_LEVEL'] >= 9){
					?>
					<li class="<?=($_GET['page'] == 'el_courseList')?'active':'';?>"> <a href="?page=el_courseList"><span class="title">Course</span></a> </li>
					<li class="<?=($_GET['page'] == 'el_contentReport')?'active':'';?>"> <a href="?page=el_contentReport"><span class="title">Report</span></a> </li>
					<?php
						}
					?>
					<?php
						if($_SESSION['USER_LEVEL'] == 10){
					?>
					<li class="<?=($_GET['page'] == 'el_courseClosedList')?'active':'';?>"> <a href="?page=el_courseClosedList"><span class="title">Course Closed List</span></a> </li>
					<?php
						}
					?>
				</ul>
			</li>
			<?php
				}
			?>
		</ul>
	</div>
</div>
<div class="footer-widget">
	<div class=""><a href="#" class="btn_logout"><i class="fa fa-power-off"></i></a></div>
</div>