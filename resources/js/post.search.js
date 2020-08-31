import item from "./components/post/item";

$(".item:first").siblings('.item').addBack().each(function () {

	item($(this));
});