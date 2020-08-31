var form = $('.keywords-form').first();

form.find(".keywords").select2({
	width: 'style',
	placeholder: $(this).data('placeholder'),
	tags: true,
	tokenSeparators: [','],
	maximumSelectionLength: 0,
	ajax: {
		url: "/keywords/search",
		dataType: 'json',
		delay: 100,
		data: function (params) {

			var query = {
				q: params.term,
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

			var arr = [];

			for (var i = 0; i < data.data.length; i++) {
				arr[i] = data.data[i];
				arr[i]['id'] = arr[i]['text'];
			}

			console.log(arr);

			return {
				results: arr,
				pagination: {
					more: (data.next_page_url) ? true : false
				}
			};
		},
		cache: true
	},
	escapeMarkup: function (markup) {
		return markup;
	}, // let our custom formatter work
	templateResult: function formatRepo(repo) {

		console.log('templateResult');

		if (repo.loading) return repo.text;

		console.log(repo);

		var markup = "";

		if (repo.text) {
			markup += "<div >";
			markup += repo.text;
			markup += " </div>";
		} else {
			markup += "<div >";
			markup += repo.keyword.text;
			markup += " </div>";
		}
		return markup;
	},
	templateSelection: function formatRepoSelection(repo) {

		console.log('templateSelection');

		if (repo.keyword) {
			return repo.keyword.text;
		} else {
			return repo.text;
		}

	}

});

$('.keyword').each(function () {

	var keyword = $(this);

	var buttons = $(this).find('.buttons');

	keyword.find('.delete').click(function () {

		var button = $(this);

		button.button('loading');

		$.ajax({
			method: "DELETE",
			url: "/books/" + keyword.data('book-id') + "/keywords/" + keyword.data('id') + ""
		}).done(function (result) {
			buttons.remove();
			button.button('reset');

			keyword.addClass('transparency');
		});
	});

	keyword.find('.approve').click(function () {

		var button = $(this);

		button.button('loading');

		$.ajax({
			method: "GET",
			url: "/books/" + keyword.data('book-id') + "/keywords/" + keyword.data('id') + "/approve"
		}).done(function (result) {
			buttons.remove();
			button.button('reset');
		});

	});
});
