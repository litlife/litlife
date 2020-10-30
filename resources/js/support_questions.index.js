import SupportQuestion from "./components/support_question/item";

$('.support_question').each(function () {
	new SupportQuestion().init($(this));
});