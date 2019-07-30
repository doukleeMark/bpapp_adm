$(document).ready(function () {

	$('.select2').select2({ minimumResultsForSearch: -1, placeholder: "선택안함" });

	// sliders
	var sliders = new Array();

	// file upload
	$('.file-upload').each(function (e) {
		console.log($(this).attr('action'));
		$(this).fileupload({
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			datatype: 'xml',
			dropZone: $(this).find('.dropinput'),
			add: function (event, data) {
				window.onbeforeunload = function () {
					return 'You have unsaved changes.';
				};

				var file = data.files[0];
				var maxFileSize = $(this)
					.find('.file_size')
					.val();

				var fileExt = file.name.split('.').pop();
				var checkExt;
				var target = $(this).find("input[name='ct_s3_file']");
				if (target.length) {
					checkExt = isContent;
				} else {
					checkExt = isImage;
				}

				if (!checkExt(fileExt)) {
					window.onbeforeunload = null;
					alert('등록가능한 파일이 아닙니다.');
					return false;
				}

				// 사이즈체크
				if (!fileSizeCheck(file, maxFileSize)) {
					window.onbeforeunload = null;
					alert('첨부파일 사이즈는 ' + maxFileSize + 'MB 이내로 등록 가능합니다.');
					return false;
				}

				data.submit();

				var bar = $('<div class="progress" data-mod="' + file.size + '"><div class="bar"></div></div>');
				$(this)
					.find('.progress-bar-area')
					.html(bar);
				bar.slideDown('fast');
			},
			progress: function (e, data) {
				var percent = Math.round((data.loaded / data.total) * 100);
				$(this)
					.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
					.css('width', percent + '%')
					.html(percent + '%');
			},
			fail: function (e, data) {
				window.onbeforeunload = null;
				$(this)
					.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
					.css('width', '100%')
					.addClass('red')
					.html('');
			},
			done: function (event, data) {
				window.onbeforeunload = null;

				$(this).find('.progress-bar-area .progress').slideUp('fast');
				$(this).find('.progress-bar-area').html('');

				if (data.result) {

					var original = data.files[0];
					var s3Result = JSON.parse(data.result);

					$(this).find('.fileInput').addClass('hidden');
					$(this).find('.fileDel').removeClass('hidden');

					$(this).find('.link-filename').attr('href', s3Result['tmpUrl']).text(original.name);
					$(this).find('.file_idx').val(s3Result['s3Idx']);
					$(this).find('img').attr('src', s3Result['tmpUrl']);

					if (parseInt(s3Result['playSec']) > 0) {
						$('#playTime').val(parseInt(s3Result['playSec']));

						$.each(sliders, function (index, item) {
							item.bootstrapSlider('setAttribute', 'max', parseInt(s3Result['playSec']))
								.bootstrapSlider({ formatter: sliderTimeFormat })
								.bootstrapSlider('refresh');
						});
					}
				} else {
					alert('지원하지 않는 파일입니다.');
				}
			}
		});
	});

	var d_fileIdxs = [];

	$('.btn-delfile').on('click', function () {
		var fileGroup = $(this).closest('.file-group');

		// 삭제파일 리스트
		d_fileIdxs.push(fileGroup.find('.file_idx').val());

		fileGroup.find('.fileDel').addClass('hidden');
		fileGroup.find('.fileInput').removeClass('hidden');
		fileGroup.find('.file_idx').val('0');

		// content file 삭제시 깜짝퀴즈 range 설정 초기 및 비활성화
		if (fileGroup.find("input[name='ct_s3_file']").length > 0) {
			$('#playTime').val(0);
			$.each(sliders, function (index, item) {
				item.bootstrapSlider('setAttribute', 'max', 0)
					.bootstrapSlider({ formatter: sliderTimeFormat })
					.bootstrapSlider('refresh');
			});
		}
	});

	$('.btnSubmit').on('click', function () {
		if (!$('input[name=ct_title]').val().length) {
			$('input[name=ct_title]').focus();
			alert('타이틀명을 입력해주세요.');
			return;
		}

		const s3_file = $("input[name='ct_s3_file']").val();
		if (!(parseInt(s3_file) > 0)) {
			alert('컨텐츠 파일을 추가해주세요.');
			return;
		}

		var formData = new FormData();

		formData.append('actionType', $("input[name='actionType']").val());
		formData.append('idx', $("input[name='idx']").val());

		formData.append('ct_code_pd', arrayToString($("#code_pd :selected")));
		formData.append('ct_code_di', arrayToString($("#code_di :selected")));
		formData.append('ct_code_gd', arrayToString($("#code_gd :selected")));
		formData.append('ct_code_lv', arrayToString($("#code_lv :selected")));

		formData.append('ct_title', $("input[name='ct_title']").val());
		formData.append('ct_speaker', $("input[name='ct_speaker']").val());
		formData.append('ct_desc', $("textarea[name='ct_desc']").val());
		formData.append('ct_type', $("input[name='ct_type']:checked").val());
		formData.append('ct_s3_file', $("input[name='ct_s3_file']").val());
		formData.append('ct_s3_thumb', $("input[name='ct_s3_thumb']").val());

		formData.append('d_file', d_fileIdxs.join());

		if ($("input[name='actionType']").val() == 'update') {
			if ($('input[name="quizIdx[]"]').length) {
				var chk = false;
				$('textarea[name="cs_question[]"]').each(function (index, item) {
					if (!$(item).val().length) {
						chk = true;
						return false;
					}
				});
				if (chk) {
					alert('문제를 입력해주세요.');
					return;
				}
				$('input[name*="cs_item_"]').each(function (index, item) {
					if (!$(item).val().length) {
						chk = true;
						return false;
					}
				});
				if (chk) {
					alert('문제항목을 입력해주세요.');
					return;
				}
			}

			formData.append('ct_test_count', $("input[name='ct_test_count']").val());
			formData.append('del_quizs', del_quizs.join(','));

			$("input[name='quizIdx[]']").each(function () { formData.append("quizIdx[]", $(this).val()); });
			$("textarea[name='cs_question[]']").each(function () { formData.append("cs_question[]", $(this).val()); });
			$("input[name='cs_item_1[]']").each(function () { formData.append("cs_item_1[]", $(this).val()); });
			$("input[name='cs_item_2[]']").each(function () { formData.append("cs_item_2[]", $(this).val()); });
			$("input[name='cs_item_3[]']").each(function () { formData.append("cs_item_3[]", $(this).val()); });
			$("input[name='cs_item_4[]']").each(function () { formData.append("cs_item_4[]", $(this).val()); });
			$("select[name='cs_answer[]']").each(function () { formData.append("cs_answer[]", $('option:selected', this).attr('value')); });
			$("input[name='cs_on[]']").each(function () { formData.append("cs_on[]", $(this).val()); });
			$("input[name='range[]']").each(function () { formData.append("range[]", $(this).val()); });
		}

		$('.btnSubmit').prop('disabled', true);
		$.ajax({
			url: '/page/ajax/a_contentInfo.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function (res) {
				window.onbeforeunload = null;

				$('.btnSubmit').prop('disabled', false);
				if ($("input[name='actionType']").val() == 'insert') {
					location.href = '/?page=el_contentInfo&idx=' + res;
				} else {
					location.href = '/?page=el_contentList';
				}
			}
		});
	});

	if ($('#actionType').val() == 'update') {

		$('#question_group').on('change', '.cs_on', function () {
			$(this)
				.prev()
				.val($(this).attr('checked') ? '1' : '0');
		});

		$('input.slider-element').each(function () {
			sliders.push($(this).bootstrapSlider({ formatter: sliderTimeFormat }));
		});
		$('input.slider-element').bootstrapSlider({ formatter: sliderTimeFormat });

		$('#tab-menu').on('click', 'a', function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		// 항목 인덱스
		var q_no = $('#tab-menu li').length;
		var del_quizs = [];

		// 항목 추가 버튼
		$('#btnAddQuiz').on('click', function () {
			const cnt = $('#tab-menu li').length;

			if (cnt > 4) {
				alert('더이상 생성할 수 없습니다.');
				return;
			}
			window.onbeforeunload = function () {
				return 'You have unsaved changes.';
			};
			var no = 0;

			if (cnt) {
				no = parseInt(
					$('#tab-menu li')
						.last()
						.attr('no')
				);
				$('#tab-menu li').removeClass('active');
			}

			$('#tab-menu').append(
				"<li class='active' no='" + (no + 1) + "'><a href='#q" + (q_no + 1) + "'>Q" + (no + 1) + '</a></li>'
			);

			$.ajax({
				type: 'POST',
				url: '/page/add/add_supriseQuiz.php',
				dataType: 'html',
				data: { no: q_no, playSec: $('#playTime').val() },
				success: function (data) {
					if (!cnt) {
						$('.supriseQuiz-blank').addClass('hidden');
					}
					$('#question_group .tab-pane').removeClass('active');
					$('#question_group').append(data);
					$('#question_group .tab-pane.active .select2').select2({
						minimumResultsForSearch: -1
					});
					sliders.push(
						$('#question_group .tab-pane.active .slider-element').bootstrapSlider({
							formatter: sliderTimeFormat
						})
					);
				}
			});
			q_no++;
		});

		// 항목 삭제 버튼
		$('#question_group').on('click', '.btnDelQuiz', function () {
			var cnt = $('#tab-menu li').length;

			if (cnt < 1) return;
			else if (cnt == 1) {
				$('.supriseQuiz-blank').removeClass('hidden');
			}
			window.onbeforeunload = function () {
				return 'You have unsaved changes.';
			};

			const $target_tab = $(this).closest('.tab-pane');
			var quizIdx = $target_tab.find('.quizIdx').val();

			if (quizIdx > 0) {
				del_quizs.push(quizIdx);
			}

			// tab-menu
			const $li = $('#tab-menu li.active');
			var num = $li.attr('no');

			sliders.splice(parseInt(num) - 1, 1);

			if ($li.next().length) {
				$li.next().addClass('active');

				var $next = $li;
				while ($next.next().length) {
					$next.next().attr('no', num);
					$next
						.next()
						.find('a')
						.text('Q' + num);
					$next = $next.next();
					num++;
				}
			} else if ($li.prev().length) {
				$li.prev().addClass('active');
			}

			$li.remove();

			// tab-content
			const $tab = $('#question_group .tab-pane.active');

			if ($tab.next().length) {
				$tab.next().addClass('active');
			} else if ($tab.prev().length) {
				$tab.prev().addClass('active');
			}

			$tab.remove();
		});

		// Show Test 변경 확인
		$('#ct_test_count').on('change', function () {
			window.onbeforeunload = function () {
				return 'You have unsaved changes.';
			};
		});

		// Test Table
		const testTable = $('#testTable').DataTable({
			dom: "<<'pull-left'l><'pull-right'f>>rt<'bottom'<'pull-right'p><'pull-left'B>i>",
			lengthMenu: [[10, 25, 50, 100, -1], ['10', '25', '50', '100', 'All']],
			buttons: [
				{
					text: 'Remove',
					className: 'btn btn-danger',
					action: function (e, dt, node, config) {
						const rows = dt.rows({ selected: true }).data();

						if (rows.length < 1) return;

						if (confirm("삭제하시겠습니까?")) {
							var idxs = '';
							rows.each(function (item) {
								idxs += item.idx + ',';
							});
							idxs = idxs.substring(0, idxs.length - 1);

							$.post(
								'/page/ajax/a_contentQuiz.php',
								{
									actionType: 'delTest',
									contentIdx: $('#idx').val(),
									idxs: idxs
								},
								function (d) {
									dt.ajax.reload();
									reset_form();
								}
							);
						}
					}
				}
			],
			language: {
				lengthMenu: '_MENU_',
				zeroRecords: '문제를 추가해주세요.',
				info: '',
				infoEmpty: '',
				infoFiltered: ''
			},
			pagingType: 'numbers',
			serverSide: true,
			processing: true,
			scrollX: false,
			ajax: {
				type: 'POST',
				url: '/page/ajax/a_getContentTestList.php',
				data: {
					content_idx: $('#idx').val()
				}
			},
			select: {
				style: 'os'
			},
			columns: [{ data: 'idx' }, { data: 'cq_question' }],
			order: [[0, 'desc']],
			columnDefs: [
				{
					targets: 0,
					visible: false
				}
			],
			drawCallback: function (settings) {
				const cnt = this.api().page.info().recordsTotal;
				$('.testCount').text(cnt);
				var $number = $('input[type="text"].bfh-number');
				$number.data('bfhnumber').options['max'] = cnt;

				if ($number.val() > cnt) $number.val(cnt);
			}
		});

		var select_test_idx = 0;

		// 제품 선택
		testTable.on('select', function (e, dt, type, indexes) {
			if (type === 'row') {
				if (testTable.rows('.selected').data().length == 1) {
					select_test_idx = testTable.rows('.selected').data()[0].idx;
				} else {
					select_test_idx = 0;
				}
				set_form(select_test_idx);
			}
		});

		// 제품 선택 해제
		testTable.on('deselect', function (e, dt, type, indexes) {
			if (type === 'row') {
				if (testTable.rows('.selected').data().length == 1) {
					select_test_idx = testTable.rows('.selected').data()[0].idx;
				} else {
					select_test_idx = 0;
				}
				set_form(select_test_idx);
			}
		});

		$('.dataTables_length select').select2({
			minimumResultsForSearch: -1
		});
		$('.select2-container').removeClass('form-control input-sm');

		// Add/update Test
		$("#quiz_group").on("click", ".btn-add-update", function (e) {

			const question = $("textarea[name='cq_question']").val();
			if (!question.length) {
				alert('문제를 입력해주세요.');
				$("textarea[name='cq_question']").focus();
				return;
			}

			const item_1 = $("input[name='cq_item_1']").val();
			if (!item_1.length) {
				alert('보기를 입력해주세요.');
				return;
			}

			const item_2 = $("input[name='cq_item_2']").val();
			if (!item_2.length) {
				alert('보기를 입력해주세요.');
				return;
			}

			const item_3 = $("input[name='cq_item_3']").val();
			if (!item_3.length) {
				alert('보기를 입력해주세요.');
				return;
			}

			const item_4 = $("input[name='cq_item_4']").val();
			if (!item_4.length) {
				alert('보기를 입력해주세요.');
				return;
			}

			$.post(
				'/page/ajax/a_contentQuiz.php',
				{
					actionType: 'add_update',
					contentIdx: $('#idx').val(),
					idx: select_test_idx,
					cq_question: question,
					cq_item_1: item_1,
					cq_item_2: item_2,
					cq_item_3: item_3,
					cq_item_4: item_4,
					cq_answer: $("select[name='cq_answer']").val()
				},
				function (res) {
					if (res.result == 'success') {
						testTable.ajax.reload();
						reset_form();
					}
				},
				"json"
			);
		});

		// excel upload
		$("#test_upload").on("change", "input[type=file]", function () {
			var input = $(this).closest('.input-group').find(':text');
			var label = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
			input.val(label);
		});

		$("#uploadButton").on('click', function () {
			if ($("input[name=testListFile]")[0].files[0] == null) {
				alert("엑셀파일을 선택해주세요.");
				return;
			}
			var formData = new FormData();
			formData.append("testListFile", $("input[name=testListFile]")[0].files[0]);
			formData.append("contentIdx", $('#idx').val());

			$("#uploadButton").prop('disabled', true);
			$.ajax({
				url: "/page/ajax/a_testUpload.php",
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST',
				dataType: 'json',
				success: function (res) {
					if (res.result == 'success') {
						testTable.ajax.reload();
						alert("문제가 추가되었습니다. (" + res.total + "개 중 " + res.count + "개)");
						$("#test_upload input[type=file]").val('');
						$("#test_upload input[type=text]").val('');
					} else {
						alert("처리에 실패했습니다.");
						return;
					}
				},
				complete: function (res) {
					$("#uploadButton").prop('disabled', false);
				}
			});
		});
	}
});

