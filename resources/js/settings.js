$(".forums").select2({
	width: 'style',
	tags: true,
	ajax: {
		url: "/forums/search",
		dataType: 'json',
		delay: 100,
		data: function (params) {
			return {
				q: params.term
			};
		},
		processResults: function (data, params) {
			// parse the results into the format expected by Select2
			// since we are using custom formatting functions we do not need to
			// alter the remote JSON data, except to indicate that infinite
			// scrolling can be used
			params.page = params.page || 1;

			return {
				results: data.items,
				pagination: {
					more: (params.page * 30) < data.total_count
				}
			};
		},
		cache: true
	},
	tokenSeparators: [',', ' '],
	escapeMarkup: function (markup) {
		return markup;
	},
	minimumInputLength: 1,
	templateResult: function formatRepo(repo) {

		console.log('templateResult');
		if (repo.loading) return repo.id;

		console.log(repo);

		var markup = "<div >" + repo.name + " ID: " + repo.id + "</div>";

		return markup;
	},
	createTag: function (params) {
		return undefined;
	},
	templateSelection: function formatRepoSelection(repo) {

		console.log('templateSelection');

		console.log(repo);

		var s = repo.name || repo.text;

		return '' + s + ' ID: ' + repo.id;
	},
});

$(".genres_books_comments_hide_from_home_page").select2({
	width: 'style',
	placeholder: $(this).data('placeholder'),
	ajax: {
		url: "/genres/search?limit=100",
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
	}, // let our custom formatter work
	minimumInputLength: 0,
	templateResult: function formatRepo(repo) {

		console.log('templateResult');
		console.log(repo);

		if (repo.loading) return repo.text;

		var markup = "";

		markup += "<div >";
		markup += ' ';
		markup += repo.name;
		markup += "</div>";

		return markup;
	},
	templateSelection: function formatRepoSelection(repo) {
		return repo.name || repo.text;
	}
});