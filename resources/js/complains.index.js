import ComplainItem from "./components/complain/item";

$('.complain').each(function () {
	new ComplainItem().init($(this));
});