import copy_protection from "./components/copy_protection";
import PageStyleModal from "./components/page_style_modal";
import BookChaptersListGoTo from "./components/book/chapters_list_go_to";

const instance = new BookChaptersListGoTo();
instance.modal = $('#chaptersList');
instance.init();

document.addEventListener('DOMContentLoaded', function () {

	$('.noselect').each(function () {
		copy_protection($(this));
	});

	let instance = new PageStyleModal();
	instance.button = $('.change_read_style').first();
	instance.init();
});