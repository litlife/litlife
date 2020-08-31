import item from "./comment/item";

export default function comments_list(container) {

	// контейнер, который содержит список элементов
	let list = container.find(".list");
	// форма, которая должна будет использоваться для поиска
	let form = container.find("form");

	// обозначение пагинации
	define_infinity_pagination();
	// событие срабытывает, когда список элементов готов
	on_list_ready();

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

					return true;
				},
				success: function (responseText, statusText, xhr, $form) {

					list.removeClass("loading-cap");

					list.html(responseText);

					define_infinity_pagination();

					on_list_ready();
				},
				error: function (jqXHR, error, type, $form) {
					if (jqXHR.status == 401) location.reload();
				}
			});
		}
	});

	function on_list_ready() {
		window.paginationScrollToActive();

		list.find(".item").each(function () {
			item($(this));
		});
	}

	function define_infinity_pagination() {
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


}








