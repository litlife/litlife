export default function authors_list(parent) {

	let authors = parent;

	let list = parent.find(".list");

	let form = parent.find("#author-form");

	let selected_authors = parent.find("#selected-authors").select2();

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

		selected_authors.on('select2:unselect', function (e) {
			var data = e.params.data;
			list.find('.author[data-id=' + data.id + ']').find('[type=checkbox]').prop('checked', false);
		});

		selected_authors.on('select2:select', function (e) {
			var data = e.params.data;
			list.find('.author[data-id=' + data.id + ']').find('[type=checkbox]').prop('checked', true);
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

		list.find('.author').each(function () {

			var author = $(this);

			var id = author.data('id');

			var name = author.find('.name').text();

			author.find('[type=checkbox]').unbind('click').click(function () {

				if ($(this).prop('checked')) {
					var option = $('<option value="' + id + '" selected="selected">' + name + '</option>');
					selected_authors.append(option);
				} else {
					selected_authors.find('option[value=' + id + ']').remove();
				}

				selected_authors.trigger("change");
			});

			if (selected_authors.find('option[value=' + id + ']:selected').length) {
				author.find('[type=checkbox]').prop('checked', true);
			}
		});

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


	/*
	 form.find("select").change(function() {
	 console.log('change');
	 });

	 form.find("input, textarea").keyup(function() {
	 console.log('change');
	 });
	 */

}




