import item from "./book/item";

export default function books_list(parent) {

	this.parent = parent;

	let self = this;

	this.init = function () {

		let books = self.parent;

		self.list = books.find(".list");
		self.form = books.find("form");

		// обозначение пагинации
		self.defineInfinityPagination();

		self.form.formChange({
			timeout: 500,
			onShow: function () {

				$(this).ajaxSubmit({
					beforeSubmit: function showRequest(formData, jqForm, options) {

						self.list.addClass("loading-cap");

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

						self.list.removeClass("loading-cap");

						self.list.html(responseText);

						self.defineInfinityPagination();

						self.on_list_ready();
					},
					error: function (jqXHR, error, type, $form) {
						if (jqXHR.status == 401) location.reload();
					}
				});
			}
		});

		// событие срабытывает, когда список элементов готов
		self.on_list_ready();

		self.genres();
		self.and_genres();
		self.exclude_genres();
		self.awards();
		self.keywords();
		self.language();
		self.original_language();
		self.formats();
		self.order();

		self.bindSaveFields();
	};

	this.onLoadUrl = function (url) {

		self.list.addClass("loading-cap");

		window.history.pushState("", "", url);

		$.ajax({
			url: url, data: {'ajax': true}
		}).done(function (data) {
			self.list.removeClass("loading-cap");
			self.list.html(data);

			self.on_list_ready();

			$('html, body').animate({
				scrollTop: self.list.offset().top - 80
			}, 100);

		}).fail(function () {
			if (jqXHR.status == 401) location.reload();
		});
	};

	this.defineInfinityPagination = function () {

		//list.find('.pagination').hide();
		/*
				if (list.find(".pagination [rel=next]:last").length) {

					let url  = list.find(".pagination [rel=next]").first().attr("href");

					if (url) {

						list.show();

						list.infinityScroll({
							path: function (self) {
								var url = self.find(".pagination").last().find("[rel=next]").attr('href');
								return url;
							},
							minDistanceToStartLoad: 800,
							onAppend: on_list_ready,
							onFail: function (event, self, jqXHR) {
								if (jqXHR.status == 401) location.reload();
							}
						});
					}
				}
				*/
		console.log(self.list);

		self.list.off('click', '.pagination a').on('click', '.pagination a', function (e) {
			e.preventDefault();

			self.list.addClass("loading-cap");

			let url = $(this).attr('href');

			self.onLoadUrl(url);
		});

		self.list.off('click', '.view a').on('click', '.view a', function (e) {
			e.preventDefault();

			let url = $(this).attr('href');

			self.onLoadUrl(url);
		});
	};

	this.order = function () {
		self.form.find(".order").select2({width: '100%', placeholder: $(this).data('placeholder')});
	};

	this.language = function () {
		self.form.find(".language").select2({width: '100%', placeholder: $(this).data('placeholder')});
	};

	this.original_language = function () {
		self.form.find(".original_lang").select2({width: '100%', placeholder: $(this).data('placeholder')});
	};

	this.formats = function () {
		self.form.find(".formats").select2({width: '100%', placeholder: $(this).data('placeholder')});
	};

	this.genres = function () {

		let $btn_open_modal = $('#selected_genres_button').first();
		let $modal = $('#selected_genres_modal').first();
		let $select = $('.genres');
		let $container = $modal.find('.container-fluid').first();

		self.selectGenre($select, $modal, $container);
	};

	this.and_genres = function () {

		let $btn_open_modal = $('#and_selected_genres_button').first();
		let $modal = $('#and_selected_genres_modal').first();
		let $select = $('.and_genres');
		let $container = $modal.find('.container-fluid').first();

		self.selectGenre($select, $modal, $container);
	};

	this.exclude_genres = function () {

		let $btn_open_modal = $('#excluded_genres_button').first();
		let $modal = $('#excluded_genres_modal').first();
		let $select = $('.exclude-genres');
		let $container = $modal.find('.container-fluid').first();

		self.selectGenre($select, $modal, $container);
	};

	this.keywords = function () {

		self.form.find(".keywords").select2({
			width: '100%',
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
			minimumInputLength: 2,
			// отображение в выпадающем меню
			templateResult: function formatRepo(repo) {

				console.log('templateResult');

				if (repo.loading) return repo.text;

				var markup = "";

				markup += "<div >";
				markup += repo.text;
				markup += " </div>";

				return markup;
			},
			// отображение результатов в поле select
			templateSelection: function formatRepoSelection(repo) {

				console.log('templateSelection');

				if (repo.keyword) {
					return repo.text;
				} else {
					return repo.text;
				}

			},
			createTag: function (params) {
				return undefined;
			}

		});

	};

	this.on_list_ready = function () {

		let self = this;

		window.paginationScrollToActive();

		self.list.find('.book').each(function () {

			var i = item($(this));

		});
	};

	this.bindSaveFields = function () {

		console.log('bindSaveFields');

		self.form.find('.save').each(function () {

			let $input = $(this);

			console.log($input.attr('type'));

			if ($input.is("select")) {
				$input.bind('change', function () {
					self.saveInputValue($input);
				});
			} else if ($input.attr('type') === 'checkbox') {
				$input.bind('change', function () {
					self.saveInputValue($input);
				});
			}
		});
	};

	this.saveInputValue = function ($input) {

		console.log('saveInputValue change');

		let $name = $input.attr('name');
		let $value = $input.val();

		$.ajax({
			url: '/books/search/settings/store',
			data: {
				'name': $name,
				'value': $value
			}
		}).done(function (data) {
			$input.addClass('is-valid');

		}).fail(function () {
			$input.addClass('is-invalid');
		});
	};

	this.awards = function () {

		self.form.find(".award").select2({
			placeholder: $(this).data('placeholder'),
			width: '100%',
			allowClear: true,
			ajax: {
				url: "/awards",
				dataType: 'json',
				delay: 100,
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

					var arr = [];

					for (var i = 0; i < data.data.length; i++) {
						arr[i] = data.data[i];
						arr[i]['id'] = arr[i]['title'];
					}

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
			minimumInputLength: 0,
			templateResult: function formatRepo(repo) {

				console.log('templateResult');
				console.log(repo);

				if (repo.loading) return repo.text;

				var markup = "";

				markup += "<div >";
				markup += repo.title;
				markup += "</div>";

				return markup;
			},
			templateSelection: function formatRepoSelection(repo) {
				return repo.title || repo.text;
			}
		});
	};

	this.selectGenre = function ($select, $modal, $container) {

		$select.select2({
			width: '100%',
			placeholder: $(this).data('placeholder'),
			ajax: {
				url: "/genres/search?limit=100",
				dataType: 'json',
				delay: 100,
				placeholder: $(this).data('placeholder'),
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
				//console.log(repo);

				if (repo.loading) return repo.text;

				var markup = "";

				markup += "<div>";
				markup += repo.name;
				markup += "</div>";

				return markup;
			},
			templateSelection: function formatRepoSelection(repo) {
				return repo.name || repo.text;
			}
		});

		$modal.on('show.bs.modal', function (event) {

			console.log($modal.data('loaded'));

			$container.addClass("loading-cap");

			$.ajax({
				url: '/genres/select_list'
			}).done(function (data) {

				$modal.data('loaded', 'true');
				$container.removeClass("loading-cap");
				$container.html(data);

				on_modal_select_genres_open();

			}).fail(function () {
				if (jqXHR.status == 401) location.reload();
			});
		});

		function on_modal_select_genres_open() {

			console.log('on_genres_open');

			$select.val().forEach(function ($number) {
				//console.log($number);
				$container.find('[data-type=genre][data-id=' + $number + ']').prop('checked', true);
			});

			$container.find("[data-type=genre_group]").unbind('click').click(function () {

				if ($container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]:checked").length) {
					$container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]").each(function (item) {
						$(this).prop('checked', false);
					});
				} else {
					$container.find("[data-type=genre][data-parent-id=" + $(this).data('id') + "]").each(function (item) {
						$(this).prop('checked', true);
					});
				}
			});
		}

		$modal.on('hidden.bs.modal', function (event) {

			console.log('hidden');

			$select.html('');

			$container.find('[data-type=genre]').each(function () {

				if ($(this).is(':checked')) {
					var id = $(this).data('id');

					$select.append('<option value="' + id + '" selected="selected">' + $container.find('[for=' + $(this).attr('id') + ']').text() + '</option>')
				}
			});

			$select.trigger('change');
		});
	};
}








