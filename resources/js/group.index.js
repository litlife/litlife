import item from "./components/user_group/item";

$('.groups').find('.item').each(function () {
	item($(this));
});