import comments_list from "./components/comments_list";
import Favorite from "./components/user_library_button";
import Like from "./components/Like";
import ScrollBooster from 'scrollbooster';

new SequenceShow().init();

export default function SequenceShow() {

	let self = this;

	this.init = function () {

		self.container = $(".sequence");

		if (self.container.length) {

			self.id = window.sharedData.sequence_id;

			self.likeButton = self.container.find('.like').first();
			self.favoriteButton = $(".user_library").first();
			self.description = self.container.find('#description');
			self.expandBiography = self.container.find('.expand-biography').first();
			self.compressBiography = self.container.find('.compress-biography').first();
			self.tabs = self.container.find('#sequenceTab');
			self.loaderHtml = '<div class="text-center py-5 px-2"><h1 class="fas fa-spinner fa-spin"></h1></div>';

			if (self.description.length > 0) {
				self.description.htmlExpand({
					maxHeight: 100,
					expand_button: self.expandBiography,
					compress_button: self.compressBiography,
					onExpand: function () {
						//$(window).scrollTo(annotation, 50, {offset: -60});
					},
					onCompress: function () {
						$(window).scrollTo(self.description);
					}
				});
			}

			new Like().init(self.likeButton);

			new Favorite().init(self.favoriteButton, "/sequences/" + self.id + "/toggle_my_library");

			self.booksTabContent = self.container.find('#books').first();
			self.commentsTabContent = self.container.find('#comments').first();

			self.table();

			self.tabs.find('a[href="#books"]').unbind('click').bind('click', self.onBooksTabClick);

			self.tabs.find('a[href="#comments"]').unbind('click').bind('click', self.onCommentsTabClick);
		}
	};

	this.onBooksTabClick = function (e) {

		e.preventDefault();

		//self.booksTabContent.tab('show');

		if (!self.booksTabContent.find(".table").length) {
			self.booksTabContent.load("/sequences/" + self.id + "/books?with_panel=true", function () {
				self.table();
			});
		}
	};

	this.onCommentsTabClick = function (e) {

		e.preventDefault();

		$(this).tab('show');

		var panel = self.container.find($(this).attr("href"));

		if (!panel.find(".comments-search-container").length) {
			panel.append(self.loaderHtml);
			panel.load("/sequences/" + self.id + "/comments?with_panel=true", function () {
				comments_list(panel.find(".comments-search-container"));
			});
		}
	};

	this.table = function () {

		let table = self.booksTabContent.find('.table');
		let table_wrapper = table.parent().first();
		let search_input = self.booksTabContent.find('input.search').first();

		if (table.length > 0) {
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
		}
	};
}




