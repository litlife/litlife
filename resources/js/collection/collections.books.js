import books_list from "../components/books_list";

var object = new books_list($(".books-search-container").first());

object.on_list_ready = function () {

	var self = this;

	window.paginationScrollToActive();

	self.list.find('[data-id]').each(function () {
		let book = $(this);

		book.find('.detach').unbind("click").on('click', function (event) {
			event.preventDefault();

			let url = $(this).attr('href');

			$.ajax({
				url: url
			}).done(function (data) {

				book.addClass("loading-cap");

			}).fail(function () {

			});
		});
	});
};

object.init();