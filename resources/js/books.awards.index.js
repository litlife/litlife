var form = $('.awards-form').first();

form.find("[name=award]").select2({
	width: 'style',
	ajax: {
		url: "/awards",
		dataType: 'json',
		delay: 500,
		data: function (params) {

			var query = {
				search: params.term,
				page: params.page || 1
			};

			// Query parameters will be ?search=[term]&page=[page]
			return query;
		},
		processResults: function (data, params) {
			// parse the results into the format expected by Select2
			// since we are using custom formatting functions we do not need to
			// alter the remote JSON data, except to indicate that infinite
			// scrolling can be used
			params.page = params.page || 1;

			return {
				results: data.data,
				pagination: {
					more: (data.next_page_url) ? true : false
				}
			};
		},
		cache: true
	},
	escapeMarkup: function (markup) {

		return markup;
	},
	templateResult: function formatRepo(repo) {
		if (repo.loading) return repo.text;

		var markup = "<div >" + repo.title + "</div>";

		return markup;
	},
	templateSelection: function formatRepoSelection(repo) {
		return repo.title || repo.text;
	}
});

$('.awards').find('.item').each(function () {

	let i = $(this);

	let buttons = $(this).find('.buttons');

	let id = i.data("id");
	let award_id = i.data("award-id");
	let book_id = i.data("book-id");
	let title = i.find(".title");
	let description = i.data(".description");

	let $btn_delete = i.find('.delete');

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	let $btn_restore = i.find('.restore');

	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/books/" + book_id + "/awards/" + award_id
		}).done(function (msg) {

			if (msg.deleted_at) {
				i.hide();
				$btn_delete.hide();
				//$btn_restore.show();
				//i.find('img').addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				//i.find('img').removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}
});
