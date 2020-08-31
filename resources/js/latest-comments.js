import item from "./components/comment/item";

$(".item:first").siblings('.item').addBack().each(function () {

	item($(this));
});