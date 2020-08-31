import item from "./components/comment/item";

$('.list').find(".item").each(function () {

	item($(this));
});