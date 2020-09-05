import copy_protection from "./components/copy_protection";
import PageStyleModal from "./components/page_style_modal";
import BookChaptersListGoTo from "./components/book/chapters_list_go_to";

const instance = new BookChaptersListGoTo();
instance.modal = $('#chaptersList');
instance.init();

document.addEventListener('DOMContentLoaded', function () {

	let text = $('.book_text');

	text.find('a[href][data-section-id][data-type=note]').each(function () {

		let link = $(this);
		let section_id = link.data('section-id');
		let url = link.attr('href');

		link.unbind('click').on('click', function (e) {

			e.preventDefault();

			let dialog = bootbox.alert({
				size: "large",
				message: '<div class="text-center"><h1><i class="fas fa-spinner fa-spin"></i></h1></div>'
			});

			dialog.init(function () {
				$.ajax({
					method: "GET",
					url: url
				}).done(function (msg) {
					dialog.find('.bootbox-body').html(msg);
				});
			});
		});
	});

	$('.noselect').each(function () {
		copy_protection($(this));
	});

	let instance = new PageStyleModal();
	instance.button = $('.change_read_style').first();
	instance.init();
});
