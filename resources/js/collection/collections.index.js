import collection from "../components/collection/item";

var object = new CollectionsList();
object.form = $('.collection-form').first();
object.list = $('.list').first();
object.init();

function CollectionsList() {

	let self = this;

	this.init = function () {

		self.pagination();

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

						self.pagination();

						self.on_list_ready();
					},
					error: function (jqXHR, error, type, $form) {
						if (jqXHR.status == 401) location.reload();
					}
				});
			}
		});

		self.on_list_ready();
	};

	this.pagination = function () {

		console.log(self.list);

		self.list.off('click', '.pagination a').on('click', '.pagination a', function (e) {
			e.preventDefault();

			self.list.addClass("loading-cap");

			let url = $(this).attr('href');

			self.load_url(url);
		});

		self.list.off('click', '.view a').on('click', '.view a', function (e) {
			e.preventDefault();

			let url = $(this).attr('href');

			self.load_url(url);
		});
	};

	this.load_url = function (url) {

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

	this.on_list_ready = function () {

		window.paginationScrollToActive();

		self.list.find(".collection").each(function () {
			let object = new collection();
			object.init($(this));
		});
	};
}