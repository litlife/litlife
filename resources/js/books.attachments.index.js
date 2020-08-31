import attachment from "./components/attachment/item"

$('.attachments').find('.item').each(function () {

	attachment($(this));
});

