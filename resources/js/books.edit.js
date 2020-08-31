import select2_sortable from "./plugins/select2_sortable";


$(".author-select").each(function () {

	let author_select = $(this).select2({
		width: '100%',
		tags: true,
		placeholder: $(this).data('placeholder'),
		ajax: {
			url: "/authors/search",
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
		minimumInputLength: 1,
		// отображение в выпадающем меню
		templateResult: function formatRepo(repo) {

			console.log('templateResult');
			console.log(repo);

			if (repo.loading) return repo.text;

			var markup = "";

			if (repo.newTag) {
				markup += repo.text + ' - Добавить нового автора';
			} else {
				markup += "<div >";
				markup += repo.fullName;
				markup += " ID: " + repo.id;
				markup += " </div>";
			}

			return markup;
		},
		// отображение результатов в поле select
		templateSelection: function formatRepoSelection(repo) {

			console.log('templateSelection');
			console.log(repo);

			if (repo.id.match(/[0-9]+/i)) {
				var s = '<a href="/authors/' + repo.id + '" target="_blank">';

				if (repo.fullName)
					s += repo.fullName;

				if (repo.text)
					s += repo.text;

				s += '</a> ID: ' + repo.id;

				s += ' <i class=\"move fas fa-arrows-alt text-black-50\"></i> ';

				return s;
			} else {
				return repo.text;
			}
		},
		createTag: function (params) {
			return undefined;
		}
	});

	select2_sortable(author_select);
});

/*
$.getJSON( "/genres/all_for_select2", function( data ) {

    $(".genres").select2({
        multiple: true,
        data: data,
        createTag: function (params) {
            return undefined;
        }
    });
});
*/

let genres = $(".genres").first();

genres.select2({
	width: '100%',
	tags: true,
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

			console.log('processResults');
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

		if (repo.loading) return repo.text;

		var markup = "";

		markup += "<div >";
		markup += repo.group.name;
		markup += ' <i class="fas fa-angle-right"></i> ';
		markup += repo.name;
		markup += " ID: " + repo.id;
		markup += "</div>";

		return markup;
	},
	// отображение результатов в поле select
	templateSelection: function formatRepoSelection(repo) {

		console.log('templateSelection');
		console.log(repo);

		if (repo.id.match(/[0-9]+/i)) {
			var s = '<a href="/books?genre=' + repo.id + '" target="_blank">';

			s += (repo.text || repo.name);

			s += '</a> ID: ' + repo.id;

			s += ' <i class=\"move fas fa-arrows-alt text-black-50\"></i> ';

			return s;
		} else {
			return repo.text;
		}
	},
	createTag: function (params) {
		return undefined;
	}
});

select2_sortable(genres);


var sequences_list = $('.sequences-list');

function list_updated() {

	sequences_list.find('.item').each(function () {
		var item = $(this);

		item.find('.delete').unbind().click(function () {

			item.remove();
		});
	});

}

list_updated();

var sequence_select = $('.sequence-select');

if (sequence_select.length) {
	var select = sequence_select.find('select');

	select.select2({
		width: '100%',
		tags: true,
		placeholder: $(this).data('placeholder'),
		ajax: {
			url: "/sequences/search",
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
		minimumInputLength: 1,
		// отображение в выпадающем меню
		templateResult: function formatRepo(repo) {

			console.log('templateResult');
			console.log(repo);

			if (repo.loading) return repo.text;

			var markup = "";

			if (repo.newTag) {
				markup += repo.text;
			} else {
				markup += "<div >";
				markup += repo.name;
				markup += " ID: " + repo.id;
				markup += "</div>";
			}

			return markup;
		},
		// отображение результатов в поле select
		templateSelection: function formatRepoSelection(repo) {

			console.log('templateSelection');
			console.log(repo);

			if (repo.id.match(/[0-9]+/i)) {
				var s = '<a href="/sequences/' + repo.id + '" target="_blank">';

				if (repo.name)
					s += repo.name;

				if (repo.text)
					s += repo.text;

				s += '</a> ID: ' + repo.id;

				return s;
			} else {
				return repo.text;
			}
		},
		createTag: function (params) {
			return undefined;
		}
	});

	sequence_select.find('.add').click(function () {

		var sequence_id = select.val();

		if (sequence_id) {
			$.ajax({
				method: 'GET',
				url: '/sequences/' + sequence_id + '/book_edit',
				dataType: 'html'
			}).done(function (data) {
				sequences_list.append(data);
				select.val('').trigger("change");
				list_updated();
			});
		}
	});
}

sequences_list.sortable({
	handle: '.move',
	itemSelector: '.item',
	group: 'sequences-list',
	pullPlaceholder: true,
	onDrop: function ($item, container, _super) {

		var $clonedItem = $('<div/>').css({height: 0});
		$item.before($clonedItem);
		$clonedItem.animate({'height': $item.height()});

		$item.animate($clonedItem.position(), function () {
			$clonedItem.detach();
			_super($item, container);
		});
	}
});


var publishers = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	prefetch: '/storage/typeahead/book/publishers.json'
	/*,
	remote: {
		url: '/books_publishers/%QUERY.json',
		wildcard: '%QUERY'
	}
	*/
});

