import users_list from "./components/users_list";
import topics_list from "./components/topic/list";
import comments_list from "./components/comments_list";
import Favorite from "./components/user_library_button";
import Like from "./components/Like";
import shareButton from "./components/share_button";
import ScrollBooster from 'scrollbooster';

new AuthorsShow().init();

export default function AuthorsShow() {

	let self = this;

	this.init = function () {

		self.container = $(".author");

		if (self.container.length) {

			self.books_tab_content = self.container.find('#books');
			self.read_status_button = self.container.find('.read-status').first();
			self.btn_share = self.container.find('button.share');
			self.tabs = $('#author_tab');
			self.biographyContainer = self.container.find('#biography').first();
			self.favoriteButton = $(".user_library").first();
			self.likeButton = self.container.find('.like').first();
			self.loaderHtml = '<div class="text-center py-5 px-2"><h1 class="fas fa-spinner fa-spin"></h1></div>';

			new shareButton().init(self.btn_share);
			new Like().init(self.likeButton);

			new Favorite().init(self.favoriteButton, "/authors/" + window.sharedData.author_id + "/toggle_my_library");

			self.read_status_button.unbind('change').bind('change', function () {

				var button = $(this);

				$.ajax({
					method: "GET",
					url: '/authors/' + window.sharedData.author_id + '/read_status/' + button.val()
				}).done(function (msg) {

				}).fail(function () {
					button.val("0");
				});

			});

			self.photo();
			self.biography();
			self.onBooksTabLoadComplete();

			self.tabs.find('a[href="#books"]').unbind('click').bind('click', self.onBooksTabClick);
			self.tabs.find('a[href="#comments"]').unbind('click').bind('click', self.onCommentsTabClick);
			self.tabs.find('a[href="#forum"]').unbind('click').bind('click', self.onForumTabClick);
			self.tabs.find('a[href="#votes"]').unbind('click').bind('click', self.onVotesTabClick);
		}
	};

	this.photo = function () {
		self.container.find('.photo').click(function () {

			let url = $(this).data('fullsize-photo-url');

			let photo_big = $('.photo_big');

			photo_big.find('img').attr('src', url);
			photo_big.modal('show');
		});
	};

	this.biography = function () {

		self.biographyContainer.htmlExpand({
			expand_button: self.container.find('.expand-biography').first(),
			compress_button: self.container.find('.compress-biography').first(),
			onExpand: function () {
				//$(window).scrollTo(biography, 50, {offset: -60});
			},
			onCompress: function () {
				$(window).scrollTo(self.biographyContainer);
			}
		});
	};

	this.onBooksTabClick = function (e) {

		e.preventDefault();

		$(this).tab('show');

		var panel = self.container.find("#books");

		if (!panel.find(".table").length) {
			panel.append(self.loaderHtml);
			panel.load("/authors/" + window.sharedData.author_id + "/books?with_panel=true", self.onBooksTabLoadComplete);
		}
	};

	this.onCommentsTabClick = function (e) {

		e.preventDefault();

		$(this).tab('show');

		var panel = self.container.find($(this).attr("href"));

		if (!panel.find(".comments-search-container").length) {
			panel.append(self.loaderHtml);
			panel.load("/authors/" + window.sharedData.author_id + "/comments?with_panel=true", function () {
				comments_list(panel.find(".comments-search-container"));
			});
		}
	};

	this.onVotesTabClick = function (e) {

		e.preventDefault();

		$(this).tab('show');

		var panel = self.container.find($(this).attr("href"));

		if (!panel.find(".users-search-container").length) {
			panel.append(self.loaderHtml);
			panel.load("/authors/" + window.sharedData.author_id + "/books_votes?with_panel=true", function () {
				users_list(panel.find(".users-search-container"));
			});
		}
	};

	this.onForumTabClick = function (e) {

		e.preventDefault();

		$(this).tab('show');

		var panel = self.container.find($(this).attr("href"));

		if (!panel.find(".forum-container").length) {
			panel.append(self.loaderHtml);
			panel.load("/authors/" + window.sharedData.author_id + "/forum", function () {
				topics_list(panel.find(".forum-container"));
			});
		}
	};

	this.onBooksTabLoadComplete = function () {

		self.books_tab_content.find(".books_container").each(function () {
			let container = $(this);
			let table = container.find('.table');
			let table_wrapper = table.parent().first();
			let search_input = container.find('input.search').first();

			table
				.tablesorter({
					widgets: ["filter"],
					widgetOptions: {
						filter_external: search_input,
						filter_defaultFilter: {1: '~{query}'},
						filter_columnFilters: false
					}
				})
				.bind('tablesorter-ready', function (e) {

					if (!window.isTouchDevice()) {
						let sb = new ScrollBooster({
							viewport: table_wrapper.get(0), // required
							content: table.get(0), // scrollable element
							mode: 'x', // scroll only in horizontal dimension
							bounce: false,
							textSelection: true,
							onUpdate: (data) => {
								// your scroll logic goes here
								table.get(0).style.transform = `translateX(${-data.position.x}px)`
							}
						});

						let table_wrapper_width = table_wrapper.width();
						let table_width = table.width();

						if (table_width > table_wrapper_width) {
							table.css('cursor', 'move');
						}
					}
				});
		});
	};
}