var generateRandom = function (min, max) {
	var ranNum = Math.floor(Math.random() * (max - min + 1)) + min;
	return ranNum;
};

var createFileName = function () {
	var d = new Date();

	var res = d.getFullYear();
	res += ('0' + (d.getMonth() + 1)).slice(-2);
	res += ('0' + d.getDate()).slice(-2);
	res += ('0' + d.getHours()).slice(-2);
	res += ('0' + d.getMinutes()).slice(-2);
	res += ('0' + d.getSeconds()).slice(-2);
	res += generateRandom(100, 999);
	return res;
};

var fileSizeCheck = function (file, max) {
	var maxSize = max * 1024 * 1024;
	var fileSize = 0;

	// 브라우저 확인
	var browser = navigator.appName;

	// 익스플로러일 경우
	if (browser == 'Microsoft Internet Explorer') {
		var oas = new ActiveXObject('Scripting.FileSystemObject');
		fileSize = oas.getFile(file.value).size;
	}
	// 익스플로러가 아닐경우
	else {
		fileSize = file.size;
	}

	return fileSize < maxSize;
};

var isImage = function (ext) {
	switch (ext.toLowerCase()) {
		case 'jpg':
		case 'jpeg':
		case 'png':
			return true;
	}
	return false;
};

var isContent = function (ext) {
	switch (ext.toLowerCase()) {
		case 'mp4':
		case 'mp3':
			return true;
	}
	return false;
};

