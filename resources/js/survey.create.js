import autosize from "autosize";

$('textarea.autogrow').each(function () {
	autosize($(this));
});