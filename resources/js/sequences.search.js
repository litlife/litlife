let parent = $('.sequences-search-container');

let list = parent.find(".list");

let form = parent.find(".sequence-form");

add_event();

on_list_ready();

function add_event() {

	list.off('click', '.pagination a').on('click', '.pagination a', function (e) {
		e.preventDefault();

		let url = $(this).attr('href');

		load_url(url);
	});

	list.off('click', '.view a').on('click', '.view a', function (e) {
		e.preventDefault();

		list.addClass("loading-cap");

		let url = $(this).attr('href');
		load_url(url);
	});
}

function load_url(url) {

	list.addClass("loading-cap");

	history.pushState(null, null, url);

	$.ajax({
		url: url, data: {'ajax': true}
	}).done(function (data) {
		list.removeClass("loading-cap");
		list.html(data);

		on_list_ready();

		$('html, body').animate({
			scrollTop: list.offset().top - 80
		}, 100);

	}).fail(function () {
		if (jqXHR.status == 401) location.reload();
	});
}

function get_url() {
	return list.find(".pagination [rel=next]").first().attr("href");
}

function on_list_ready() {
	console.log('onAppend');

	window.paginationScrollToActive();
}

form.formChange({
	timeout: 500,
	onShow: function () {

		$(this).ajaxSubmit({
			beforeSubmit: function showRequest(formData, jqForm, options) {

				list.addClass("loading-cap");

				// удаляем пустые параметры

				formData = $.grep(formData, function (item) {
					return item.value != "";
				});

				// добавляем в историю

				history.pushState(null, null, jqForm.attr("action") + "?" + $.param(formData));
				//console.log(jqForm.attr("action") + "?" + queryString);
				return true;
			},
			success: function (responseText, statusText, xhr, $form) {

				list.removeClass("loading-cap");

				list.html(responseText);

				add_event();

				on_list_ready();
			},
			error: function (jqXHR, error, type, $form) {
				if (jqXHR.status == 401) location.reload();
			}
		});


	}
});

