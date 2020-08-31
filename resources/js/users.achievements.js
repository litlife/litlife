import item from "./components/achievement/item";

$(".achievements").find('.item').each(function () {
	item($(this));
});

$('.select').select2({
	dropdownAutoWidth: true,
	width: 'style',
	ajax: {
		url: "/achievements_search",
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
	// отображение в выпадающем меню
	templateResult: function formatRepo(repo) {

		console.log('templateResult');
		console.log(repo);

		if (repo.loading) {
			return repo.text;
		}

		var markup = "<div class='media'>" +
			'<img class="align-self-center mr-3" src="' + repo.image.fullUrlSized + '" />' +
			"<div class='media-body'>" +
			"<h6 class='media-heading'>" + repo.title + "</h6>";

		if (repo.description) {
			markup += "<small>" + repo.description + "</small>";
		}

		markup += "</div></div>";

		return markup;
	},
	// отображение результатов в поле select
	templateSelection: function formatRepoSelection(repo) {

		console.log('templateSelection');
		console.log(repo);

		if (!repo.id) {
			return repo.text;
		}

		var markup = "<div class='media'>" +
			'<img class="align-self-center mr-3" src="' + repo.image.fullUrlSized + '" />' +
			"<div class='media-body'>" +
			"<h6 class='media-heading'>" + repo.title + "</h6>";

		markup += "</div></div>";

		return markup;
	},
	createTag: function (params) {
		return undefined;
	}
});