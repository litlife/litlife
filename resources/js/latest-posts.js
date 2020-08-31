import post from "./components/post/item";
import topic from "./components/topic/item";

$('.topics').find('.item').each(function () {

	topic($(this));
});

$('.posts').find(".item:first").siblings('.item').addBack().each(function () {

	post($(this));
});