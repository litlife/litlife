import item from "./components/post/item";

$('.list').find(".item").each(function () {

	item($(this));
});