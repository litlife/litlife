import item from "./components/award/item";

$(".awards").find('.item').each(function () {
	item($(this));
});