var sliderTimeFormat = function (value) {
	if (value.length == 2) {
		var str = '';
		if (value[0] > value[1]) {
			str = secondToTime(value[1]) + ' : ' + secondToTime(value[0]);
		} else {
			str = secondToTime(value[0]) + ' : ' + secondToTime(value[1]);
		}
	}
	return str;
};

var arrayToString = function ($select) {

	var res = "";
	$select.each(function () {
		res += "X" + $(this).val() + ",";
	});

	return res;
}

var set_form = function (_idx) {
	if (_idx > 0) {
		$.ajax({
			url: '/page/add/add_quiz.php',
			type: 'POST',
			data: {
				idx: _idx
			},
			dataType: "html",
			success: function (res) {
				$("#quiz_group").html(res);
				$('#cq_answer').select2({ minimumResultsForSearch: -1 });
			}
		});
	} else {
		reset_form();
	}
};

var reset_form = function () {
	$("textarea[name='cq_question']").val("");
	$("input[name='cq_item_1']").val("");
	$("input[name='cq_item_2']").val("");
	$("input[name='cq_item_3']").val("");
	$("input[name='cq_item_4']").val("");
	$("select[name='cq_answer']").val(1).trigger('change');
	$("#quiz_group button").removeClass("btn-warning").addClass("btn-success");
	$("#quiz_group button i").addClass("fa-plus").text(" Add Test");
};
