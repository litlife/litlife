import item from "./components/blog/item";

$('.blog-posts').find(".item:first").siblings('.item').addBack().each(function () {
	item($(this));
});