$('#pi_pub').typeahead(null, {
	name: 'best-pictures',
	display: 'value',
	hint: true,
	highlight: true,
	minLength: 2,
	source: publishers
});

var cities = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	prefetch: '/storage/typeahead/book/cities.json'
	/*,
	remote: {
		url: '/books_publish_city/%QUERY.json',
		wildcard: '%QUERY'
	}
	*/
});

$('#pi_city').typeahead(null, {
	name: 'best-pictures',
	display: 'value',
	hint: true,
	highlight: true,
	minLength: 2,
	source: cities
});


var rightholders = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	prefetch: '/storage/typeahead/book/rightholders.json'
	/*,
	remote: {
		url: '/books_publishers/%QUERY.json',
		wildcard: '%QUERY'
	}
	*/
});

$('#rightholder').typeahead(null, {
	name: 'best-pictures',
	display: 'value',
	hint: true,
	highlight: true,
	minLength: 2,
	source: rightholders
});

$("#ti_lb").select2({width: '100%'});

$("#ti_olb").select2({width: '100%'});


var $btn_select_genres = $('#selected_genres_button');
var $modal_select_genres = $('#selected_genres_modal');
var $modal_select_genres_container = $modal_select_genres.find('.container-fluid');
var $select_genres = $('.genres');

$modal_select_genres.on('show.bs.modal', function (event) {

	console.log($modal_select_genres.data('loaded'));

	if (!$modal_select_genres.data('loaded')) {
		$modal_select_genres_container.addClass("loading-cap");

		$.ajax({
			url: '/genres/select_list'
		}).done(function (data) {

			$modal_select_genres.data('loaded', 'true');
			$modal_select_genres_container.removeClass("loading-cap");
			$modal_select_genres_container.html(data);

			on_genres_open();

		}).fail(function () {
			if (jqXHR.status == 401) location.reload();
		});
	} else {
		on_genres_open();
	}
});

function on_genres_open() {

	console.log('on_genres_open');

	$select_genres.val().forEach(function ($number) {
		//console.log($number);
		$modal_select_genres_container.find('[data-type=genre][data-id=' + $number + ']').prop('checked', true);
	});

	$modal_select_genres_container.find("[data-type=genre_group]").unbind('click').click(function () {

		if ($modal_select_genres_container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]:checked").length) {
			$modal_select_genres_container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]").each(function (item) {
				$(this).prop('checked', false);
			});
		} else {
			$modal_select_genres_container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]").each(function (item) {
				$(this).prop('checked', true);
			});
		}
	});
}

$modal_select_genres.on('hidden.bs.modal', function (event) {

	console.log('hidden');

	$select_genres.html('');

	$modal_select_genres_container.find('[data-type=genre]').each(function () {

		if ($(this).is(':checked')) {
			var id = $(this).data('id');

			$select_genres.append('<option value="' + id + '" selected="selected">' + $modal_select_genres_container.find('[for=' + $(this).attr('id') + ']').text() + '</option>')
		}
	});

	$select_genres.trigger('change');
});


let keywords = $("#keywords").first();

keywords.select2({
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

	},
	createTag: function (params) {
		return undefined;
	}
});










