import SupportRequest from "./components/support_request/item";

$('.support_request').each(function () {
	new SupportRequest().init($(this));
});