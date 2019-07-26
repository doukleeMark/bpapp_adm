$(document).ready(function() {
	$("#tab-menu").on("click", "a", function(e) {
		e.preventDefault();
		$(this).tab("show");
	});

	$(document).on("click", "#insertBtn", function(e) {
		e.preventDefault();

		if ($("#svf_title").val() == "") {
			alert("타이틀을 입력해주세요.");
			return;
		}

		var formData = new FormData();

		formData.append("actionType", $("#actionType").val());
		formData.append("svf_title", $("#svf_title").val());

		formData.append(
			"svf_visible",
			$("input[name=svf_visible]:radio:checked").val()
		);

		$("#insertBtn").prop("disabled", true);
		$.ajax({
			url: "/page/ajax/a_survey.php",
			data: formData,
			processData: false,
			contentType: false,
			type: "POST",
			success: function(res) {
				res = res.trim();
				if (res != "") {
					location.href = "/?page=survey_info&idx=" + res;
				}
				$("#insertBtn").prop("disabled", false);
			}
		});
	});

	// 항목 추가 버튼
	$("#addSubBtn").on("click", function() {
		var q_cnt = $("#tab-menu li").length;

		if (q_cnt > 9) {
			alert("더이상 생성할 수 없습니다.");
			return;
		}
		$("#tab-menu li").removeClass("active");
		var newTabMenu = $("#tab-menu li")
			.eq(0)
			.clone();
		newTabMenu
			.addClass("active")
			.find("a")
			.attr("href", "#q" + (q_cnt + 1))
			.text("Q" + (q_cnt + 1));
		$("#tab-menu").append(newTabMenu);
		$("#question_group .tab-pane").removeClass("active");
		var newQuestion = $("#question_group .tab-pane")
			.eq(0)
			.clone();
		newQuestion
			.addClass("active")
			.attr("id", "q" + (q_cnt + 1))
			.find("input")
			.val("");
		$("#question_group").append(newQuestion);
	});

	// 항목 삭제 버튼
	$("#delSubBtn").on("click", function() {
		var q_cnt = $("#tab-menu li").length;

		if (q_cnt <= 1) {
			alert("더이상 지울 수 없습니다.");
			return;
		}
		if (
			$("#tab-menu li")
				.eq(q_cnt - 1)
				.hasClass("active")
		)
			$("#tab-menu li")
				.eq(0)
				.addClass("active");
		$("#tab-menu li")
			.eq(q_cnt - 1)
			.remove();
		if (
			$("#question_group .tab-pane")
				.eq(q_cnt - 1)
				.hasClass("active")
		)
			$("#question_group .tab-pane")
				.eq(0)
				.addClass("active");
		$("#question_group .tab-pane")
			.eq(q_cnt - 1)
			.remove();
	});

	$(document).on("click", "#updateBtn", function(e) {
		e.preventDefault();

		var tempArr;
		var formData = new FormData();

		formData.append("actionType", $("#actionType").val());
		formData.append("idx", $("#idx").val());
		formData.append("svf_title", $("#svf_title").val());

		formData.append(
			"svf_visible",
			$("input[name=svf_visible]:radio:checked").val()
		);

		// svs_question
		tempArr = new Array();
		$("input[name=svs_question]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_question", JSON.stringify(tempArr));

		// svs_item_1
		tempArr = new Array();
		$("input[name=svs_item_1]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_item_1", JSON.stringify(tempArr));

		// svs_item_2
		tempArr = new Array();
		$("input[name=svs_item_2]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_item_2", JSON.stringify(tempArr));

		// svs_item_3
		tempArr = new Array();
		$("input[name=svs_item_3]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_item_3", JSON.stringify(tempArr));

		// svs_item_4
		tempArr = new Array();
		$("input[name=svs_item_4]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_item_4", JSON.stringify(tempArr));

		// svs_item_5
		tempArr = new Array();
		$("input[name=svs_item_5]").each(function() {
			tempArr.push($(this).val());
		});
		formData.append("svs_item_5", JSON.stringify(tempArr));

		$("#updateBtn").prop("disabled", true);
		$.ajax({
			url: "/page/ajax/a_survey.php",
			data: formData,
			processData: false,
			contentType: false,
			type: "POST",
			success: function(res) {
				result = res.trim();
				if (result == "success") {
					location.href = "/?page=survey_list";
				}
				$("#updateBtn").prop("disabled", false);
			}
		});
	});
});
