$(document).ready(function(){

	var isMobile = {
		Android: function () {
			return navigator.userAgent.match(/Android/i);
		},
		BlackBerry: function () {
			return navigator.userAgent.match(/BlackBerry/i);
		},
		iOS: function () {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
		Opera: function () {
			return navigator.userAgent.match(/Opera Mini/i);
		},
		Windows: function () {
			return navigator.userAgent.match(/IEMobile/i);
		},
		any: function () {
			return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		}
	};

	var datepicker_object;

	// 켈린더에 이벤트 표시 및 datepicker 플러그인 적용
	getEventDate();

	function getEventDate(){
		
		$.post("/page/ajax/a_calendar.php", {
				actionType: "getEventDate",
				unit: $("#unit").val()
			}, function(eventDate){		
				if(datepicker_object != null){
					datepicker_object.datepicker("destroy");
				}
				datepicker_object = $('#datepicker').datepicker({
					defaultDate: new Date(),
					dateFormat: 'yy-mm-dd', // 데이터는 yyyy-MM-dd로 나옴
					inline: true,
					prevText: "<",
					nextText: ">",
					dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
					onSelect: function (dateText, inst) {
						if(isMobile.iOS()){		
							document.location = "jscall://" + dateText;
						}
						if(isMobile.Android()){
							window.android.callAndroid(dateText);
						}
					},
					beforeShowDay: function (dateStr, inst) {
						// 일자 선택되기전 이벤트 발생
						var addStr = '';
						if ($.inArray($.datepicker.formatDate('yy-mm-dd', dateStr), eventDate.user) >= 0) {
							addStr = 'user_highlighted ';
						}
						if ($.inArray($.datepicker.formatDate('yy-mm-dd', dateStr), eventDate.admin) >= 0) {
							addStr += 'admin_highlighted';
						}
						return [true, addStr, ''];
					},
					onChangeMonthYear: function (year, month, inst) {}
				});
		}, "JSON");
	}
});
