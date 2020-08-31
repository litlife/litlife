import item from "./components/user_note/item";

$('.user-notes').find('.item').each(function () {
	item($(this));
});