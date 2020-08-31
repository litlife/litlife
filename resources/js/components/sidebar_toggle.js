$("[data-target='#sidebar']").click(function () {

	var sidebar = $('#sidebar');
	var main = $('#main');
	var footer = $('#footer');

	if (sidebar.is(':visible')) {
		console.log('hide');

		sidebar.removeClass('d-sm-block').addClass('d-none');
		main.removeClass('pl-260px');
		footer.removeClass('pl-260px');

		$.ajax({
			method: "GET",
			url: '/sidebar/hide'
		}).done(function (msg) {

		});
	} else {
		console.log('show');

		sidebar.addClass('d-sm-block').removeClass('d-none');
		main.addClass('pl-260px');
		footer.addClass('pl-260px');

		$.ajax({
			method: "GET",
			url: '/sidebar/show'
		}).done(function (msg) {

		});
	}
});
