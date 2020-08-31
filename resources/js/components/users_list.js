export default function users_list(parent) {

	var users = parent;

	var list = users.find(".list");

	var form = users.find("form");

	add_event();

	function add_event() {
		list.off('click', '.pagination a').on('click', '.pagination a', function (e) {
			e.preventDefault();

			list.addClass("loading-cap");

			let url = $(this).attr('href');

			window.history.pushState("", "", url);

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
		});
	}

	function get_url() {
		return list.find(".pagination [rel=next]").first().attr("href");
	}

	function on_list_ready() {
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

					history.pushState('', '', jqForm.attr("action") + "?" + $.param(formData));
					//console.log(jqForm.attr("action") + "?" + queryString);
					return true;
				},
				success: function (responseText, statusText, xhr, $form) {

					list.removeClass("loading-cap");

					list.html(responseText);

					add_event();
				},
				error: function (jqXHR, error, type, $form) {
					if (jqXHR.status == 401) location.reload();
				}
			});
		}
	});


	/*
	 form.find("select").change(function() {
	 console.log('change');
	 });

	 form.find("input, textarea").keyup(function() {
	 console.log('change');
	 });
	 */
}




